@props(['rooms'])

<div class="mb-6">
    <label for="room_type_selector" class="block font-medium text-sm text-gray-700">Select Room Type</label>
    <select id="room_type_selector" {{ $attributes->merge(['class' => 'block mt-1 w-full md:w-1/2 rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50']) }}>
        <option value="">-- Please choose a room type --</option>
        @foreach ($rooms as $room)
            <option value="{{ $room->id }}">{{ $room->name }} ({{ $room->location }})</option>
        @endforeach
    </select>
</div>