<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'contact_person',
        'tel',
        'email',
        'address',
        'vat_number',
        'opening_balance',
        'current_balance',
        'is_active',
    ];

    /**
     * Relationship: One supplier can have many purchase invoices.
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }
}
