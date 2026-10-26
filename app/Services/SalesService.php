<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Payment;
use App\Models\CustomerTransaction;
use App\Models\WebOrder;
use App\Models\RestaurantTable;
use Illuminate\Support\Facades\DB;
use Exception;

class SalesService
{
    /**
     * Process and finalize a point of sale transaction.
     *
     * @param array $data
     * @return Sale
     * @throws Exception
     */
    public function processSale(array $data): Sale
    {
        return DB::transaction(function () use ($data) {
            $branchId = $data['branch_id'];
            $cart = $data['cart'];
            $paymentCondition = $data['payment_condition'];
            $paymentMethod = $data['payment_method'];
            $total = (float) $data['total'];

            // 1. Create Sale record
            $sale = Sale::create([
                'user_id'                  => $data['user_id'],
                'branch_id'                => $branchId,
                'seller_id'                => $data['seller_id'] ?? null,
                'customer_id'              => $data['customer_id'] ?? null,
                'subtotal_before_discount' => $data['subtotal_before_discount'] ?? $total,
                'discount_amount'          => $data['discount_amount'] ?? 0,
                'total'                    => $total,
                'tax_10'                   => (int) round($total / 11),
                'tax_5'                    => 0,
                'tax_exempt'               => 0,
                'status'                   => 'completed',
                'payment_condition'        => $paymentCondition,
                'web_order_id'             => $data['web_order_id'] ?? null,
                'restaurant_table_id'      => $data['restaurant_table_id'] ?? null,
                'waiter_id'                => $data['waiter_id'] ?? null,
            ]);

            // 2. Process Items & Deduct Stock with pessimistic locking
            foreach ($cart as $item) {
                $product = Product::lockForUpdate()->find($item['id']);
                $variation = !empty($item['variation_id']) ? ProductVariation::lockForUpdate()->find($item['variation_id']) : null;

                if (!$product) {
                    throw new Exception("El producto '{$item['name']}' ya no existe en la base de datos.");
                }

                $quantity = (float) $item['quantity'];

                if ($product->manage_stock) {
                    $stockable = $variation ?: $product;
                    $branchStock = $stockable->branchStocks()->where('branch_id', $branchId)->lockForUpdate()->first();
                    $stockToCheck = $branchStock ? $branchStock->stock : 0;

                    if ($stockToCheck < $quantity) {
                        throw new Exception("Stock insuficiente en esta sucursal para: " . $item['name']);
                    }

                    if ($branchStock) {
                        $branchStock->decrement('stock', $quantity);
                    }
                    $stockable->decrement('stock', $quantity);
                } elseif ($product->parent_id) {
                    // Materia Prima Global (Carnicería) / Decants (Fraccionamiento)
                    $parentProduct = Product::lockForUpdate()->find($product->parent_id);
                    if (!$parentProduct) {
                        throw new Exception("El producto padre de '{$item['name']}' no existe.");
                    }

                    if ($parentProduct->manage_stock) {
                        $parentBranchStock = $branchId ? $parentProduct->branchStocks()->where('branch_id', $branchId)->lockForUpdate()->first() : null;
                        $stockToCheck = $parentBranchStock ? (float)$parentBranchStock->stock : (float)$parentProduct->stock;
                        $quantityToDeduct = (float)$quantity * (float)($product->fraction_multiplier ?: 1.0);

                        if ($stockToCheck < $quantityToDeduct) {
                            $unit = $parentProduct->unit_type ?: 'Kg';
                            throw new Exception("Stock insuficiente en la materia prima '{$parentProduct->name}'. Disponible: " . number_format($stockToCheck, 2) . " {$unit}");
                        }

                        if ($parentBranchStock) {
                            $parentBranchStock->decrement('stock', $quantityToDeduct);
                        }
                        $parentProduct->decrement('stock', $quantityToDeduct);
                    }
                }

                SaleItem::create([
                    'sale_id'              => $sale->id,
                    'product_id'           => $item['id'],
                    'product_variation_id' => $item['variation_id'] ?? null,
                    'product_name'         => $item['name'],
                    'quantity'             => $quantity,
                    'price_unit'           => $item['price'],
                    'subtotal'             => $item['price'] * $quantity,
                ]);
            }

            // 3. Process Payments
            if ($paymentCondition === 'contado') {
                if ($paymentMethod === 'multiple') {
                    $rates = $data['rates'] ?? [];
                    foreach ($data['multiple_payments'] as $method => $amount) {
                        $amount = (float) $amount;
                        if ($amount > 0) {
                            $finalAmount = $amount;
                            $displayName = $method;

                            if ($method === 'efectivo_usd') {
                                $finalAmount = (int) round($amount * ($rates['usd'] ?? 1));
                                $displayName = 'Efectivo USD';
                            } elseif ($method === 'efectivo_brl') {
                                $finalAmount = (int) round($amount * ($rates['brl'] ?? 1));
                                $displayName = 'Efectivo BRL';
                            } elseif ($method === 'efectivo_ars') {
                                $finalAmount = (int) round($amount * ($rates['ars'] ?? 1));
                                $displayName = 'Efectivo ARS';
                            }

                            Payment::create([
                                'sale_id' => $sale->id,
                                'method'  => $displayName,
                                'amount'  => $finalAmount,
                            ]);
                        }
                    }
                } else {
                    $paymentData = [
                        'sale_id' => $sale->id,
                        'method'  => $paymentMethod,
                        'amount'  => $total,
                    ];

                    if ($paymentMethod === 'cheque' && !empty($data['cheque_details'])) {
                        $paymentData['bank_name']     = $data['cheque_details']['bank_name'] ?? null;
                        $paymentData['cheque_number'] = $data['cheque_details']['cheque_number'] ?? null;
                        $paymentData['due_date']      = $data['cheque_details']['due_date'] ?? null;
                    }

                    Payment::create($paymentData);
                }
            } elseif ($paymentCondition === 'credito' && !empty($sale->customer_id)) {
                CustomerTransaction::create([
                    'customer_id'    => $sale->customer_id,
                    'type'           => 'debit',
                    'amount'         => $total,
                    'reference_type' => 'sale',
                    'reference_id'   => $sale->id,
                    'description'    => 'Venta #' . $sale->id,
                ]);
            }

            // 4. Complete Web Order if applicable
            if (!empty($data['web_order_id'])) {
                WebOrder::where('id', $data['web_order_id'])->update(['status' => 'Completado']);
            }

            // 5. Restaurant cleanup: Delete pending sale, free table
            if (!empty($data['active_sale_id'])) {
                Sale::where('id', $data['active_sale_id'])->delete();
            }
            if (!empty($data['restaurant_table_id'])) {
                RestaurantTable::where('id', $data['restaurant_table_id'])->update([
                    'status'          => 'available',
                    'current_sale_id' => null,
                ]);
            }

            // 6. Process Electronic Invoicing (SIFEN v150) if enabled
            if (\App\Services\SifenService::isEnabled()) {
                $sale = \App\Services\SifenService::processSaleElectronicInvoice($sale);
            }

            return $sale;
        });
    }
}
