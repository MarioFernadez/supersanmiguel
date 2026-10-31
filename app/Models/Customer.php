<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuidV7;

class Customer extends Model
{
    use HasFactory, HasUuidV7, SoftDeletes, \App\Traits\Searchable;

    protected $fillable = [
        'name',
        'ruc',
        'email',
        'phone',
        'address',
        'is_active',
        'credit_limit',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'credit_limit' => 'float',
    ];

    public function transactions()
    {
        return $this->hasMany(CustomerTransaction::class);
    }

    public function getBalanceAttribute()
    {
        return $this->transactions()->sum(\DB::raw("CASE WHEN type = 'debit' THEN amount ELSE -amount END"));
    }
}
