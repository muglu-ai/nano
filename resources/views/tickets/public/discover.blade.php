@extends('tickets.public.layout')

@section('title', 'Select Your Ticket')

@push('styles')
<style>
        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 2rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Toggle Section */
        .toggle-section {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 3rem;
        }

        .toggle-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            padding: 0.75rem 2rem;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s;
            cursor: pointer;
        }

        .toggle-btn.active {
            background: var(--pink-gradient);
            border-color: transparent;
        }

        /* Event Selection */
        .event-selection {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 3rem;
            flex-wrap: wrap;
        }

        .event-radio {
            display: none;
        }

        .event-radio-label {
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
            min-width: 200px;
            text-align: center;
        }

        .event-radio:checked + .event-radio-label {
            background: var(--primary-gradient);
            border-color: transparent;
        }

        /* Ticket Cards */
        .tickets-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .ticket-card {
            background: var(--primary-gradient);
            border-radius: 20px;
            padding: 2rem;
            position: relative;
            overflow: hidden;
            transition: all 0.3s;
            cursor: pointer;
        }

        .ticket-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }

        .ticket-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 20px;
            background: repeating-linear-gradient(
                90deg,
                transparent,
                transparent 10px,
                rgba(0, 0, 0, 0.3) 10px,
                rgba(0, 0, 0, 0.3) 20px
            );
        }

        .ticket-card.sold-out {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .ticket-name {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #fff;
        }

        .ticket-price {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #fff;
        }

        .ticket-status {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .ticket-status.sold-out {
            background: rgba(229, 62, 62, 0.8);
        }

        /* Entitlements Table */
        .entitlements-section {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 2rem;
            margin-top: 3rem;
        }

        .entitlements-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .entitlements-table {
            width: 100%;
            border-collapse: collapse;
        }

        .entitlements-table th,
        .entitlements-table td {
            padding: 1rem;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .entitlements-table th {
            background: rgba(102, 126, 234, 0.2);
            font-weight: 600;
            color: #fff;
        }

        .entitlements-table td {
            color: #ccc;
        }

        .check-icon {
            color: #48bb78;
            font-size: 1.25rem;
        }

        .cross-icon {
            color: #e53e3e;
            font-size: 1.25rem;
        }

        @media (max-width: 768px) {
            .tickets-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
<div class="ticket-page-container">
        <h1 class="page-title">Select Your Ticket</h1>

        <!-- Toggle: Indian/International -->
        <div class="toggle-section">
            <button class="toggle-btn active" id="toggle-indian">Indian</button>
            <button class="toggle-btn" id="toggle-international">International</button>
        </div>

        <!-- Event Selection -->
        <div class="event-selection">
            <input type="radio" name="event-option" id="event-all" class="event-radio" checked>
            <label for="event-all" class="event-radio-label">
                @if($event->start_date && $event->end_date)
                    @php
                        $days = \Carbon\Carbon::parse($event->start_date)->diffInDays(\Carbon\Carbon::parse($event->end_date)) + 1;
                    @endphp
                    {{ $event->event_name }} ({{ $days }} days, {{ \Carbon\Carbon::parse($event->start_date)->format('j M') }} - {{ \Carbon\Carbon::parse($event->end_date)->format('j M') }})
                @else
                    {{ $event->event_name }}
                @endif
            </label>
        </div>

        <!-- Ticket Cards -->
        <div class="tickets-grid">
            @forelse($ticketTypes as $ticketType)
                @php
                    $currentPrice = $ticketType->getCurrentPrice();
                    $isSoldOut = $ticketType->isSoldOut();
                    $isEarlyBird = $ticketType->isEarlyBirdActive();
                @endphp
                <div class="ticket-card {{ $isSoldOut ? 'sold-out' : '' }}" 
                     onclick="{{ !$isSoldOut ? "selectTicket({$ticketType->id})" : '' }}">
                    <div class="ticket-name">{{ $ticketType->name }}</div>
                    <div class="ticket-price">₹{{ number_format($currentPrice, 0) }}</div>
                    @if($isEarlyBird && $ticketType->early_bird_price)
                        <small style="opacity: 0.8;">Early Bird: ₹{{ number_format($ticketType->early_bird_price, 0) }}</small>
                    @endif
                    <div class="ticket-status {{ $isSoldOut ? 'sold-out' : 'available' }}">
                        {{ $isSoldOut ? 'Sold Out' : 'Available' }}
                    </div>
                    @if($ticketType->description)
                        <p style="font-size: 0.875rem; opacity: 0.9; margin-top: 1rem;">{{ $ticketType->description }}</p>
                    @endif
                </div>
            @empty
                <div class="col-12 text-center">
                    <p class="text-muted">No tickets available at this time.</p>
                </div>
            @endforelse
        </div>

        <!-- Entitlements Table -->
        @if($ticketTypes->count() > 0)
            <div class="entitlements-section">
                <h2 class="entitlements-title">Ticket Entitlements</h2>
                <div class="table-responsive">
                    <table class="entitlements-table">
                        <thead>
                            <tr>
                                <th>Inclusions</th>
                                @foreach($ticketTypes as $ticketType)
                                    <th>{{ $ticketType->name }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // Define common entitlements - you can make this dynamic later
                                $entitlements = [
                                    'Inaugural' => true,
                                    'Plenary' => true,
                                    'Conference Sessions' => true,
                                    'Exhibition Access' => true,
                                    'Networking Events' => false,
                                    'Lunch' => false,
                                ];
                            @endphp
                            @foreach($entitlements as $entitlement => $defaultValue)
                                <tr>
                                    <td><strong>{{ $entitlement }}</strong></td>
                                    @foreach($ticketTypes as $ticketType)
                                        <td>
                                            <i class="fas {{ $defaultValue ? 'fa-check check-icon' : 'fa-times cross-icon' }}"></i>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
        // Toggle functionality
        document.getElementById('toggle-indian').addEventListener('click', function() {
            this.classList.add('active');
            document.getElementById('toggle-international').classList.remove('active');
        });

        document.getElementById('toggle-international').addEventListener('click', function() {
            this.classList.add('active');
            document.getElementById('toggle-indian').classList.remove('active');
        });

        // Ticket selection
        function selectTicket(ticketTypeId) {
            window.location.href = '{{ route("tickets.register", $event->slug ?? $event->id) }}?ticket=' + ticketTypeId;
        }
    </script>
@endpush

