<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Application;
use App\Models\Ticket\TicketType;
use App\Helpers\TicketAllocationHelper;

class ExhibitionParticipant extends Model
{
    //
    use HasFactory;

    protected $fillable = ['application_id', 'stall_manning_count', 'complimentary_delegate_count', 'coExhibitor_id', 'ticketAllocation', ];

    protected $casts = [
        'ticketAllocation' => 'array',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function stallManning()
    {
        return $this->hasMany(StallManning::class);
    }

    public function complimentaryDelegates()
    {
        return $this->hasMany(ComplimentaryDelegate::class);
    }

    /**
     * Get ticket allocations with ticket names and counts (using new TicketType model)
     * 
     * @return array
     */
    public function tickets()
    {
        $tickets = [];
        $allocations = $this->ticketAllocation ?? [];
        
        foreach ($allocations as $ticketTypeId => $count) {
            if ($count > 0) {
                $ticketType = TicketType::find($ticketTypeId);
                if ($ticketType) {
                    $tickets[] = [
                        'id' => $ticketType->id,
                        'name' => $ticketType->name,
                        'slug' => $ticketType->slug,
                        'count' => $count,
                    ];
                }
            }
        }
        return $tickets;
    }

    /**
     * Get TicketType models from ticketAllocation JSON
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function ticketTypes()
    {
        $allocations = $this->ticketAllocation ?? [];
        $ticketTypeIds = array_keys($allocations);
        
        if (empty($ticketTypeIds)) {
            return collect([]);
        }

        return TicketType::with(['category', 'subcategory', 'event'])
            ->whereIn('id', $ticketTypeIds)
            ->get();
    }

    /**
     * Get structured ticket allocation details
     * 
     * @return array
     */
    public function getTicketAllocationDetails(): array
    {
        $allocations = $this->ticketAllocation ?? [];
        $details = [];

        foreach ($allocations as $ticketTypeId => $count) {
            if ($count > 0) {
                $ticketType = TicketType::with(['category', 'subcategory', 'event'])->find($ticketTypeId);
                
                if ($ticketType) {
                    $details[$ticketTypeId] = [
                        'id' => $ticketType->id,
                        'name' => $ticketType->name,
                        'slug' => $ticketType->slug,
                        'count' => $count,
                        'ticket_type' => $ticketType,
                        'category' => $ticketType->category,
                        'subcategory' => $ticketType->subcategory,
                    ];
                }
            }
        }

        return $details;
    }

    /**
     * Get available slots for invitations
     * 
     * @param int|null $ticketTypeId Optional specific ticket type
     * @return array
     */
    public function getAvailableSlotsForInvitation(?int $ticketTypeId = null): array
    {
        if ($this->application_id) {
            return TicketAllocationHelper::getAvailableSlots($this->application_id, $ticketTypeId);
        }
        
        return [];
    }

    /**
     * Calculate stall manning count from ticketAllocation JSON
     * 
     * @return int
     */
    public function getStallManningCount(): int
    {
        $allocations = $this->ticketAllocation ?? [];
        
        // TODO: Add configuration to identify which ticket types are for stall manning
        // For now, sum all allocations as placeholder
        return array_sum($allocations);
    }

    /**
     * Calculate complimentary delegate count from ticketAllocation JSON
     * 
     * @return int
     */
    public function getComplimentaryDelegateCount(): int
    {
        $allocations = $this->ticketAllocation ?? [];
        
        // TODO: Add configuration to identify which ticket types are for delegates
        // For now, sum all allocations as placeholder
        return array_sum($allocations);
    }

    /**
     * Get counts from allocation (delegates exhibitor panel)
     * 
     * @return array
     */
    public function getCountsFromAllocation(): array
    {
        if ($this->application_id) {
            return TicketAllocationHelper::getCountsFromAllocation($this->application_id, $this->coExhibitor_id);
        }
        
        return [
            'total_allocated' => 0,
            'by_ticket_type' => [],
            'stall_manning_count' => 0,
            'complimentary_delegate_count' => 0,
        ];
    }

    //now even coexhibitors can be allocated the bagde count 
    public function coExhibitor()
    {
        return $this->belongsTo(CoExhibitor::class, 'coExhibitor_id');
    }
}
