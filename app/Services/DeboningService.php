<?php

namespace App\Services;

use App\Models\Product;
use App\Models\DeboningSession;
use App\Models\DeboningItem;
use Illuminate\Support\Facades\DB;
use Exception;

class DeboningService
{
    /**
     * Create and record a complete meat deboning session.
     *
     * @param array $data
     * @return DeboningSession
     * @throws Exception
     */
    public function createSession(array $data): DeboningSession
    {
        return DB::transaction(function () use ($data) {
            $rawMaterialId = $data['raw_material_product_id'];
            $totalStart = (float) str_replace(',', '.', (string)($data['total_weight_start'] ?? 0));
            $items = $data['items'];
            $notes = $data['notes'] ?? '';
            $userId = $data['user_id'];

            if ($totalStart <= 0) {
                throw new Exception("El peso inicial de la materia prima debe ser mayor a 0 Kg.");
            }

            $rawMaterial = Product::lockForUpdate()->findOrFail($rawMaterialId);

            $branchId = \App\Models\User::find($userId)?->branch_id ?? \App\Models\Branch::first()?->id;
            if (!$branchId) {
                throw new Exception("No hay una sucursal asignada para procesar el desposte.");
            }

            $branchStock = $rawMaterial->branchStocks()->where('branch_id', $branchId)->first();
            $availableBranchStock = $branchStock ? (float)$branchStock->stock : 0.0;

            if ($availableBranchStock < $totalStart) {
                throw new Exception("Stock insuficiente de materia prima en esta sucursal. Disponible: " . number_format($availableBranchStock, 3) . " Kg");
            }

            // Calculate total yield and waste
            $totalYield = 0.0;
            $sanitizedItems = [];
            foreach ($items as $item) {
                $rawWeight = (float) str_replace(',', '.', (string)($item['weight'] ?? 0));
                if ($rawWeight < 0) {
                    throw new Exception("El peso de un corte no puede ser negativo.");
                }
                if ($rawWeight > 0) {
                    $totalYield += $rawWeight;
                    $sanitizedItems[] = [
                        'product_id' => $item['product_id'],
                        'weight'     => $rawWeight,
                    ];
                }
            }

            if (empty($sanitizedItems)) {
                throw new Exception("Debe registrar al menos un corte con peso mayor a cero.");
            }

            $wasteWeight = max(0, $totalStart - $totalYield);

            if ($totalYield > $totalStart) {
                throw new Exception("El peso total de los cortes (" . number_format($totalYield, 3) . " Kg) no puede superar el peso inicial (" . number_format($totalStart, 3) . " Kg).");
            }

            // 1. Create Session
            $session = DeboningSession::create([
                'raw_material_product_id' => $rawMaterialId,
                'user_id'                 => $userId,
                'total_weight_start'      => $totalStart,
                'total_weight_yield'      => $totalYield,
                'waste_weight'            => $wasteWeight,
                'status'                  => 'completed',
                'notes'                   => $notes,
            ]);

            // 2. Deduct Raw Material Stock
            $rawMaterial->decrement('stock', $totalStart);
            $rawMaterial->branchStocks()->firstOrCreate(
                ['branch_id' => $branchId],
                ['stock' => 0]
            )->decrement('stock', $totalStart);

            \App\Models\InventoryMovement::create([
                'product_id' => $rawMaterial->id,
                'user_id'    => $userId,
                'type'       => 'out',
                'quantity'   => $totalStart,
                'reason'     => 'Desposte de Carne - Materia Prima (Sesión #' . $session->id . ')',
                'notes'      => 'Kilos brutos de res procesados para despiece.',
            ]);

            // 3. Create items and increment cut stock
            foreach ($sanitizedItems as $item) {
                $weight = $item['weight'];

                DeboningItem::create([
                    'deboning_session_id' => $session->id,
                    'product_id'          => $item['product_id'],
                    'weight'              => $weight,
                ]);

                $productCut = Product::find($item['product_id']);
                if ($productCut) {
                    $productCut->increment('stock', $weight);
                    $productCut->branchStocks()->firstOrCreate(
                        ['branch_id' => $branchId],
                        ['stock' => 0]
                    )->increment('stock', $weight);

                    \App\Models\InventoryMovement::create([
                        'product_id' => $productCut->id,
                        'user_id'    => $userId,
                        'type'       => 'in',
                        'quantity'   => $weight,
                        'reason'     => 'Ingreso de Corte por Desposte (Sesión #' . $session->id . ')',
                        'notes'      => 'Corte derivado obtenido del desposte de res.',
                    ]);
                }
            }

            return $session;
        });
    }
}
