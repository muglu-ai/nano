<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormFieldConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_type',
        'version',
        'field_name',
        'field_label',
        'is_required',
        'validation_rules',
        'field_order',
        'field_group',
        'is_active',
        'is_current_version',
        'created_by',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'is_current_version' => 'boolean',
        'validation_rules' => 'array',
        'field_order' => 'integer',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeCurrentVersion($query)
    {
        return $query->where('is_current_version', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByFormType($query, $formType)
    {
        return $query->where('form_type', $formType);
    }

    public function scopeByVersion($query, $version)
    {
        return $query->where('version', $version);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('field_order')->orderBy('field_name');
    }

    // Helper methods
    public function getValidationRulesArray()
    {
        if ($this->validation_rules && is_array($this->validation_rules)) {
            return $this->validation_rules;
        }
        return [];
    }
}
