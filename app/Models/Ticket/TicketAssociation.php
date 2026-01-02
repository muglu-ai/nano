<?php

namespace App\Models\Ticket;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketAssociation extends Model
{
    protected $table = 'ticket_associations';

    protected $fillable = [
        'name',
        'logo_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get portal users (admins) for this association
     */
    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'ticket_association_admins',
            'association_id',
            'portal_user_id'
        );
    }

    /**
     * Get quota allocations for this association
     */
    public function allocations(): HasMany
    {
        return $this->hasMany(TicketAssociationAllocation::class, 'association_id');
    }

    /**
     * Get shareable links for this association
     */
    public function links(): HasMany
    {
        return $this->hasMany(TicketAssociationLink::class, 'association_id');
    }
}

