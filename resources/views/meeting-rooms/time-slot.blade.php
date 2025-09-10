@props(['date', 'slotType'])

@php
    $title = ($slotType === 'morning') ? 'Morning Slot' : 'Afternoon Slot';
    $timeRange = ($slotType === 'morning') ? '08:30 - 12:30' : '13:30 - 17:30';
@endphp

<div class="p-3 rounded-md text-center transition-all duration-300" data-date="{{ $date }}" data-slot="{{ $slotType }}">
    <div class="font-semibold">{{ $title }}</div>
    <div class="text-sm text-gray-600">{{ $timeRange }}</div>
    <div class="availability-status mt-2 text-sm text-gray-500">
        Select a room type to see status
    </div>
</div>