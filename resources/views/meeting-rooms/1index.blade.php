@extends('meeting-rooms.layout')

@section('content')
    <div class="content-section">
        <h2 class="section-title">
            <i class="fas fa-calendar-alt text-primary"></i>
            Book a Meeting Room
        </h2>
        <!-- Step 1: Room Selection -->
        <div class="form-section">
            <div class="d-flex align-items-center mb-3">
                <div class="step-indicator me-3">1</div>
                <h3 class="h5 mb-0 fw-semibold">Select Room Type</h3>
            </div> <!-- Room Selector -->
            <div class="row">
                <div class="col-12">
                    <label class="form-label">Choose your preferred meeting room:</label>
                    <div class="room-selection row">
                        @foreach ($rooms as $room)
                            <div class="col-md-4">
                                <div class="form-check room-option"
                                    data-member-rate="{{ $room->member_rate }}"
                                    data-non-member-rate="{{ $room->non_member_rate }}">
                                    <input type="radio" class="form-check-input room-radio" id="room_{{ $room->id }}"
                                        name="room_type" value="{{ $room->id }}"
                                        data-room='@json($room)'>
                                    <label class="form-check-label d-block" for="room_{{ $room->id }}">
                                        <span class="room-title">{{ $room->room_type }}, {{ $room->location }}</span><br>
                                        <span class="ms-2 capacity-badge">
                                            <i class="fas fa-users"></i> {{ $room->capacity }} persons
                                        </span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="room-info mt-3" id="room-info" style="display: none;">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-info-circle text-primary me-2"></i>
                    <strong>Room Features</strong>
                </div>
                <div class="room-features" id="room-features">
                    <!-- Populated dynamically -->
                </div>
            </div>
        </div>

        <!-- Availability Grid (hidden by default) -->
        <div class="form-section">
            <div class="d-flex align-items-center mb-3">
                <div class="step-indicator me-3">2</div>
                <h3 class="h5 mb-0 fw-semibold">Choose an Available Time Slot</h3>
            </div>
            <div class="time-slots-grid">
                <div class="day-card">
                    @foreach ($eventDates as $date)
                        <div class="day-header text-center bg-dark text-white py-2 mb-4">
                            <i class="fas fa-calendar-alt me-2"></i>
                            {{ \Carbon\Carbon::parse($date)->format('l, F jS') }}
                        </div>
                        <div class="slot-container row g-4">
                            <div class="col-md-6">
                                <div class="time-slot-card" data-date="{{ $date }}" data-slot="morning">
                                    <div class="time-slot">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="session-info">
                                                <h5 class="session-title"><i class="fas fa-sun"></i> Morning Session</h5>
                                                <div class="time-info">
                                                    <i class="fas fa-clock text-primary"></i>
                                                    08:30 - 12:30 (4 hours)
                                                </div>
                                                <div class="price-info mt-2 d-none">
                                                    <div class="price-row">
                                                        <i class="fas fa-user-tag text-primary"></i>
                                                        <span>Member: ₹<span class="member-price">0.00</span></span>
                                                    </div>
                                                    <div class="price-row">
                                                        <i class="fas fa-users text-primary"></i>
                                                        <span>Non-Member: ₹<span class="non-member-price">0.00</span></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="availability-status text-center">
                                            <div class="spinner-border spinner-border-sm" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <span class="ms-2">Checking availability...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="time-slot-card" data-date="{{ $date }}" data-slot="afternoon">
                                    <div class="time-slot">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="session-info">
                                                <h5 class="session-title"><i class="fas fa-moon"></i> Afternoon Session</h5>
                                                <div class="time-info">
                                                    <i class="fas fa-clock text-primary"></i>
                                                    13:30 - 17:30 (4 hours)
                                                </div>
                                            </div>
                                        </div>
                                        <div class="availability-status text-center">
                                            <div class="spinner-border spinner-border-sm" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <span class="ms-2">Checking availability...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    </div>
    </div>
@endsection
<script>
    function handleRoomSelection() {
        const selectedRoomInput = document.querySelector('.room-radio:checked');
        const roomData = selectedRoomInput ? JSON.parse(selectedRoomInput.dataset.room) : null;

        const availabilityGrid = document.querySelector('.time-slots-grid');
        const roomInfo = document.getElementById('room-info');
        const featuresContainer = document.getElementById('room-features');
        const priceInfos = document.querySelectorAll('.price-info');

        if (!roomData) {
            availabilityGrid.style.display = 'none';
            roomInfo.style.display = 'none';
            priceInfos.forEach(info => info.classList.add('d-none'));
            return;
        }

        // Update price information for all time slots
        priceInfos.forEach(priceInfo => {
            const memberPrice = priceInfo.querySelector('.member-price');
            console.log(roomData);
            const nonMemberPrice = priceInfo.querySelector('.non-member-price');            // Format the prices with Indian number formatting
            const formatPrice = (price) => {
                return new Intl.NumberFormat('en-IN', {
                    maximumFractionDigits: 2,
                    minimumFractionDigits: 2
                }).format(price);
            };
            
            memberPrice.textContent = formatPrice(roomData.member_price);
            nonMemberPrice.textContent = formatPrice(roomData.non_member_price);

            priceInfo.classList.remove('d-none');
        });

        // Display room features
        roomInfo.style.display = 'block';
        featuresContainer.innerHTML = `
        <span class="feature-tag"><i class="fas fa-users me-1"></i>${roomData.capacity} Capacity</span>
        ${roomData.equipment ? `<span class="feature-tag"><i class="fas fa-plug me-1"></i>${roomData.equipment}</span>` : ''}
        <span class="feature-tag"><i class="fas fa-mug-hot me-1"></i>${roomData.fnb}</span>
        ${roomData.currency ? `<span class="feature-tag"><i class="fas fa-rupee-sign me-1"></i>₹${roomData.member_price} (Member), ₹${roomData.non_member_price} (Non-Member)</span>` : ''}
    `;

        // Show grid and fetch availability
        availabilityGrid.style.display = 'block';
        setAllSlotsToLoading();

        const eventDates = @json($eventDates);
        const selectedRooms = [roomData.id]; // for now, one room at a time
        eventDates.forEach(date => {
            fetchAvailability(selectedRooms, date);
        });
    }

    function updatePriceDisplay(timeSlot, room) {
        const priceInfo = timeSlot.querySelector('.price-info');
        const memberPrice = priceInfo.querySelector('.member-price');
        const nonMemberPrice = priceInfo.querySelector('.non-member-price');

        // Calculate prices based on room rates and duration
        const duration = 4; // 4 hours per session
        const memberRate = parseFloat(room.dataset.memberRate) * duration;
        const nonMemberRate = parseFloat(room.dataset.nonMemberRate) * duration;

        memberPrice.textContent = memberRate.toFixed(2);
        nonMemberPrice.textContent = nonMemberRate.toFixed(2);
        priceInfo.classList.remove('d-none');
    }

    document.querySelectorAll('.room-radio').forEach(radio => {
        radio.addEventListener('change', function () {
            const room = this.closest('.room-option');
            document.querySelectorAll('.time-slot').forEach(timeSlot => {
                updatePriceDisplay(timeSlot, room);
            });
        });
    });
</script>
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roomRadios = document.querySelectorAll('.room-radio');
            const availabilityGrid = document.querySelector('.time-slots-grid');

            // Hide availability grid initially
            availabilityGrid.style.display = 'none';

            // Add change event listener to all radio buttons
            roomRadios.forEach(radio => {
                radio.addEventListener('change', handleRoomSelection);
            });

            function handleRoomSelection() {
                const selectedRoomInput = document.querySelector('.room-radio:checked');
                if (!selectedRoomInput) {
                    availabilityGrid.style.display = 'none';
                    document.getElementById('room-info').style.display = 'none';
                    return;
                }

                const roomData = JSON.parse(selectedRoomInput.dataset.room);
                console.log('Selected room data:', roomData); // Debug log

                // Update room features
                const roomInfo = document.getElementById('room-info');
                const featuresContainer = document.getElementById('room-features');
                
                roomInfo.style.display = 'block';
                featuresContainer.innerHTML = `
                    <span class="feature-tag"><i class="fas fa-users me-1"></i>${roomData.capacity} Capacity</span>
                    ${roomData.equipment ? `<span class="feature-tag"><i class="fas fa-plug me-1"></i>${roomData.equipment}</span>` : ''}
                    <span class="feature-tag"><i class="fas fa-mug-hot me-1"></i>${roomData.fnb}</span>
                    <span class="feature-tag"><i class="fas fa-rupee-sign me-1"></i>₹${roomData.member_rate}/hr (Member), ₹${roomData.non_member_rate}/hr (Non-Member)</span>
                `;

                // Update price information for all time slots
                document.querySelectorAll('.time-slot').forEach(timeSlot => {
                    const priceInfo = timeSlot.querySelector('.price-info');
                    if (priceInfo) {
                        const memberPrice = priceInfo.querySelector('.member-price');
                        const nonMemberPrice = priceInfo.querySelector('.non-member-price');
                        const duration = 4; // 4 hours per session

                        const memberTotal = (parseFloat(roomData.member_rate) * duration).toFixed(2);
                        const nonMemberTotal = (parseFloat(roomData.non_member_rate) * duration).toFixed(2);

                        memberPrice.textContent = memberTotal;
                        nonMemberPrice.textContent = nonMemberTotal;
                        priceInfo.classList.remove('d-none');
                    }
                });

                // Show availability grid and check availability
                availabilityGrid.style.display = 'block';
                setAllSlotsToLoading();
                
                fetchAvailability([roomData.id], document.querySelector('.time-slot-card').dataset.date);
            }

            function setAllSlotsToLoading() {
                document.querySelectorAll('.time-slot').forEach(slot => {
                    const statusElement = slot.querySelector('.availability-status');
                    slot.className = 'time-slot'; // Reset all classes
                    statusElement.innerHTML = `
                        <div class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <span class="ms-2">Checking availability...</span>
                    `;
                });
            }

            function fetchAvailability(roomIds, date) {
                const url = `/meeting-rooms/availability?room_ids=${roomIds.join(',')}&date=${date}`;


                fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        updateSlotUI('morning', date, data.morning);
                        updateSlotUI('afternoon', date, data.afternoon);
                    })
                    .catch(error => handleApiError(error, date));
            }            function updateSlotUI(slotType, date, availability) {
                const slotElement = document.querySelector(`[data-date="${date}"][data-slot="${slotType}"]`);
                if (!slotElement) return;

                const timeSlotEl = slotElement.querySelector('.time-slot');
                const statusElement = slotElement.querySelector('.availability-status');

                // Reset only the availability-related classes
                timeSlotEl.classList.remove('available', 'booked', 'selected');

                let badgeHtml = '';
                if (availability.is_blocked) {
                    slotElement.querySelector('.time-slot').classList.add('booked');
                    badgeHtml = `
                        <span class="availability-badge blocked">
                            <i class="fas fa-ban me-2"></i>Blocked
                        </span>
                        <div class="text-muted mt-1">${availability.reason || 'Not available'}</div>
                    `;
                } else if (availability.is_available) {
                    const timeSlotEl = slotElement.querySelector('.time-slot');
                    timeSlotEl.classList.add('available');
                    badgeHtml = `
                        <span class="availability-badge available">
                            <i class="fas fa-check-circle me-2"></i>Available
                        </span>
                        <div class="text-success mt-1">${availability.available_count} rooms left</div>
                    `;
                    // Add click handler for booking
                    timeSlotEl.onclick = () => {
                        document.querySelectorAll('.time-slot').forEach(slot => slot.classList.remove(
                            'selected'));
                        timeSlotEl.classList.add('selected');
                        initiateBooking(date, slotType, availability);
                    };
                } else {
                    slotElement.querySelector('.time-slot').classList.add('booked');
                    badgeHtml = `
                        <span class="availability-badge booked">
                            <i class="fas fa-times-circle me-2"></i>Fully Booked
                        </span>
                    `;
                }
                statusElement.innerHTML = badgeHtml;
            }

            function initiateBooking(date, slotType, availability) {
                const selectedRooms = Array.from(document.querySelectorAll('.room-checkbox:checked'))
                    .map(cb => ({
                        id: cb.value,
                        name: cb.nextElementSibling.querySelector('.room-name').textContent
                    }));

                const formattedDate = new Date(date).toLocaleDateString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });

                const roomsList = selectedRooms.map(room => room.name).join('\n- ');

                if (confirm(
                        `Confirm booking for:\n\nRooms:\n- ${roomsList}\n\nDate: ${formattedDate}\nSession: ${slotType === 'morning' ? 'Morning (8:30 - 12:30)' : 'Afternoon (13:30 - 17:30)'}`
                    )) {
                    // Redirect to booking confirmation page or show booking form
                    const roomIds = selectedRooms.map(room => room.id).join(',');
                    window.location.href = `/meeting-rooms/book?room_ids=${roomIds}&date=${date}&slot=${slotType}`;
                }
            }
        });
    </script>
@endpush

@push('styles')
    <style>
        /* Room selection styles */
        .room-selection {
            margin: 20px 0;
        }

        .room-option {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
        }

        .room-option:hover {
            background-color: #f8f9fa;
        }

        .room-title {
            font-weight: 500;
            color: #212529;
        }

        /* Time slot styles */
        .time-slot {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 1.5rem;
            background-color: #fff;
            transition: all 0.3s ease;
            height: 100%;
            cursor: pointer;
        }

        .time-slot:hover {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
        }

        .time-slot.available {
            border-color: #198754;
            background-color: rgba(25, 135, 84, 0.05);
        }

        .time-slot.booked {
            border-color: #dc3545;
            background-color: rgba(220, 53, 69, 0.05);
        }

        .time-slot.selected {
            border-color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.1);
        }

        .session-title {
            font-size: 1.1rem;
            font-weight: 500;
            color: #212529;
            margin-bottom: 0.5rem;
        }

        .session-title i {
            margin-right: 0.5rem;
            color: #0d6efd;
        }

        .time-info {
            color: #6c757d;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .time-info i {
            color: #0d6efd;
        }

        .duration-badge {
            background-color: #e9ecef;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
        }

        .price-info {
            font-size: 0.9rem;
            color: #495057;
        }

        .price-info {
            margin-top: 1rem !important;
            padding-top: 0.5rem;
            border-top: 1px solid #e9ecef;
        }

        .price-info:not(.d-none) {
            display: block !important;
        }

        .price-row {
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .price-row i {
            width: 16px;
        }

        .price-row:last-child {
            margin-bottom: 0;
        }

        .availability-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .availability-badge.available {
            background-color: rgba(25, 135, 84, 0.1);
            color: #198754;
        }

        .availability-badge.booked {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .availability-badge.blocked {
            background-color: rgba(108, 117, 125, 0.1);
            color: #6c757d;
        }

        .availability-status {
            text-align: center;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e9ecef;
        }

        /* Room features styles */
        .feature-tag {
            display: inline-flex;
            align-items: center;
            background-color: #f8f9fa;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            margin: 0.25rem;
            font-size: 0.9rem;
            color: #495057;
        }

        .feature-tag i {
            margin-right: 0.5rem;
            color: #0d6efd;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .slot-container {
                margin: 0;
            }

            .time-slot-card {
                margin-bottom: 1rem;
            }

            .form-check-label {
                flex-direction: column;
                align-items: flex-start !important;
            }

            .capacity-badge {
                margin-left: 0 !important;
                margin-top: 4px;
            }
        }

        /* Status badges */
        .badge {
            padding: 0.5em 1em;
        }

        .selected-slot {
            border-color: #0d6efd !important;
            background-color: rgba(13, 110, 253, 0.05) !important;
        }
    </style>
@endpush
