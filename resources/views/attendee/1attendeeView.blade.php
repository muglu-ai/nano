@extends('layouts.dashboard')
@section('title', 'Attendee Profile')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Attendee Profile</h3>
    <div class="row g-4 align-items-stretch">
        <!-- Profile Card -->
        <div class="col-12 col-md-3">
            <div class="card text-center shadow-sm p-4 h-100">
                <img src="{{ $attendee->profile_picture ? asset($attendee->profile_picture) 
                    : 'https://ui-avatars.com/api/?name='.urlencode($attendee->first_name.' '.$attendee->last_name).'&background=0D8ABC&color=fff' }}" 
                    class="img-fluid rounded-circle mb-3"
                    style="width: 110px; height: 110px;" alt="Profile">
                <h5 class="fw-bold mb-1">
                    {{ $attendee->title ? ucfirst(strtolower($attendee->title)) . ' ' : '' }}{{ $attendee->first_name }} {{ $attendee->middle_name }} {{ $attendee->last_name }}
                </h5>
                <div class="text-muted">{{ $attendee->designation ?? 'Attendee' }}</div>
                {{-- <span class="badge bg-pink mt-2 px-3 py-2" style="background-color:#e91e63;">
                    {{ $attendee->badge_category ?? 'VISITOR' }}
                </span> --}}
            </div>
        </div>
        <!-- Details Card -->
        <div class="col-12 col-md-9">
            <div class="card shadow-sm p-4 h-100">
                <div class="row g-2">
                    @php
                        // Helper function to output fields cleanly
                        function showField($label, $value) {
                            if(isset($value) && $value !== "") {
                                echo '<div class="col-12 col-sm-6 mb-2">
                                        <div class="small text-uppercase text-muted fw-semibold">' . $label . '</div>
                                        <div class="fw-bold">' . e($value) . '</div>
                                      </div>';
                            }
                        }

                            $products = $attendee->products;
                            if (!is_array($products) && $products) {
                                $products = json_decode($products, true);
                            }

                            //business_nature 
                            if (is_string($attendee->business_nature)) {
                                $attendee->business_nature = json_decode($attendee->business_nature, true);
                            }

                            // purpose
                            if (is_string($attendee->purpose)) {
                                $attendee->purpose = json_decode($attendee->purpose, true);
                            }

                            // ["All"] 
                            if (is_array($attendee->event_days) && count($attendee->event_days) === 1 && $attendee->event_days[0] === 'All') {
                                $attendee->event_days = 'All Days';
                            } elseif (is_string($attendee->event_days)) {
                                $attendee->event_days = json_decode($attendee->event_days, true);
                            }

                            
                    @endphp
                    {!! showField('Registration Date', $attendee->created_at ? $attendee->created_at->format('Y-m-d') : '') !!}

                    {!! showField('Company', $attendee->company) !!}
                    {!! showField('Email', $attendee->email) !!}
                    {!! showField('Mobile', $attendee->mobile) !!}
                    {!! showField('Address', $attendee->address) !!}
                    {!! showField('City', $attendee->city) !!}
                    {!! showField('State', $attendee->stateRelation->name ?? '') !!}
                    {!! showField('Country', $attendee->countryRelation->name ?? '') !!}
                    {!! showField('Postal Code', $attendee->postal_code) !!}
                    {!! showField('Unique ID', $attendee->unique_id) !!}
                    {!! showField('Registration Type', $attendee->registration_type) !!}
                    {!! showField('Event Days', is_array($attendee->event_days) ? implode(', ', $attendee->event_days) : $attendee->event_days) !!}
                    {!! showField('Job Category', $attendee->job_category) !!}
                    {!! showField('Job Subcategory', $attendee->job_subcategory) !!}
                    {!! showField('Other Job Category', $attendee->other_job_category) !!}
                    {!! showField('ID Card Type', $attendee->id_card_type) !!}
                    {!! showField('ID Card Number', $attendee->id_card_number) !!}
                    {!! showField('Products', is_array($products) ? implode(', ', $products) : ($products ?? 'N/A')) !!}
                    {!! showField('Business Nature', is_array($attendee->business_nature) ? implode(', ', $attendee->business_nature) : $attendee->business_nature) !!}
                    {!! showField('Startup', $attendee->startup ? 'Yes' : 'No') !!}
                    {!! showField('Promotion Consent', $attendee->promotion_consent ? 'Yes' : 'No') !!}
                    {!! showField('Inaugural Session', $attendee->inaugural_session ? 'Yes' : 'No') !!}
                    {!! showField('Purpose', is_array($attendee->purpose) ? implode(', ', $attendee->purpose) : $attendee->purpose) !!}
                    {{-- {!! showField('Status', ucfirst($attendee->status)) !!} --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
