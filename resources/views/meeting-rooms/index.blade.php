@extends('meeting-rooms.layout')

@section('content')

<div class="content-section">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title mb-0">
            <i class="fas fa-calendar-alt text-primary"></i>
            Book a Meeting Room
        </h2>
        <a href="{{ route('meeting_rooms.mybook') }}" class="btn btn-outline-primary ms-3">
            <i class="fas fa-list"></i> My Bookings
        </a>
    </div>

    <!-- Step 1: Room Selection -->
    <div class="form-section">
        <div class="d-flex align-items-center mb-3">
            <div class="step-indicator me-3">1</div>
            <h3 class="h5 mb-0 fw-semibold">Select Room Type</h3>
        </div>

        <!-- Room Selector -->
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
            <div class="room-features" id="room-features"></div>
        </div>
    </div>

    <!-- Step 2: Availability Grid -->
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
                    @if (\Carbon\Carbon::parse($date)->format('d F') !== '02 September')
                    <div class="col-md-6">
                        <div class="time-slot-card" data-date="{{ $date }}" data-slot="morning">
                            <div class="time-slot">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="session-info">
                                        <h5 class="session-title"></i> Morning Session</h5>
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
                                <div class="booking-action text-center mt-3">
                                    <button class="btn btn-primary book-now-btn">
                                        <i class="fas fa-calendar-check me-2"></i>Book Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="col-md-6">
                        <div class="time-slot-card" data-date="{{ $date }}" data-slot="afternoon">
                            <div class="time-slot">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="session-info">
                                        <h5 class="session-title"></i> Afternoon Session</h5>
                                        <div class="time-info">
                                            <i class="fas fa-clock text-primary"></i>
                                            13:30 - 17:30 (4 hours)
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
                                <div class="booking-action text-center mt-3">
                                    <button class="btn btn-primary book-now-btn">
                                        <i class="fas fa-calendar-check me-2"></i>Book Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Hidden form for booking submission -->
    <form id="booking-form" method="POST" action="/meeting-rooms/book" style="display: none;">
        @csrf
        <input type="hidden" name="room_type_id" id="booking-room-id">
        <input type="hidden" name="booking_date" id="booking-date">
        <input type="hidden" name="slot" id="booking-slot">
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roomRadios = document.querySelectorAll('.room-radio');
        const availabilityGrid = document.querySelector('.time-slots-grid');
        const eventDates = @json($eventDates);

        availabilityGrid.style.display = 'none';

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
            const duration = 4;

            // Show features
            const featuresContainer = document.getElementById('room-features');
            const roomInfo = document.getElementById('room-info');
            roomInfo.style.display = 'block';

            featuresContainer.innerHTML = `
                <span class="feature-tag"><i class="fas fa-users me-1"></i>${roomData.capacity} Capacity</span>
                ${roomData.equipment ? `<span class="feature-tag"><i class="fas fa-plug me-1"></i>${roomData.equipment}</span>` : ''}
                <span class="feature-tag"><i class="fas fa-mug-hot me-1"></i>${roomData.fnb}</span>
                <span class="feature-tag"><i class="fas fa-rupee-sign me-1"></i>₹${roomData.member_price}/4 hr (Member), ₹${roomData.non_member_price}/ 4 hr (Non-Member)</span>
            `;

            // Update prices
            document.querySelectorAll('.time-slot').forEach(slot => {
                const priceInfo = slot.querySelector('.price-info');
                const memberPrice = priceInfo.querySelector('.member-price');
                const nonMemberPrice = priceInfo.querySelector('.non-member-price');
                const memberTotal = (parseFloat(roomData.member_price)).toFixed(2);
                const nonMemberTotal = (parseFloat(roomData.non_member_price)).toFixed(2);


                memberPrice.textContent = memberTotal;
                nonMemberPrice.textContent = nonMemberTotal;
                priceInfo.classList.remove('d-none');
            });

            // Show grid and fetch availability
            availabilityGrid.style.display = 'block';
            setAllSlotsToLoading();

            eventDates.forEach(date => {
                fetchAvailability([roomData.id], date);
            });
        }

        function setAllSlotsToLoading() {
            document.querySelectorAll('.time-slot').forEach(slot => {
                const statusElement = slot.querySelector('.availability-status');
                slot.className = 'time-slot';
                statusElement.innerHTML = `
                    <div class="spinner-border spinner-border-sm" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <span class="ms-2">Checking availability...</span>
                `;
            });
        }

        function fetchAvailability(roomIds, date) {
            fetch(`/meeting-rooms/availability?room_ids=${roomIds.join(',')}&date=${date}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    //console.log(`Availability for ${date}`, data); // Debug
                    updateSlotUI('morning', date, data.morning);
                    updateSlotUI('afternoon', date, data.afternoon);
                })
                .catch(err => console.error("API error:", err));
        }

        function updateSlotUI(slotType, date, availability) {
            const slotElement = document.querySelector(`[data-date="${date}"][data-slot="${slotType}"]`);
            if (!slotElement) return;

            const timeSlotEl = slotElement.querySelector('.time-slot');
            const statusElement = slotElement.querySelector('.availability-status');
            const bookingAction = slotElement.querySelector('.booking-action');
            const bookNowBtn = bookingAction.querySelector('.book-now-btn');

            timeSlotEl.classList.remove('available', 'booked', 'selected');
            bookingAction.style.display = 'none';

            let badgeHtml = '';
            if (!availability) {
                badgeHtml = `<span class="text-danger">No data</span>`;
            } else if (availability.is_blocked) {
                timeSlotEl.classList.add('booked');
                badgeHtml = `
                    <span class="availability-badge blocked"><i class="fas fa-ban me-2"></i>Blocked</span>
                    <div class="text-muted mt-1">${availability.reason || 'Not available'}</div>
                `;
            } else if (availability.is_available) {
                timeSlotEl.classList.add('available');
                badgeHtml = `
                    <span class="availability-badge available"><i class="fas fa-check-circle me-2"></i>Available</span>
                    <div class="text-success mt-1">${availability.available_count} rooms left</div>
                `;

                // Show and configure Book Now button
                bookingAction.style.display = 'block';
                bookNowBtn.onclick = (e) => {
                    e.preventDefault();
                    const selectedRoom = document.querySelector('.room-radio:checked');
                    if (!selectedRoom) return;

                    const roomId = selectedRoom.value;
                    const roomName = selectedRoom.nextElementSibling.querySelector('.room-title').textContent;

                    Swal.fire({
                        title: 'Confirm Booking',
                        html: `<div style="text-align:left">
                            <strong>Room:</strong> ${roomName}<br>
                            <strong>Date:</strong> ${date}<br>
                            <strong>Session:</strong> ${slotType.charAt(0).toUpperCase() + slotType.slice(1)}
                        </div>`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Book Now',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('booking-room-id').value = roomId;
                            document.getElementById('booking-date').value = date;
                            document.getElementById('booking-slot').value = slotType;
                            document.getElementById('booking-form').submit();
                        }
                    });
                };
            } else {
                timeSlotEl.classList.add('booked');
                badgeHtml = `<span class="availability-badge booked"><i class="fas fa-times-circle me-2"></i>Fully Booked</span>`;
            }

            statusElement.innerHTML = badgeHtml;
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