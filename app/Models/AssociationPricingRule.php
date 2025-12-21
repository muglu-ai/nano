<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssociationPricingRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'association_name',
        'display_name',
        'logo_path',
        'promocode',
        'base_price',
        'special_price',
        'is_complimentary',
        'max_registrations',
        'current_registrations',
        'is_active',
        'description',
        'entitlements',
        'valid_from',
        'valid_until',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'special_price' => 'decimal:2',
        'is_complimentary' => 'boolean',
        'is_active' => 'boolean',
        'max_registrations' => 'integer',
        'current_registrations' => 'integer',
        'valid_from' => 'date',
        'valid_until' => 'date',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        $now = now();
        return $query->where(function ($q) use ($now) {
            $q->whereNull('valid_from')
              ->orWhere('valid_from', '<=', $now);
        })->where(function ($q) use ($now) {
            $q->whereNull('valid_until')
              ->orWhere('valid_until', '>=', $now);
        });
    }

    // Helper methods
    public function getEffectivePrice()
    {
        if ($this->is_complimentary) {
            return 0;
        }
        return $this->special_price ?? $this->base_price;
    }

    public function isRegistrationFull()
    {
        if (!$this->max_registrations) {
            return false;
        }
        return $this->current_registrations >= $this->max_registrations;
    }

    public function getLogoUrl()
    {
        if ($this->logo_path) {
            return asset('storage/' . $this->logo_path);
        }
        return null;
    }
}
