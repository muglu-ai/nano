@extends('layouts.sponsor-application')
@section('title', 'Sponsorship Confirmation')
@section('content')
<div class="container py-5">
    <h1 class="h4 mb-4">Sponsorship Application Summary</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @php
        $sqm = $application->allocated_sqm;
        $discountMap = [
            72 => 10,
            144 => 15,
            200 => 25,
        ];

        $eligibleDiscount = 0;
        foreach ($discountMap as $threshold => $percent) {
            if ($sqm >= $threshold) {
                $eligibleDiscount = $percent;
            }
        }
        $exhibition_price = $application->invoice->price ?? 0; // Ensure we have a default value

        $total = 0;
        $hasInitiated = false;
        $discountAmount = 0;
    @endphp
     @php
     $titleTiers = $titleSponsors->sortBy('price');
     foreach($sponsorships as $s) {
         if ($s->status !== 'rejected') {
             $total += $s->price * $s->sponsorship_item_count;
         }
         if ($s->status === 'initiated') {
             $hasInitiated = true;
         }
     }

     $discountAmount = ($exhibition_price * $eligibleDiscount) / 100;
     
     $afterDiscount = round($total);
     $gst = round($afterDiscount * 0.18);
     $grandTotal = round($afterDiscount + $gst);

     $payable = $afterDiscount + $discountAmount;
     $eligible = null;
     $nextTier = null;
 @endphp

    @if($application->withdraw_title == 0) 
    <div class="card mb-4">
        <div class="card-header">Title Sponsorship Eligibility</div>
        <div class="card-body">
           

            @foreach($titleTiers as $tier)
                @if($payable >= $tier->price)
                    @php $eligible = $tier; @endphp
                @elseif(!$nextTier)
                    @php $nextTier = $tier; @endphp
                @endif
            @endforeach

            @if($eligible)
                <div class="alert alert-success">
                    🎉 You are eligible for the <strong>{{ $eligible->name }}</strong> Title Sponsorship<br>
                    {{-- Worth ₹{{ number_format($eligible->price) }} --}}
                </div>
            @elseif($nextTier)
                <div class="alert alert-warning">
                    Add ₹{{ number_format($nextTier->price - $payable) }} more to reach <strong>{{ $nextTier->name }}</strong> (₹{{ number_format($nextTier->price) }})
                </div>
            @else
                <div class="alert alert-info">
                    No title sponsorship tiers available for comparison.
                </div>
            @endif
        </div>
    </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">Applied Sponsorship Items</div>
        <div class="card-body">
            <ul class="list-group">
                @foreach($sponsorships as $sponsorship)
                    <li class="list-group-item d-flex justify-content-between">
                        <div>
                            <strong>{{ $sponsorship->sponsorship_item }}</strong><br>
                            Quantity: {{ $sponsorship->sponsorship_item_count }}<br>
                            Price per item: ₹{{ number_format($sponsorship->price) }}<br>
                           
                        </div>
                        <div>
                            ₹{{ number_format($sponsorship->price * $sponsorship->sponsorship_item_count) }} <br>
                            Status:  <span class="badge 
                            @if ($sponsorship->status === 'initiated') bg-warning 
                            @elseif ($sponsorship->status === 'approved') bg-success 
                            @elseif ($sponsorship->status === 'rejected') bg-danger 
                            @else bg-secondary 
                            @endif">
                            {{ ucfirst($sponsorship->status) }}
                        </span><br>
                            @if($sponsorship->status === 'approved' && $sponsorship->approval_date)
                                Approved on: <span class="fw-bold">{{ \Carbon\Carbon::parse($sponsorship->approval_date)->format('d M Y') }}</span>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>

            <hr>
            <div class="text-end fw-bold">
                Subtotal : ₹{{ number_format($total) }}<br>
                @if($eligibleDiscount > 0 && $application->withdraw_title == 0)
                {{-- Discount: ₹{{ round($discountAmount) }} for eligible title sponsorship <br> --}}
                {{-- Credit note: ₹{{ round($discountAmount) }}  for eligible title sponsorship <br> --}}
                🎁 You're eligible for <strong>₹{{ round($discountAmount ) }} </strong> credit towards Title Sponsor added from the purchase of a {{$application->allocated_sqm}} SQM Exhibition Booth. <br>
                    {{-- <span class="text-success">Discount Applied Based on Allocated Sqm ({{ $sqm }} sqm)</span> --}}
                    {{-- <br> --}}
                @endif

                GST (18%): ₹{{ number_format($gst) }}<br>
                <span class="h5">Grand Total Payable: ₹{{ number_format($grandTotal) }}</span>
            </div>
            @if($hasInitiated)
                <form method="POST" action="{{ route('sponsor.submit') }}" class="text-end mt-3">
                    @csrf
                    <input type="hidden" name="sponsor_id" value="{{ $sponsorships->firstWhere('status', 'initiated')->id }}">
                    <button type="submit" class="btn btn-primary">Submit Sponsorship</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
