<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuidV7;
use App\Traits\HasPygFormatting;

class Sale extends Model
{
    use HasFactory, HasUuidV7, HasPygFormatting;

    protected $fillable = [
        'customer_id',
        'user_id',
        'seller_id',
        'total',
        'subtotal_before_discount',
        'discount_amount',
        'tax_10',
        'tax_5',
        'tax_exempt',
        'status',
        'payment_condition',
        'branch_id',
        'restaurant_table_id',
        'waiter_id',
        'sifen_cdc',
        'sifen_numero_factura',
        'sifen_estado',
        'sifen_qr_url',
        'sifen_xml',
        'sifen_respuesta',
        'sifen_tipo_documento',
    ];

    /**
     * Get the fields that should be formatted as PYG.
     * 
     * @return array
     */
    protected function getPygFields(): array
    {
        return ['total', 'subtotal_before_discount', 'discount_amount', 'tax_10', 'tax_5', 'tax_exempt'];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class)->withTrashed();
    }

    public function restaurantTable()
    {
        return $this->belongsTo(RestaurantTable::class);
    }

    public function waiter()
    {
        return $this->belongsTo(Waiter::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Virtual Invoice Number Accessor
     * Generates a sequential number based on ID sorting (since we use Ordered UUIDs)
     */
    public function getInvoiceNumberAttribute()
    {
        // Count how many sales exist with an ID 'smaller' (older) than this one
        // This effectively gives us the row number / sequence
        $count = static::where('id', '<', $this->id)->count() + 1;
        
        // Return 6-digit padded string (e.g., 000123)
        return str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    public function isElectronicInvoice(): bool
    {
        return !empty($this->sifen_cdc);
    }

    public function getFormattedCdcAttribute(): ?string
    {
        return $this->sifen_cdc ? trim(chunk_split($this->sifen_cdc, 4, ' ')) : null;
    }

    public function getSifenQrSvgAttribute(): ?string
    {
        if (empty($this->sifen_qr_url)) {
            return null;
        }
        return \App\Services\SifenService::renderQrSvg($this->sifen_qr_url, 130);
    }
}
