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
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .toggle-btn:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }
        
        .toggle-btn i {
            margin-right: 0.5rem;
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
        
        .ticket-price .currency {
            font-size: 1.5rem;
            vertical-align: top;
            margin-right: 0.25rem;
        }
        
        .early-bird-info,
        .regular-price-info {
            margin-top: 0.5rem;
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
            <button class="toggle-btn active" id="toggle-indian" data-nationality="national">
                <i class="fas fa-flag"></i> Indian (INR)
            </button>
            <button class="toggle-btn" id="toggle-international" data-nationality="international">
                <i class="fas fa-globe"></i> International (USD)
            </button>
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
        <div class="tickets-grid" id="tickets-grid">
            @forelse($ticketTypes as $ticketType)
                @php
                    $isSoldOut = $ticketType->isSoldOut();
                    $isEarlyBird = $ticketType->isEarlyBirdActive();
                @endphp
                <div class="ticket-card {{ $isSoldOut ? 'sold-out' : '' }}" 
                     data-ticket-id="{{ $ticketType->id }}"
                     onclick="{{ !$isSoldOut ? "selectTicket({$ticketType->id})" : '' }}">
                    <div class="ticket-name">{{ $ticketType->name }}</div>
                    <div class="ticket-price" data-nationality="national">
                        <span class="currency">₹</span>
                        <span class="price-national">{{ number_format($ticketType->getCurrentPrice('national'), 0) }}</span>
                    </div>
                    <div class="ticket-price" data-nationality="international" style="display: none;">
                        <span class="currency">$</span>
                        <span class="price-international">{{ number_format($ticketType->getCurrentPrice('international'), 2) }}</span>
                    </div>
                    @if($isEarlyBird)
                        <div class="early-bird-info" data-nationality="national">
                            <small style="opacity: 0.8; display: block; margin-top: 0.5rem;">
                                Early Bird: ₹{{ number_format($ticketType->getEarlyBirdPrice('national') ?? 0, 0) }}
                            </small>
                            <small style="opacity: 0.7; font-size: 0.75rem;">Regular: ₹{{ number_format($ticketType->getRegularPrice('national'), 0) }}</small>
                        </div>
                        <div class="early-bird-info" data-nationality="international" style="display: none;">
                            <small style="opacity: 0.8; display: block; margin-top: 0.5rem;">
                                Early Bird: ${{ number_format($ticketType->getEarlyBirdPrice('international') ?? 0, 2) }}
                            </small>
                            <small style="opacity: 0.7; font-size: 0.75rem;">Regular: ${{ number_format($ticketType->getRegularPrice('international'), 2) }}</small>
                        </div>
                    @else
                        <div class="regular-price-info" data-nationality="national">
                            <small style="opacity: 0.7; font-size: 0.875rem; display: block; margin-top: 0.5rem;">
                                Regular Price
                            </small>
                        </div>
                        <div class="regular-price-info" data-nationality="international" style="display: none;">
                            <small style="opacity: 0.7; font-size: 0.875rem; display: block; margin-top: 0.5rem;">
                                Regular Price
                            </small>
                        </div>
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
        let currentNationality = 'national'; // Default to national
        
        // Toggle functionality
        document.getElementById('toggle-indian').addEventListener('click', function() {
            this.classList.add('active');
            document.getElementById('toggle-international').classList.remove('active');
            currentNationality = 'national';
            updatePrices('national');
        });

        document.getElementById('toggle-international').addEventListener('click', function() {
            this.classList.add('active');
            document.getElementById('toggle-indian').classList.remove('active');
            currentNationality = 'international';
            updatePrices('international');
        });

        // Update prices based on nationality
        function updatePrices(nationality) {
            const ticketCards = document.querySelectorAll('.ticket-card');
            
            ticketCards.forEach(card => {
                // Show/hide price elements
                const priceNational = card.querySelector('.ticket-price[data-nationality="national"]');
                const priceInternational = card.querySelector('.ticket-price[data-nationality="international"]');
                const earlyBirdNational = card.querySelector('.early-bird-info[data-nationality="national"]');
                const earlyBirdInternational = card.querySelector('.early-bird-info[data-nationality="international"]');
                const regularNational = card.querySelector('.regular-price-info[data-nationality="national"]');
                const regularInternational = card.querySelector('.regular-price-info[data-nationality="international"]');
                
                if (nationality === 'national') {
                    if (priceNational) priceNational.style.display = 'block';
                    if (priceInternational) priceInternational.style.display = 'none';
                    if (earlyBirdNational) earlyBirdNational.style.display = 'block';
                    if (earlyBirdInternational) earlyBirdInternational.style.display = 'none';
                    if (regularNational) regularNational.style.display = 'block';
                    if (regularInternational) regularInternational.style.display = 'none';
                } else {
                    if (priceNational) priceNational.style.display = 'none';
                    if (priceInternational) priceInternational.style.display = 'block';
                    if (earlyBirdNational) earlyBirdNational.style.display = 'none';
                    if (earlyBirdInternational) earlyBirdInternational.style.display = 'block';
                    if (regularNational) regularNational.style.display = 'none';
                    if (regularInternational) regularInternational.style.display = 'block';
                }
            });
        }

        // Ticket selection
        function selectTicket(ticketTypeId) {
            window.location.href = '{{ route("tickets.register", $event->slug ?? $event->id) }}?ticket=' + ticketTypeId + '&nationality=' + currentNationality;
        }
    </script>
@endpush

