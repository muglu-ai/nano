@extends('layouts.dashboard')
@section('title', 'Attendee Profile')

@section('content')

<style>
.custom-modal-form .modal-content {
    border-radius: 12px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.08);
    padding: 0 8px;
}
.custom-modal-form .modal-header {
    border-bottom: 1px solid #f0f0f0;
    background: #f8f9fa;
}
.custom-modal-form .modal-title {
    font-weight: 600;
    color: #333;
}
.custom-modal-form .form-label {
    font-weight: 500;
    color: #555;
}
.custom-modal-form .form-control {
    border-radius: 6px;
    border: 1px solid #d1d5db;
    box-shadow: none;
    font-size: 1rem;
}
.custom-modal-form .form-control:focus {
    border-color: #0d8abc;
    box-shadow: 0 0 0 2px rgba(13,138,188,0.1);
}
.custom-modal-form .modal-footer {
    border-top: 1px solid #f0f0f0;
    background: #f8f9fa;
}
.custom-modal-form .btn-success {
    background: #22bb33;
    border: none;
}
.custom-modal-form .btn-success:hover {
    background: #1da127;
}
.custom-modal-form .btn-secondary {
    background: #6c757d;
    border: none;
}
.custom-modal-form .btn-secondary:hover {
    background: #565e64;
}
</style>
<div class="container mt-4">
    <h3 class="mb-4">Attendee Profile</h3>
    @if(auth()->user() && auth()->user()->role === 'admin')
    <button class="btn btn-sm btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#editAttendeeModal">
        Edit
    </button>
@endif
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

    @if(auth()->user() && auth()->user()->role === 'admin')
<!-- Edit Modal -->
<div class="modal fade" id="editAttendeeModal" tabindex="-1" aria-labelledby="editAttendeeModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('attendee.update', $attendee->unique_id) }}" enctype="multipart/form-data">
  @csrf
  @method('PATCH')
  <div class="modal-content custom-modal-form">
    <div class="modal-header">
      <h5 class="modal-title" id="editAttendeeModalLabel">Edit Attendee Details</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
      <div class="mb-3">
        <label for="profile_picture" class="form-label">Profile Picture</label>
        <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
        @if($attendee->profile_picture)
          <img src="{{ asset($attendee->profile_picture) }}" class="img-thumbnail mt-2" style="width:60px;height:60px;">
        @endif
      </div>
      <div class="mb-3">
        <label for="company" class="form-label">Company</label>
        <input type="text" class="form-control" id="company" name="company" value="{{ $attendee->company }}">
      </div>
      <div class="mb-3">
    <label for="id_card_type" class="form-label">ID Card Type</label>
    <select class="form-select" id="id_card_type" name="id_card_type" required>
        <option value="">Select ID Card Type</option>
        <option value="Aadhaar Card" {{ $attendee->id_card_type == 'Aadhaar Card' ? 'selected' : '' }}>Aadhaar Card</option>
        <option value="PAN Card" {{ $attendee->id_card_type == 'PAN Card' ? 'selected' : '' }}>PAN Card</option>
        <option value="Driving License" {{ $attendee->id_card_type == 'Driving License' ? 'selected' : '' }}>Driving License</option>
        <option value="Passport" {{ $attendee->id_card_type == 'Passport' ? 'selected' : '' }}>Passport</option>
        <option value="Voter ID" {{ $attendee->id_card_type == 'Voter ID' ? 'selected' : '' }}>Voter ID</option>
    </select>
</div>
      <div class="mb-3">
        <label for="id_card_number" class="form-label">ID Card Number</label>
        <input type="text" class="form-control" id="id_card_number" name="id_card_number" value="{{ $attendee->id_card_number }}">
      </div>
    </div>
    <div class="modal-footer d-flex justify-content-end">
      <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
      <button type="submit" class="btn btn-success">Save Changes</button>
    </div>
  </div>
</form>
  </div>
</div>
@endif
</div>
@endsection
