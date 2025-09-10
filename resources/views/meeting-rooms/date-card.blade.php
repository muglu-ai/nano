@props(['date'])

<div class="border rounded-lg p-4 bg-white">
    <h3 class="font-bold text-lg text-center mb-3 border-b pb-2">
        {{ \Carbon\Carbon::parse($date)->format('l, F jS') }}
    </h3>
    <div class="space-y-2">
        <x-meeting-rooms.time-slot :date="$date" slotType="morning" />
        <x-meeting-rooms.time-slot :date="$date" slotType="afternoon" />
    </div>
</div>