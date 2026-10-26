<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Livewire\Component;
use Livewire\Attributes\Rule;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Form extends Component
{
    use WithFileUploads;

    public $product;
    public $productId;

    #[Rule('required|min:3')]
    public $name;

    #[Rule('nullable|unique:products,sku')]
    public $sku;

    public $description;

    #[Rule('required')]
    public $price_cost;

    #[Rule('required')]
    public $price_sale;

    #[Rule('required|numeric|min:0')]
    public $stock = 0;

    public $branch_stocks = [];

    #[Rule('required|numeric|min:0')]
    public $min_stock = 5;

    #[Rule('nullable|exists:categories,id')]
    public $category_id;

    #[Rule('nullable|exists:suppliers,id')]
    public $supplier_id;

    #[Rule('required')]
    public $unit_type = 'unit';

    public $manage_stock = true;
    public $is_active = true;
    public $discount_percentage = 0;
    
    public $product_image;
    public $product_image_path;

    // Variations
    public $has_variations = false;
    public $variations = [];
    
    // Matrix generation properties
    public $selectedSizes = [];
    public $selectedColors = [];
    
    public $availableSizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', 'Unico'];
    public $availableColors = ['Blanco', 'Negro', 'Gris', 'Azul', 'Rojo', 'Verde', 'Amarillo', 'Naranja', 'Rosa', 'Beige', 'Marrón'];

    // Decants & Carnicería
    public $is_decant = false;
    public $parent_id = null;
    public $fraction_multiplier = 1.000;
    public $is_raw_material = false;

    // Restaurant
    public $production_zone = 'none';

    public function updatedIsRawMaterial($value)
    {
        if ($value) {
            $this->is_decant = false;
            $this->parent_id = null;
            $this->unit_type = 'kg';
        }
    }

    public function updatedIsDecant($value)
    {
        if ($value) {
            $this->is_raw_material = false;
        }
    }

    public function mount(Product $product = null)
    {
        if ($product && $product->exists) {
            $this->product = $product;
            $this->productId = $product->id;
            $this->name = $product->name;
            $this->sku = $product->sku;
            $this->description = $product->description;
            $this->price_cost = $product->formatPyg($product->price_cost);
            $this->price_sale = $product->formatPyg($product->price_sale);
            $this->stock = $product->stock;
            $this->min_stock = $product->min_stock;
            $this->category_id = $product->category_id;
            $this->supplier_id = $product->supplier_id;
            $this->unit_type = $product->unit_type;
            $this->manage_stock = $product->manage_stock;
            $this->is_active = $product->is_active;
            $this->discount_percentage = $product->discount_percentage;
            $this->product_image_path = $product->image_path;
            $this->has_variations = $product->has_variations;
            $this->is_raw_material = (bool)($product->is_raw_material ?? false);
            $this->is_decant = !is_null($product->parent_id);
            $this->parent_id = $product->parent_id;
            $this->fraction_multiplier = $product->fraction_multiplier ?? 1.000;
            $this->production_zone = $product->production_zone ?? 'none';
            
            $this->branch_stocks = $product->branchStocks->pluck('stock', 'branch_id')->toArray();

            $this->variations = $product->variations->map(fn($v) => [
                'id' => $v->id,
                'name' => $v->name,
                'sku' => $v->sku,
                'price_cost' => $v->formatPyg($v->price_cost),
                'price_sale' => $v->formatPyg($v->price_sale),
                'stock' => $v->stock,
                'branch_stocks' => $v->branchStocks->pluck('stock', 'branch_id')->toArray()
            ])->toArray();
        } else {
            $this->productId = null;
        }
    }

    public function render()
    {
        $discountProductEnabled = \App\Models\StoreSetting::where('key', 'discount_product_enabled')->value('value') === '1';
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $suppliers = Supplier::where('active', true)->orderBy('name')->get();
        $units = \App\Models\Unit::where('is_active', true)->orderBy('name')->get();
        $branches = \App\Models\Branch::where('is_active', true)->orderBy('name')->get();
        
        $potentialParents = [];
        if (hasModule('meat_deboning')) {
            $potentialParents = Product::where('is_active', true)
                                       ->where('is_raw_material', true)
                                       ->where('id', '!=', $this->productId)
                                       ->orderBy('name')
                                       ->get();
        } elseif (hasModule('decants')) {
            $potentialParents = Product::where('is_active', true)
                                       ->whereNull('parent_id')
                                       ->where('id', '!=', $this->productId)
                                       ->orderBy('name')
                                       ->get();
        }

        return view('livewire.products.form', [
            'categories' => $categories,
            'suppliers' => $suppliers,
            'units' => $units,
            'branches' => $branches,
            'discountProductEnabled' => $discountProductEnabled,
            'potentialParents' => $potentialParents,
        ])->layout('layouts.app');
    }

    public function store()
    {
        $id = $this->productId ?: null;

        $rules = [
            'name'       => 'required|min:3',
            'sku'        => 'nullable|unique:products,sku' . ($id ? ',' . $id : ''),
            'description'=> 'nullable|string',
            'price_cost' => 'required',
            'price_sale' => 'required',
            'min_stock'  => $this->manage_stock ? 'required|numeric|min:0' : 'nullable',
            'unit_type'  => 'required',
            'discount_percentage' => 'integer|min:0|max:100',
            'product_image' => 'nullable|image|max:1024',
            'production_zone' => 'nullable|in:none,kitchen,bar',
        ];

        $this->validate($rules);

        if ($this->product_image) {
            if ($this->product_image_path) {
                Storage::disk('public')->delete($this->product_image_path);
            }
            $this->product_image_path = $this->product_image->store('products', 'public');
        }

        if (empty($this->sku)) {
            do {
                $this->sku = str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);
            } while (Product::where('sku', $this->sku)->exists());
        }

        $totalStock = 0;
        if ($this->manage_stock && !$this->has_variations) {
            foreach ($this->branch_stocks as $bStock) {
                $totalStock += (float) ($bStock ?? 0);
            }
        }

        $product = Product::updateOrCreate(['id' => $id], [
            'name'       => $this->name,
            'sku'        => $this->sku,
            'description'=> $this->description,
            'price_cost' => $this->price_cost,
            'price_sale' => $this->price_sale,
            'discount_percentage' => (int) $this->discount_percentage,
            'stock'      => $this->is_decant ? 0 : $totalStock, // Decants don't have their own stock
            'min_stock'  => ($this->manage_stock && !$this->is_decant) ? $this->min_stock : 0,
            'category_id' => $this->category_id ?: null,
            'supplier_id' => $this->supplier_id ?: null,
            'unit_type'  => $this->unit_type,
            'manage_stock' => $this->is_decant ? false : $this->manage_stock,
            'has_variations' => $this->has_variations,
            'is_active'  => $this->is_active,
            'image_path' => $this->product_image_path,
            'is_raw_material' => (bool) $this->is_raw_material,
            'parent_id'  => $this->is_decant ? $this->parent_id : null,
            'fraction_multiplier' => $this->is_decant ? (float)$this->fraction_multiplier : 1.000,
            'production_zone' => $this->production_zone,
        ]);

        if ($this->manage_stock && !$this->has_variations && !$this->is_decant) {
            foreach ($this->branch_stocks as $branchId => $bStock) {
                $product->branchStocks()->updateOrCreate(
                    ['branch_id' => $branchId],
                    ['stock' => (float) ($bStock ?? 0)]
                );
            }
        } else {
            $product->branchStocks()->delete();
        }

        if ($this->has_variations) {
            $existingIds = [];
            foreach ($this->variations as $var) {
                $varTotalStock = 0;
                $varBranchStocks = $var['branch_stocks'] ?? [];
                if ($this->manage_stock) {
                    foreach ($varBranchStocks as $bStock) {
                        $varTotalStock += (float) ($bStock ?? 0);
                    }
                }

                $v = $product->variations()->updateOrCreate(
                    ['id' => $var['id'] ?? null],
                    [
                        'name' => $var['name'],
                        'sku' => $var['sku'] ?: $product->sku . '-' . \Illuminate\Support\Str::random(4),
                        'price_cost' => $var['price_cost'],
                        'price_sale' => $var['price_sale'],
                        'stock' => $varTotalStock,
                        'is_active' => true
                    ]
                );
                
                if ($this->manage_stock) {
                    foreach ($varBranchStocks as $branchId => $bStock) {
                        $v->branchStocks()->updateOrCreate(
                            ['branch_id' => $branchId],
                            ['stock' => (float) ($bStock ?? 0)]
                        );
                    }
                } else {
                    $v->branchStocks()->delete();
                }

                $existingIds[] = $v->id;
            }
            $product->variations()->whereNotIn('id', $existingIds)->delete();
        } else {
            $product->variations()->delete();
        }

        session()->flash('message', $id ? 'Producto actualizado correctamente.' : 'Producto creado correctamente.');

        return redirect()->route('products.index');
    }

    public function addVariation()
    {
        $this->variations[] = [
            'id' => null,
            'name' => '',
            'sku' => '',
            'price_cost' => $this->price_cost,
            'price_sale' => $this->price_sale,
            'stock' => 0,
            'branch_stocks' => []
        ];
    }

    public function removeVariation($index)
    {
        unset($this->variations[$index]);
        $this->variations = array_values($this->variations);
    }
    
    public function toggleSize($size)
    {
        if (in_array($size, $this->selectedSizes)) {
            $this->selectedSizes = array_diff($this->selectedSizes, [$size]);
        } else {
            $this->selectedSizes[] = $size;
        }
    }

    public function toggleColor($color)
    {
        if (in_array($color, $this->selectedColors)) {
            $this->selectedColors = array_diff($this->selectedColors, [$color]);
        } else {
            $this->selectedColors[] = $color;
        }
    }

    public function generateVariations()
    {
        if (empty($this->selectedSizes) && empty($this->selectedColors)) {
            return;
        }

        $combinations = [];

        if (!empty($this->selectedColors) && !empty($this->selectedSizes)) {
            foreach ($this->selectedColors as $color) {
                foreach ($this->selectedSizes as $size) {
                    $combinations[] = $color . ' - ' . $size;
                }
            }
        } elseif (!empty($this->selectedColors)) {
            foreach ($this->selectedColors as $color) {
                $combinations[] = $color;
            }
        } elseif (!empty($this->selectedSizes)) {
            foreach ($this->selectedSizes as $size) {
                $combinations[] = $size;
            }
        }

        // Add to existing variations avoiding duplicates by name
        $existingNames = array_column($this->variations, 'name');
        
        foreach ($combinations as $comboName) {
            if (!in_array($comboName, $existingNames)) {
                $this->variations[] = [
                    'id' => null,
                    'name' => $comboName,
                    'sku' => '',
                    'price_cost' => $this->price_cost,
                    'price_sale' => $this->price_sale,
                    'stock' => 0,
                    'branch_stocks' => []
                ];
            }
        }
        
        $this->selectedSizes = [];
        $this->selectedColors = [];
        
        session()->flash('variation_message', 'Variantes generadas correctamente.');
    }
}
