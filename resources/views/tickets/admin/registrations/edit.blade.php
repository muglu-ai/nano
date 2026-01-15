@extends('layouts.dashboard')
@section('title', 'Edit Ticket Registration')
@section('content')

    <style>
        .card {
            border: 1px solid #e3e6f0;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background-color: #6A1B9A;
            color: white;
            border-bottom: none;
        }
        
        .card-header h5 {
            color: white;
            font-weight: 600;
            margin: 0;
        }
        
        .back-btn {
            background-color: #6A1B9A;
            border-color: #6A1B9A;
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 0.35rem;
            text-decoration: none;
            display: inline-block;
        }
        
        .back-btn:hover {
            background-color: #4A0072;
            border-color: #4A0072;
            color: white;
            text-decoration: none;
        }
        
        .delegate-item {
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
            padding: 1rem;
            margin-bottom: 1rem;
            background-color: #f8f9fc;
        }
        
        .btn-remove-delegate {
            background-color: #dc3545;
            border-color: #dc3545;
            color: white;
        }
        
        .btn-remove-delegate:hover {
            background-color: #c82333;
            border-color: #bd2130;
            color: white;
        }
        
        .btn-add-delegate {
            background-color: #28a745;
            border-color: #28a745;
            color: white;
        }
        
        .btn-add-delegate:hover {
            background-color: #218838;
            border-color: #1e7e34;
            color: white;
        }
        
        .table-bordered {
            border: 1px solid #dee2e6 !important;
        }
        
        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6 !important;
            padding: 0.75rem;
            vertical-align: middle;
        }
        
        .table-bordered thead th {
            border-bottom-width: 2px !important;
            border: 1px solid #dee2e6 !important;
        }
        
        .table-bordered tbody tr td {
            border: 1px solid #dee2e6 !important;
        }
        
        .table {
            border-collapse: separate;
            border-spacing: 0;
        }
    </style>

    <div class="container-fluid py-2">
        <div class="row mt-4">
            <div class="col-12">
                <a href="{{ route('admin.tickets.registrations.show', $registration->id) }}" class="back-btn mb-3">
                    <i class="fas fa-arrow-left me-2"></i>Back to Details
                </a>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.tickets.registrations.update', $registration->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Company Information -->
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-building me-2"></i>Company Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Company Name <span class="text-danger">*</span></label>
                                    <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $registration->company_name) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nationality <span class="text-danger">*</span></label>
                                    <select name="nationality" class="form-control" required>
                                        <option value="Indian" {{ old('nationality', $registration->nationality) === 'Indian' ? 'selected' : '' }}>Indian</option>
                                        <option value="International" {{ old('nationality', $registration->nationality) === 'International' ? 'selected' : '' }}>International</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Company Country</label>
                                    <input type="text" name="company_country" class="form-control" value="{{ old('company_country', $registration->company_country) }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Company State</label>
                                    <input type="text" name="company_state" class="form-control" value="{{ old('company_state', $registration->company_state) }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Company City</label>
                                    <input type="text" name="company_city" class="form-control" value="{{ old('company_city', $registration->company_city) }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Company Phone</label>
                                    <input type="text" name="company_phone" class="form-control" value="{{ old('company_phone', $registration->company_phone) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-user me-2"></i>Contact Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Contact Name <span class="text-danger">*</span></label>
                                    <input type="text" name="contact_name" class="form-control" value="{{ old('contact_name', $registration->contact ? $registration->contact->name : '') }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Contact Email <span class="text-danger">*</span></label>
                                    <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $registration->contact ? $registration->contact->email : '') }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Contact Phone</label>
                                    <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $registration->contact ? $registration->contact->phone : '') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- GST Information -->
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-file-invoice me-2"></i>GST Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="gst_required" id="gst_required" value="1" 
                                               {{ old('gst_required', $registration->gst_required) ? 'checked' : '' }}
                                               onchange="toggleGstFields()">
                                        <label class="form-check-label" for="gst_required">
                                            GST Required
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div id="gst-fields" style="{{ old('gst_required', $registration->gst_required) ? '' : 'display: none;' }}">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">GSTIN</label>
                                        <input type="text" name="gstin" class="form-control" value="{{ old('gstin', $registration->gstin) }}" maxlength="15">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">GST Legal Name</label>
                                        <input type="text" name="gst_legal_name" class="form-control" value="{{ old('gst_legal_name', $registration->gst_legal_name) }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">GST Address</label>
                                        <textarea name="gst_address" class="form-control" rows="2">{{ old('gst_address', $registration->gst_address) }}</textarea>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">GST State</label>
                                        <input type="text" name="gst_state" class="form-control" value="{{ old('gst_state', $registration->gst_state) }}">
                                    </div>
                                </div>
                            </div>
                            @if(!old('gst_required', $registration->gst_required))
                            <div id="gst-not-required-message" class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>GST is not required for this registration.
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Delegates -->
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-users me-2"></i>Delegates</h5>
                        </div>
                        <div class="card-body">
                            <div id="delegates-container">
                                @php
                                    $oldDelegates = old('delegates');
                                    if ($oldDelegates) {
                                        $delegates = $oldDelegates;
                                    } else {
                                        $delegates = $registration->delegates->map(function($delegate) {
                                            return [
                                                'id' => $delegate->id,
                                                'salutation' => $delegate->salutation,
                                                'first_name' => $delegate->first_name,
                                                'last_name' => $delegate->last_name,
                                                'email' => $delegate->email,
                                                'phone' => $delegate->phone,
                                                'job_title' => $delegate->job_title,
                                            ];
                                        })->toArray();
                                    }
                                    if (empty($delegates)) {
                                        $delegates = [['salutation' => 'Mr', 'first_name' => '', 'last_name' => '', 'email' => '', 'phone' => '', 'job_title' => '']];
                                    }
                                @endphp
                                @foreach($delegates as $index => $delegate)
                                    <div class="delegate-item" data-index="{{ $index }}">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="mb-0">Delegate {{ $index + 1 }}</h6>
                                            @if($index > 0)
                                            <button type="button" class="btn btn-sm btn-remove-delegate" onclick="removeDelegate(this)">
                                                <i class="fas fa-times me-1"></i>Remove
                                            </button>
                                            @endif
                                        </div>
                                        @php
                                            $delegateId = $delegate['id'] ?? '';
                                            $salutation = $delegate['salutation'] ?? 'Mr';
                                            $firstName = $delegate['first_name'] ?? '';
                                            $lastName = $delegate['last_name'] ?? '';
                                            $email = $delegate['email'] ?? '';
                                            $phone = $delegate['phone'] ?? '';
                                            $jobTitle = $delegate['job_title'] ?? '';
                                        @endphp
                                        <input type="hidden" name="delegates[{{ $index }}][id]" value="{{ $delegateId }}">
                                        <div class="row">
                                            <div class="col-md-2 mb-3">
                                                <label class="form-label">Salutation <span class="text-danger">*</span></label>
                                                <select name="delegates[{{ $index }}][salutation]" class="form-control" required>
                                                    <option value="Mr" {{ $salutation === 'Mr' ? 'selected' : '' }}>Mr</option>
                                                    <option value="Ms" {{ $salutation === 'Ms' ? 'selected' : '' }}>Ms</option>
                                                    <option value="Mrs" {{ $salutation === 'Mrs' ? 'selected' : '' }}>Mrs</option>
                                                    <option value="Dr" {{ $salutation === 'Dr' ? 'selected' : '' }}>Dr</option>
                                                    <option value="Prof" {{ $salutation === 'Prof' ? 'selected' : '' }}>Prof</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                                <input type="text" name="delegates[{{ $index }}][first_name]" class="form-control" value="{{ $firstName }}" required>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                                <input type="text" name="delegates[{{ $index }}][last_name]" class="form-control" value="{{ $lastName }}" required>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                                <input type="email" name="delegates[{{ $index }}][email]" class="form-control" value="{{ $email }}" required>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Phone</label>
                                                <input type="text" name="delegates[{{ $index }}][phone]" class="form-control" value="{{ $phone }}">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Designation</label>
                                                <input type="text" name="delegates[{{ $index }}][job_title]" class="form-control" value="{{ $jobTitle }}">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-add-delegate" onclick="addDelegate()">
                                <i class="fas fa-plus me-1"></i>Add Delegate
                            </button>
                        </div>
                    </div>

                    <!-- Order Status -->
                    @if($registration->order)
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-receipt me-2"></i>Order Status</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Order Status</label>
                                    <select name="order_status" class="form-control">
                                        <option value="pending" {{ old('order_status', $registration->order->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="paid" {{ old('order_status', $registration->order->status) === 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="cancelled" {{ old('order_status', $registration->order->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        <option value="refunded" {{ old('order_status', $registration->order->status) === 'refunded' ? 'selected' : '' }}>Refunded</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.tickets.registrations.show', $registration->id) }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Registration
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let delegateIndex = {{ count($delegates) }};

        function addDelegate() {
            const container = document.getElementById('delegates-container');
            const newDelegate = document.createElement('div');
            newDelegate.className = 'delegate-item';
            newDelegate.setAttribute('data-index', delegateIndex);
            newDelegate.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Delegate ${delegateIndex + 1}</h6>
                    <button type="button" class="btn btn-sm btn-remove-delegate" onclick="removeDelegate(this)">
                        <i class="fas fa-times me-1"></i>Remove
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Salutation <span class="text-danger">*</span></label>
                        <select name="delegates[${delegateIndex}][salutation]" class="form-control" required>
                            <option value="Mr">Mr</option>
                            <option value="Ms">Ms</option>
                            <option value="Mrs">Mrs</option>
                            <option value="Dr">Dr</option>
                            <option value="Prof">Prof</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" name="delegates[${delegateIndex}][first_name]" class="form-control" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="delegates[${delegateIndex}][last_name]" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="delegates[${delegateIndex}][email]" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="delegates[${delegateIndex}][phone]" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Designation</label>
                        <input type="text" name="delegates[${delegateIndex}][job_title]" class="form-control">
                    </div>
                </div>
            `;
            container.appendChild(newDelegate);
            delegateIndex++;
        }

        function removeDelegate(button) {
            const delegateItem = button.closest('.delegate-item');
            delegateItem.remove();
        }

        function toggleGstFields() {
            const gstRequired = document.getElementById('gst_required').checked;
            const gstFields = document.getElementById('gst-fields');
            const gstMessage = document.getElementById('gst-not-required-message');
            
            if (gstRequired) {
                gstFields.style.display = 'block';
                if (gstMessage) {
                    gstMessage.style.display = 'none';
                }
            } else {
                gstFields.style.display = 'none';
                if (gstMessage) {
                    gstMessage.style.display = 'block';
                } else {
                    // Create message if it doesn't exist
                    const messageDiv = document.createElement('div');
                    messageDiv.id = 'gst-not-required-message';
                    messageDiv.className = 'alert alert-info';
                    messageDiv.innerHTML = '<i class="fas fa-info-circle me-2"></i>GST is not required for this registration.';
                    gstFields.parentNode.appendChild(messageDiv);
                }
            }
        }
    </script>
@endsection
