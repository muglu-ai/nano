<?php

namespace App\Helpers;

use App\Models\ExhibitionParticipant;
use App\Models\Ticket\TicketType;
use App\Models\TicketAllocationRule;
use App\Models\ComplimentaryDelegate;
use App\Models\StallManning;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TicketAllocationHelper
{
    /**
     * Allocate ticket types to an exhibitor/startup
     * 
     * @param int $applicationId
     * @param array $allocations Array of ['ticket_type_id' => count]
     * @param int|null $coExhibitorId Optional co-exhibitor ID
     * @return ExhibitionParticipant
     * @throws \Exception
     */
    public static function allocate(
        int $applicationId,
        array $allocations,
        ?int $coExhibitorId = null
    ): ExhibitionParticipant {
        // Validate ticket types exist and are active
        $validation = self::validateTicketTypes(array_keys($allocations));
        if (!$validation['valid']) {
            throw new \Exception('Invalid ticket types: ' . implode(', ', $validation['errors']));
        }

        // Filter out zero counts
        $allocations = array_filter($allocations, function($count) {
            return $count > 0;
        });

        if (empty($allocations)) {
            throw new \Exception('No valid allocations provided');
        }

        // Create or update exhibition_participants record
        $exhibitionParticipant = ExhibitionParticipant::updateOrCreate(
            [
                'application_id' => $coExhibitorId ? null : $applicationId,
                'coExhibitor_id' => $coExhibitorId,
            ],
            [
                'ticketAllocation' => json_encode($allocations),
            ]
        );

        Log::info('Ticket allocation created/updated', [
            'application_id' => $applicationId,
            'co_exhibitor_id' => $coExhibitorId,
            'allocations' => $allocations,
            'exhibition_participant_id' => $exhibitionParticipant->id,
        ]);

        return $exhibitionParticipant;
    }

    /**
     * Validate ticket type IDs
     * 
     * @param array $ticketTypeIds
     * @param int|null $eventId Optional event ID to validate against
     * @return array ['valid' => bool, 'errors' => array]
     */
    public static function validateTicketTypes(array $ticketTypeIds, ?int $eventId = null): array
    {
        $errors = [];
        $validIds = [];

        foreach ($ticketTypeIds as $ticketTypeId) {
            $ticketType = TicketType::find($ticketTypeId);
            
            if (!$ticketType) {
                $errors[] = "Ticket type ID {$ticketTypeId} does not exist";
                continue;
            }

            if (!$ticketType->is_active) {
                $errors[] = "Ticket type ID {$ticketTypeId} ({$ticketType->name}) is not active";
                continue;
            }

            if ($eventId && $ticketType->event_id != $eventId) {
                $errors[] = "Ticket type ID {$ticketTypeId} does not belong to event {$eventId}";
                continue;
            }

            $validIds[] = $ticketTypeId;
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'valid_ids' => $validIds,
        ];
    }

    /**
     * Get current allocation for an application/co-exhibitor
     * 
     * @param int $applicationId
     * @param int|null $coExhibitorId
     * @return array
     */
    public static function getAllocation(int $applicationId, ?int $coExhibitorId = null): array
    {
        $exhibitionParticipant = ExhibitionParticipant::where('application_id', $coExhibitorId ? null : $applicationId)
            ->where('coExhibitor_id', $coExhibitorId)
            ->first();

        if (!$exhibitionParticipant || empty($exhibitionParticipant->ticketAllocation)) {
            return [];
        }

        $allocations = json_decode($exhibitionParticipant->ticketAllocation, true) ?? [];
        $result = [];

        foreach ($allocations as $ticketTypeId => $count) {
            $ticketType = TicketType::with(['category', 'subcategory', 'event'])->find($ticketTypeId);
            
            if ($ticketType) {
                $result[$ticketTypeId] = [
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

        return $result;
    }

    /**
     * Clear allocation for an application/co-exhibitor
     * 
     * @param int $applicationId
     * @param int|null $coExhibitorId
     * @return bool
     */
    public static function clearAllocation(int $applicationId, ?int $coExhibitorId = null): bool
    {
        $exhibitionParticipant = ExhibitionParticipant::where('application_id', $coExhibitorId ? null : $applicationId)
            ->where('coExhibitor_id', $coExhibitorId)
            ->first();

        if ($exhibitionParticipant) {
            $exhibitionParticipant->update([
                'ticketAllocation' => null,
            ]);

            Log::info('Ticket allocation cleared', [
                'application_id' => $applicationId,
                'co_exhibitor_id' => $coExhibitorId,
                'exhibition_participant_id' => $exhibitionParticipant->id,
            ]);

            return true;
        }

        return false;
    }

    /**
     * Get counts from allocation JSON
     * 
     * @param int $applicationId
     * @param int|null $coExhibitorId
     * @return array
     */
    public static function getCountsFromAllocation(int $applicationId, ?int $coExhibitorId = null): array
    {
        $exhibitionParticipant = ExhibitionParticipant::where('application_id', $coExhibitorId ? null : $applicationId)
            ->where('coExhibitor_id', $coExhibitorId)
            ->first();

        if (!$exhibitionParticipant || empty($exhibitionParticipant->ticketAllocation)) {
            return [
                'total_allocated' => 0,
                'by_ticket_type' => [],
                'stall_manning_count' => 0,
                'complimentary_delegate_count' => 0,
            ];
        }

        $allocations = json_decode($exhibitionParticipant->ticketAllocation, true) ?? [];
        $byTicketType = [];
        $totalAllocated = 0;

        foreach ($allocations as $ticketTypeId => $allocatedCount) {
            $ticketType = TicketType::find($ticketTypeId);
            
            // Count used (non-cancelled invitations)
            $usedCount = self::getUsedCount($exhibitionParticipant->id, $ticketTypeId);
            
            // Count cancelled invitations
            $cancelledCount = self::getCancelledCount($exhibitionParticipant->id, $ticketTypeId);
            
            $available = $allocatedCount - $usedCount;

            $byTicketType[$ticketTypeId] = [
                'allocated' => $allocatedCount,
                'used' => $usedCount,
                'cancelled' => $cancelledCount,
                'available' => $available,
                'ticket_type_name' => $ticketType ? $ticketType->name : "Unknown (ID: {$ticketTypeId})",
            ];

            $totalAllocated += $allocatedCount;
        }

        // Calculate stall_manning_count and complimentary_delegate_count
        // These would be based on specific ticket types - for now, sum all
        // TODO: Add configuration to identify which ticket types are for stall manning vs delegates
        $stallManningCount = $totalAllocated; // Placeholder - should be calculated from specific ticket types
        $complimentaryDelegateCount = $totalAllocated; // Placeholder - should be calculated from specific ticket types

        return [
            'total_allocated' => $totalAllocated,
            'by_ticket_type' => $byTicketType,
            'stall_manning_count' => $stallManningCount,
            'complimentary_delegate_count' => $complimentaryDelegateCount,
        ];
    }

    /**
     * Get used count for a ticket type (non-cancelled invitations)
     * 
     * @param int $exhibitionParticipantId
     * @param int $ticketTypeId
     * @return int
     */
    private static function getUsedCount(int $exhibitionParticipantId, int $ticketTypeId): int
    {
        $count = 0;

        // Count from complimentary_delegates
        $count += ComplimentaryDelegate::where('exhibition_participant_id', $exhibitionParticipantId)
            ->where('ticketType', $ticketTypeId)
            ->where('status', '!=', 'cancelled')
            ->count();

        // Count from stall_manning
        $count += StallManning::where('exhibition_participant_id', $exhibitionParticipantId)
            ->where('ticketType', $ticketTypeId)
            ->where('status', '!=', 'cancelled')
            ->count();

        return $count;
    }

    /**
     * Get cancelled count for a ticket type
     * 
     * @param int $exhibitionParticipantId
     * @param int $ticketTypeId
     * @return int
     */
    private static function getCancelledCount(int $exhibitionParticipantId, int $ticketTypeId): int
    {
        $count = 0;

        // Count from complimentary_delegates
        $count += ComplimentaryDelegate::where('exhibition_participant_id', $exhibitionParticipantId)
            ->where('ticketType', $ticketTypeId)
            ->where('status', 'cancelled')
            ->count();

        // Count from stall_manning
        $count += StallManning::where('exhibition_participant_id', $exhibitionParticipantId)
            ->where('ticketType', $ticketTypeId)
            ->where('status', 'cancelled')
            ->count();

        return $count;
    }

    /**
     * Get ticket type details
     * 
     * @param array $ticketTypeIds
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getTicketTypeDetails(array $ticketTypeIds)
    {
        return TicketType::with(['category', 'subcategory', 'event'])
            ->whereIn('id', $ticketTypeIds)
            ->get();
    }

    /**
     * Get available slots for invitations
     * 
     * @param int $applicationId
     * @param int|null $ticketTypeId Optional specific ticket type
     * @return array
     */
    public static function getAvailableSlots(int $applicationId, ?int $ticketTypeId = null): array
    {
        $exhibitionParticipant = ExhibitionParticipant::where('application_id', $applicationId)
            ->first();

        if (!$exhibitionParticipant || empty($exhibitionParticipant->ticketAllocation)) {
            return [];
        }

        $allocations = json_decode($exhibitionParticipant->ticketAllocation, true) ?? [];
        $result = [];

        foreach ($allocations as $typeId => $allocatedCount) {
            if ($ticketTypeId && $typeId != $ticketTypeId) {
                continue;
            }

            $usedCount = self::getUsedCount($exhibitionParticipant->id, $typeId);
            $available = max(0, $allocatedCount - $usedCount);

            $result[$typeId] = [
                'allocated' => $allocatedCount,
                'used' => $usedCount,
                'available' => $available,
            ];
        }

        return $ticketTypeId ? ($result[$ticketTypeId] ?? ['allocated' => 0, 'used' => 0, 'available' => 0]) : $result;
    }

    /**
     * Validate if exhibitor can invite for a ticket type
     * 
     * @param int $applicationId
     * @param int $ticketTypeId
     * @param int $requestedCount
     * @return array
     */
    public static function canInvite(
        int $applicationId,
        int $ticketTypeId,
        int $requestedCount = 1
    ): array {
        $exhibitionParticipant = ExhibitionParticipant::where('application_id', $applicationId)
            ->first();

        if (!$exhibitionParticipant || empty($exhibitionParticipant->ticketAllocation)) {
            return [
                'can_invite' => false,
                'available' => 0,
                'allocated' => 0,
                'used' => 0,
                'message' => 'No ticket allocation found for this application',
            ];
        }

        $allocations = json_decode($exhibitionParticipant->ticketAllocation, true) ?? [];
        $allocated = $allocations[$ticketTypeId] ?? 0;
        $used = self::getUsedCount($exhibitionParticipant->id, $ticketTypeId);
        $available = max(0, $allocated - $used);

        $canInvite = $available >= $requestedCount;

        return [
            'can_invite' => $canInvite,
            'available' => $available,
            'allocated' => $allocated,
            'used' => $used,
            'message' => $canInvite 
                ? "You can invite {$requestedCount} registrant(s). {$available} slot(s) available."
                : "Insufficient slots. You have {$available} available slot(s) but need {$requestedCount}.",
        ];
    }

    /**
     * Cancel an invitation
     * 
     * @param int $invitationId
     * @param string $type 'complimentary_delegate' or 'stall_manning'
     * @param int|null $cancelledBy User ID who cancelled
     * @return bool
     */
    public static function cancelInvitation(
        int $invitationId,
        string $type = 'complimentary_delegate',
        ?int $cancelledBy = null
    ): bool {
        try {
            if ($type === 'complimentary_delegate') {
                $invitation = ComplimentaryDelegate::find($invitationId);
            } elseif ($type === 'stall_manning') {
                $invitation = StallManning::find($invitationId);
            } else {
                return false;
            }

            if (!$invitation) {
                return false;
            }

            if ($invitation->status === 'cancelled') {
                return true; // Already cancelled
            }

            $invitation->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancelled_by' => $cancelledBy,
            ]);

            Log::info('Invitation cancelled', [
                'invitation_id' => $invitationId,
                'type' => $type,
                'cancelled_by' => $cancelledBy,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to cancel invitation', [
                'invitation_id' => $invitationId,
                'type' => $type,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get invitation status
     * 
     * @param int $invitationId
     * @param string $type 'complimentary_delegate' or 'stall_manning'
     * @return string|null
     */
    public static function getInvitationStatus(int $invitationId, string $type): ?string
    {
        if ($type === 'complimentary_delegate') {
            $invitation = ComplimentaryDelegate::find($invitationId);
        } elseif ($type === 'stall_manning') {
            $invitation = StallManning::find($invitationId);
        } else {
            return null;
        }

        return $invitation ? $invitation->status : null;
    }

    /**
     * Get invitation usage statistics
     * 
     * @param int $applicationId
     * @return array
     */
    public static function getInvitationUsageStats(int $applicationId): array
    {
        $exhibitionParticipant = ExhibitionParticipant::where('application_id', $applicationId)
            ->first();

        if (!$exhibitionParticipant || empty($exhibitionParticipant->ticketAllocation)) {
            return [];
        }

        $allocations = json_decode($exhibitionParticipant->ticketAllocation, true) ?? [];
        $stats = [];

        foreach ($allocations as $ticketTypeId => $allocatedCount) {
            $ticketType = TicketType::find($ticketTypeId);

            // Get counts by status
            $pendingCount = self::getCountByStatus($exhibitionParticipant->id, $ticketTypeId, 'pending');
            $acceptedCount = self::getCountByStatus($exhibitionParticipant->id, $ticketTypeId, 'accepted');
            $cancelledCount = self::getCountByStatus($exhibitionParticipant->id, $ticketTypeId, 'cancelled');
            $usedCount = $pendingCount + $acceptedCount;
            $availableCount = max(0, $allocatedCount - $usedCount);

            $stats[$ticketTypeId] = [
                'ticket_type_id' => $ticketTypeId,
                'ticket_type_name' => $ticketType ? $ticketType->name : "Unknown (ID: {$ticketTypeId})",
                'allocated' => $allocatedCount,
                'used' => $usedCount,
                'pending' => $pendingCount,
                'accepted' => $acceptedCount,
                'cancelled' => $cancelledCount,
                'available' => $availableCount,
                'breakdown' => [
                    'delegate' => self::getCountByTypeAndStatus($exhibitionParticipant->id, $ticketTypeId, 'complimentary_delegate'),
                    'exhibitor' => self::getCountByTypeAndStatus($exhibitionParticipant->id, $ticketTypeId, 'stall_manning'),
                ],
            ];
        }

        return $stats;
    }

    /**
     * Get count by status for a ticket type
     * 
     * @param int $exhibitionParticipantId
     * @param int $ticketTypeId
     * @param string $status
     * @return int
     */
    private static function getCountByStatus(int $exhibitionParticipantId, int $ticketTypeId, string $status): int
    {
        $count = 0;

        $count += ComplimentaryDelegate::where('exhibition_participant_id', $exhibitionParticipantId)
            ->where('ticketType', $ticketTypeId)
            ->where('status', $status)
            ->count();

        $count += StallManning::where('exhibition_participant_id', $exhibitionParticipantId)
            ->where('ticketType', $ticketTypeId)
            ->where('status', $status)
            ->count();

        return $count;
    }

    /**
     * Get count by invitation type and status
     * 
     * @param int $exhibitionParticipantId
     * @param int $ticketTypeId
     * @param string $invitationType 'complimentary_delegate' or 'stall_manning'
     * @return array
     */
    private static function getCountByTypeAndStatus(int $exhibitionParticipantId, int $ticketTypeId, string $invitationType): array
    {
        if ($invitationType === 'complimentary_delegate') {
            $model = ComplimentaryDelegate::where('exhibition_participant_id', $exhibitionParticipantId)
                ->where('ticketType', $ticketTypeId);
        } else {
            $model = StallManning::where('exhibition_participant_id', $exhibitionParticipantId)
                ->where('ticketType', $ticketTypeId);
        }

        return [
            'pending' => (clone $model)->where('status', 'pending')->count(),
            'accepted' => (clone $model)->where('status', 'accepted')->count(),
            'cancelled' => (clone $model)->where('status', 'cancelled')->count(),
            'total' => $model->count(),
        ];
    }

    /**
     * Calculate allocation from booth area using rules
     * Handles numeric sqm and special strings (POD, Booth / POD, Startup Booth, etc.)
     * Checks database rules first, then falls back to config
     *
     * @param float|string|null $boothArea
     * @param int|null $eventId
     * @param string|null $applicationType
     * @return array ['ticket_allocations' => array]
     */
    public static function calculateAllocationFromBoothArea(
        float|string|null $boothArea,
        ?int $eventId = null,
        ?string $applicationType = null
    ): array {
        // Handle string values: special booth types (POD, Booth / POD, Startup Booth) or "4 SQM"
        if (is_string($boothArea)) {
            $trimmed = trim($boothArea);
            
            // First, check database rules for special booth type
            // Rules with event_id/application_type null apply to all events/types (see TicketAllocationRule scopes)
            $dbRule = TicketAllocationRule::active()
                ->forEvent($eventId)
                ->forApplicationType($applicationType)
                ->where('booth_type', $trimmed)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->first();
            
            if ($dbRule && !empty($dbRule->ticket_allocations)) {
                Log::info('Ticket allocation: matched DB rule for booth type', [
                    'booth_type' => $trimmed,
                    'rule_id' => $dbRule->id,
                    'event_id' => $eventId,
                    'application_type' => $applicationType,
                ]);
                return ['ticket_allocations' => $dbRule->ticket_allocations];
            }
            
            // If no database rule, check config file
            $special = self::getSpecialBoothTypeAllocation($trimmed, $eventId);
            if (!empty($special)) {
                return ['ticket_allocations' => $special];
            }
            
            // Try to extract numeric value if format is like "4 SQM"
            if (preg_match('/(\d+)\s*sqm/i', $boothArea, $matches)) {
                $boothArea = (float) $matches[1];
            } else {
                // Fallback: use default rule (middle of smallest range)
                $boothArea = 6;
            }
        }

        if (!is_numeric($boothArea) || $boothArea <= 0) {
            return ['ticket_allocations' => []];
        }

        $boothArea = (float) $boothArea;

        // Find matching numeric range rule
        $query = TicketAllocationRule::active()
            ->forEvent($eventId)
            ->forApplicationType($applicationType)
            ->whereNull('booth_type') // Only numeric range rules
            ->where('booth_area_min', '<=', $boothArea)
            ->where('booth_area_max', '>=', $boothArea)
            ->orderBy('sort_order')
            ->orderBy('id');

        $rule = $query->first();

        if (!$rule) {
            Log::warning('No allocation rule found for booth area', [
                'booth_area' => $boothArea,
                'event_id' => $eventId,
                'application_type' => $applicationType,
            ]);
            return ['ticket_allocations' => []];
        }

        return [
            'ticket_allocations' => $rule->ticket_allocations ?? [],
        ];
    }

    /**
     * Resolve allocation for special booth types (POD, Booth / POD, Startup Booth, etc.)
     * Returns [ticket_type_id => count] or empty if not a special type or resolution fails.
     *
     * @param string $boothType Raw value (interested_sqm / allocated_sqm)
     * @param int|null $eventId
     * @return array
     */
    public static function getSpecialBoothTypeAllocation(string $boothType, ?int $eventId = null): array
    {
        $config = config('ticket_allocation.special_booth_types', []);
        $normalized = trim($boothType);
        if ($normalized === '') {
            return [];
        }
        // Match config key case-insensitively
        foreach ($config as $key => $counts) {
            if (strcasecmp(trim((string) $key), $normalized) === 0) {
                // Explicit ticket_type_ids override
                if (isset($counts['ticket_type_ids']) && is_array($counts['ticket_type_ids'])) {
                    $out = [];
                    foreach ($counts['ticket_type_ids'] as $id => $count) {
                        if ($count > 0) {
                            $out[(int) $id] = (int) $count;
                        }
                    }
                    return $out;
                }
                // Role-based: exhibitor => 1, standard_pass => 1
                return self::resolveRoleAllocationToTicketTypeIds($counts, $eventId);
            }
        }
        return [];
    }

    /**
     * Resolve role-based allocation (exhibitor, standard_pass) to [ticket_type_id => count].
     *
     * @param array $roleCounts e.g. ['exhibitor' => 1, 'standard_pass' => 1]
     * @param int|null $eventId
     * @return array
     */
    protected static function resolveRoleAllocationToTicketTypeIds(array $roleCounts, ?int $eventId = null): array
    {
        $roles = config('ticket_allocation.ticket_type_roles', []);
        $query = TicketType::where('is_active', true);
        if ($eventId) {
            $query->where('event_id', $eventId);
        }
        $ticketTypes = $query->get();
        $result = [];

        foreach ($roleCounts as $role => $count) {
            if (!is_numeric($count) || (int) $count <= 0) {
                continue;
            }
            $count = (int) $count;
            $def = $roles[$role] ?? null;
            if (!$def) {
                continue;
            }
            $nameContains = $def['name_contains'] ?? [];
            $slugContains = $def['slug_contains'] ?? [];
            $found = $ticketTypes->first(function ($tt) use ($nameContains, $slugContains) {
                $name = strtolower($tt->name ?? '');
                $slug = strtolower($tt->slug ?? '');
                foreach ($nameContains as $sub) {
                    if (str_contains($name, strtolower($sub))) {
                        return true;
                    }
                }
                foreach ($slugContains as $sub) {
                    if (str_contains($slug, strtolower($sub))) {
                        return true;
                    }
                }
                return false;
            });
            if ($found) {
                $result[$found->id] = ($result[$found->id] ?? 0) + $count;
            } else {
                Log::warning('Ticket allocation: no ticket type found for role', [
                    'role' => $role,
                    'event_id' => $eventId,
                ]);
            }
        }

        return $result;
    }

    /**
     * Auto-allocate tickets after payment
     * 
     * @param int $applicationId
     * @param float|string|null $boothArea
     * @param int|null $eventId
     * @param string|null $applicationType
     * @return ExhibitionParticipant|null
     */
    public static function autoAllocateAfterPayment(
        int $applicationId,
        float|string|null $boothArea = null,
        ?int $eventId = null,
        ?string $applicationType = null
    ): ?ExhibitionParticipant {
        try {
            // Get booth area from application if not provided
            if ($boothArea === null) {
                $application = \App\Models\Application::find($applicationId);
                if ($application) {
                    $boothArea = $application->allocated_sqm ?? $application->interested_sqm ?? null;
                    $eventId = $eventId ?? $application->event_id ?? null;
                    $applicationType = $applicationType ?? $application->application_type ?? null;
                }
            }

            if ($boothArea === null) {
                Log::warning('Cannot auto-allocate: booth area not available', [
                    'application_id' => $applicationId,
                ]);
                return null;
            }

            // Calculate allocation from rules
            $allocationData = self::calculateAllocationFromBoothArea($boothArea, $eventId, $applicationType);
            $ticketAllocations = $allocationData['ticket_allocations'] ?? [];

            if (empty($ticketAllocations)) {
                Log::info('No ticket allocations from rules', [
                    'application_id' => $applicationId,
                    'booth_area' => $boothArea,
                ]);
                return null;
            }

            // Allocate using the helper
            return self::allocate($applicationId, $ticketAllocations);

        } catch (\Exception $e) {
            Log::error('Failed to auto-allocate tickets after payment', [
                'application_id' => $applicationId,
                'booth_area' => $boothArea,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Get exhibitor-only ticket types
     * 
     * @param int|null $eventId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getExhibitorTicketTypes(?int $eventId = null)
    {
        $query = TicketType::whereHas('category', function ($q) {
            $q->where('is_exhibitor_only', true);
        })->where('is_active', true);

        if ($eventId) {
            $query->where('event_id', $eventId);
        }

        return $query->with(['category', 'subcategory', 'event'])->get();
    }
}
