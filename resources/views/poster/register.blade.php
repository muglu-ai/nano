@extends('layouts.registration')

@section('title', 'Poster Registration - ' . config('constants.EVENT_NAME', 'Bengaluru Tech Summit') . ' ' . config('constants.EVENT_YEAR', '2026'))

@push('head-links')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@25.15.0/build/css/intlTelInput.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Varela+Round&display=swap" rel="stylesheet">
@endpush

@push('styles')

    <style>
        * {
            font-family: 'Varela Round', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        /* Override registration layout background */
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%) !important;
            min-height: 100vh;
        }

        .registration-main {
            background: transparent !important;
        }

        .wrap {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 16px;
        }

        .panel {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(102, 126, 234, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.1);
            padding: 40px;
            margin: 20px 0;
            backdrop-filter: blur(10px);
        }

        @media (max-width: 768px) {
            .panel {
                padding: 24px 16px;
                margin: 10px 0;
                border-radius: 16px;
            }
        }

        .panel h1 {
            font-family: 'Varela Round', sans-serif;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
            font-size: 2rem;
        }

        .panel .text-secondary {
            color: #000000;
            font-size: 0.95rem;
            margin-bottom: 30px;
        }

        .step-progress-bar {
            height: 10px;
            background: #e5e7eb;
            border-radius: 999px;
            overflow: hidden;
            margin-bottom: 24px;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.06);
        }

        .progress-indicator {
            height: 100%;
            width: 33.33%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            border-radius: 999px;
            transition: width 0.3s ease;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.4);
        }

        .step-header {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 32px;
        }

        .step-item {
            border: 2px solid #e5e7eb;
            border-radius: 16px;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 14px;
            background: #f9fafb;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .step-item:hover {
            border-color: #c4b5fd;
            background: #faf5ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
        }

        .step-item.active {
            border-color: #8b5cf6;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            box-shadow: 0 4px 16px rgba(139, 92, 246, 0.2);
        }

        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, #e9d5ff 0%, #ddd6fe 100%);
            color: #7c3aed;
            font-weight: 800;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .step-item.active .step-number {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .step-title {
            font-family: 'Varela Round', sans-serif;
            font-weight: 700;
            color: #000000;
            font-size: 0.95rem;
        }

        .step-item.active .step-title {
            color: #6d28d9;
        }

        .card-soft {
            border: 3px solid #8b5cf6;
            border-radius: 20px;
            padding: 28px;
            background: linear-gradient(to bottom, #ffffff 0%, #faf5ff 100%);
            margin-bottom: 24px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
        }

        .card-soft:hover {
            border-color: #6d28d9;
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.2);
            transform: translateY(-2px);
        }

        .card-soft h2 {
            font-family: 'Varela Round', sans-serif;
            font-weight: 700;
            color: #6d28d9;
            margin-bottom: 24px;
            font-size: 1.4rem;
            border-bottom: 3px solid #e9d5ff;
            padding-bottom: 12px;
        }

        .form-label {
            font-weight: 600;
            color: #000000;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .form-control,
        .form-select {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: #ffffff;
            height: 48px;
            box-sizing: border-box;
        }

        .form-control[type="file"],
        textarea.form-control {
            height: auto;
            min-height: 48px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #8b5cf6;
            box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1);
            outline: none;
            background: #ffffff;
        }

        .form-control:hover,
        .form-select:hover {
            border-color: #c4b5fd;
        }

        .help {
            color: #000000;
            font-size: 0.875rem;
            margin-top: 6px;
        }

        .req {
            color: #dc2626;
            font-weight: 800;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            padding: 14px 32px;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 16px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.4);
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        .btn-outline-primary {
            border: 2px solid #8b5cf6;
            color: #8b5cf6;
            border-radius: 10px;
            padding: 8px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #8b5cf6;
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 16px 20px;
        }

        .alert-danger {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border-left: 4px solid #dc2626;
        }

        .alert-warning {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            border-left: 4px solid #f59e0b;
        }

        /* phone lib UI  */
        .iti {
            width: 100%;
        }

        .iti__tel-input {
            width: 100%;
        }

        /* table poster tariff UI */
        .tariff-table {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            background: #ffffff;
        }

        .tariff-table thead th {
            text-align: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            font-weight: 700;
            padding: 16px;
            border: none;
        }

        .tariff-table .align-td {
            text-align: center;
            vertical-align: middle;
            padding: 14px;
            color: #000000;
        }

        .tariff-table tbody tr {
            transition: all 0.3s ease;
            background: #ffffff;
        }

        .tariff-table tbody tr:hover {
            background: #faf5ff;
        }

        .tariff-table tbody td {
            color: #000000;
        }

        .tariff-table tbody td strong {
            color: #000000;
        }

        #tariff-note-row {
            background: #ffffff !important;
        }

        #tariff-note-row td {
            color: #000000;
        }

        #tariff-note-row strong {
            color: #000000;
        }

        #tariff-note-processing {
            color: #000000;
        }

        .price-box {
            border: 2px solid #e5e7eb;
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .price-line {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 2px dashed #d1d5db;
        }

        .price-line:last-child {
            border-bottom: 0;
        }

        .price-label {
            color: #000000;
            font-weight: 600;
        }

        .price-value {
            font-weight: 800;
            color: #000000;
        }

        .price-total {
            margin-top: 8px;
            padding-top: 16px;
            border-top: 3px solid #8b5cf6;
        }

        .price-total .price-label,
        .price-total .price-value {
            font-size: 1.25rem;
            color: #6d28d9;
        }

        .badge-soft {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);
            border: 2px solid rgba(139, 92, 246, 0.3);
            color: #6d28d9;
            padding: 6px 14px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.875rem;
        }

        .form-check-input:checked {
            background-color: #8b5cf6;
            border-color: #8b5cf6;
        }

        .form-check-input:focus {
            border-color: #c4b5fd;
            box-shadow: 0 0 0 0.25rem rgba(139, 92, 246, 0.25);
        }

        hr {
            border-color: #e5e7eb;
            opacity: 0.5;
            margin: 32px 0;
        }

        /* Smooth animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-soft {
            animation: fadeIn 0.5s ease-out;
        }

        .step-item {
            animation: fadeIn 0.4s ease-out;
        }

        .step-item:nth-child(1) { animation-delay: 0.1s; }
        .step-item:nth-child(2) { animation-delay: 0.2s; }
        .step-item:nth-child(3) { animation-delay: 0.3s; }

        /* Form input focus animations */
        .form-control:focus,
        .form-select:focus {
            transform: translateY(-1px);
        }

        /* File input styling */
        .form-control[type="file"] {
            padding: 10px;
            cursor: pointer;
        }

        .form-control[type="file"]:hover {
            background: #faf5ff;
        }

        /* Radio button styling */
        .form-check-label {
            cursor: pointer;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.2s ease;
            display: inline-block;
        }

        .form-check-label:hover {
            background: #faf5ff;
        }

        /* Nationality Selector Enhanced UX */
        .nationality-selector {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .nationality-option {
            flex: 1;
            min-width: 180px;
            padding: 12px 16px !important;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            background: #ffffff;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            height: 48px;
            box-sizing: border-box;
        }

        .nationality-option:hover {
            border-color: #c4b5fd;
            background: #faf5ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
        }

        .nationality-option input[type="radio"]:checked + .nationality-label {
            color: #6d28d9;
            font-weight: 700;
        }

        .nationality-option input[type="radio"]:checked ~ .nationality-price {
            color: #6d28d9;
            font-weight: 800;
            font-size: 0.95rem;
        }

        .nationality-option:has(input[type="radio"]:checked) {
            border-color: #8b5cf6;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            box-shadow: 0 4px 16px rgba(139, 92, 246, 0.25);
        }

        .nationality-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: #000000;
            transition: all 0.3s ease;
        }

        .nationality-price {
            font-weight: 700;
            color: #000000;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        #nationality-help {
            font-size: 0.875rem;
            color: #000000;
            font-style: italic;
        }

        /* Tariff table enhanced */
        .tariff-table tbody tr {
            opacity: 0.5;
            transition: all 0.4s ease;
        }

        .tariff-table tbody tr.tariff-active {
            opacity: 1;
            transform: scale(1.02);
        }

        .tariff-table tbody tr.tariff-inactive {
            opacity: 0.3;
        }

        /* Price Preview Card */
        #price-preview-card {
            margin-bottom: 20px;
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Enhanced radio button styling */
        .nationality-option input[type="radio"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #8b5cf6;
        }

        .nationality-option input[type="radio"]:checked {
            accent-color: #6d28d9;
        }

            @media (max-width: 768px) {
            .step-header {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .card-soft {
                padding: 20px 16px;
            }

            .panel h1 {
                font-size: 1.5rem;
            }

            .nationality-selector {
                flex-direction: column;
            }

            .nationality-option {
                min-width: 100%;
            }

            .form-control,
            .form-select {
                font-size: 16px; /* Prevents zoom on iOS */
            }

            .tariff-table {
                font-size: 0.875rem;
            }

            .tariff-table thead th,
            .tariff-table tbody td {
                padding: 10px 8px;
            }
        }

        @media (max-width: 576px) {
            .panel {
                padding: 20px 12px;
            }

            .card-soft {
                padding: 16px 12px;
            }

            .card-soft h2 {
                font-size: 1.1rem;
            }

            .step-number {
                width: 35px;
                height: 35px;
                font-size: 14px;
            }

            .step-title {
                font-size: 0.85rem;
            }

            .price-box {
                padding: 16px;
            }

            .btn-primary {
                padding: 12px 24px;
                font-size: 0.95rem;
                width: 100%;
            }
        }
    </style>
@endpush

@section('header-title')
    <h1>Poster Registration</h1>
    <p>{{ config('constants.EVENT_NAME', 'Bengaluru Tech Summit') }} {{ config('constants.EVENT_YEAR', '2026') }}</p>
@endsection

@section('content')

    <div class="wrap">
        <div class="panel">
            <div class="text-center mb-4">
                <div style="font-family: 'Varela Round', sans-serif; font-size: 0.9rem; font-weight: 600; color: #8b5cf6; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 8px;">
                    Bengaluru Tech Summit 2026
                </div>
                <h1 class="h3 fw-bold mb-1">Poster Registration</h1>
                <div class="text-secondary mb-3">Step 1 of 3 — Fill details and continue to preview.</div>
            </div>

            @if ($errors->any())
            <div class="alert alert-danger">
                <div class="fw-bold mb-2">Please fix the following:</div>
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="step-progress-bar">
                <div class="progress-indicator"></div>
            </div>

            <div class="step-header">
                <div class="step-item active">
                    <div class="step-number">1</div>
                    <!-- <div class="step-title">Registration</div> -->
                    <div class="step-title">Poster Details</div>
                </div>
                <div class="step-item">
                    <div class="step-number">2</div>
                    <div class="step-title">Preview</div>
                </div>
                <div class="step-item">
                    <div class="step-number">3</div>
                    <div class="step-title">Success</div>
                </div>
            </div>

            <form method="POST" action="{{ route('poster.register.storeDraft') }}" enctype="multipart/form-data">
                @csrf

                @if (!empty($draft))
                <input type="hidden" name="token" value="{{ $draft->token }}">
                @endif

                {{-- Core --}}
                <div class="card-soft mb-3">
                    <h2 class="h5 fw-bold mb-3">Core</h2>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Sector <span class="req">*</span></label>
                            <select class="form-select" name="sector" required>
                                <option value="" disabled {{ old('sector', $draft->sector ?? '') ? '' : 'selected' }}>-- Select sector --</option>
                                @foreach ($sectorOptions as $opt)
                                <option value="{{ $opt }}" {{ old('sector', $draft->sector ?? '') === $opt ? 'selected' : '' }}>
                                    {{ $opt }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Nationality <span class="req">*</span></label>
                            <div class="nationality-selector d-flex gap-3 mt-0" style="margin-top: 0;">
                                <label class="nationality-option form-check-label">
                                    <input class="form-check-input me-2"
                                        type="radio"
                                        name="nationality"
                                        value="India"
                                        {{ old('nationality', $draft->nationality ?? '') === 'India' ? 'checked' : '' }}
                                        required>
                                    <span class="nationality-label">India</span>
                                    <span class="nationality-price" id="nat-price-india">₹3,000</span>
                                </label>
                                <label class="nationality-option form-check-label">
                                    <input class="form-check-input me-2"
                                        type="radio"
                                        name="nationality"
                                        value="International"
                                        {{ old('nationality', $draft->nationality ?? '') === 'International' ? 'checked' : '' }}
                                        required>
                                    <span class="nationality-label">International</span>
                                    <span class="nationality-price" id="nat-price-intl">$50</span>
                                </label>
                            </div>
                            <div class="help mt-2" id="nationality-help">Select your nationality to see applicable pricing</div>
                        </div>

                        {{-- Price Preview Card (shown when nationality selected) --}}
                        <div class="col-12" id="price-preview-card" style="display: none;">
                            <div class="card-soft" style="background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%); border-color: #8b5cf6;">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h3 class="h6 mb-1" style="color: #6d28d9; font-weight: 700;">Selected Pricing</h3>
                                        <div id="price-preview-text" style="color: #000000; font-size: 0.9rem;"></div>
                                    </div>
                                    <div id="price-preview-amount" style="font-size: 1.8rem; font-weight: 800; color: #6d28d9;"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Poster Tariff --}}
                        <div class="col-12" id="tariff-table-container" style="display: none;">
                            <table class="table table-bordered tariff-table mb-3">
                                <thead>
                                    <tr>
                                        <th colspan="2">Poster Tariff</th>
                                    </tr>
                                    <tr>
                                        <th class="align-td">Nationality</th>
                                        <th class="align-td">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="tariff-row-india" class="tariff-row" data-nat="India" style="display: none;">
                                        <td class="align-td">
                                            <strong>India</strong>
                                            <span class="badge-soft ms-2">Selected</span>
                                        </td>
                                        <td class="align-td">
                                            <strong style="color: #000000; font-size: 1.1rem;">₹3,000</strong>
                                        </td>
                                    </tr>
                                    <tr id="tariff-row-intl" class="tariff-row" data-nat="International" style="display: none;">
                                        <td class="align-td">
                                            <strong>International</strong>
                                            <span class="badge-soft ms-2">Selected</span>
                                        </td>
                                        <td class="align-td">
                                            <strong style="color: #000000; font-size: 1.1rem;">$50</strong>
                                        </td>
                                    </tr>
                                    <tr id="tariff-note-row">
                                        <td colspan="2">
                                            <strong>Note:</strong><br>
                                            - 18% GST is applicable.<br>
                                            <span id="tariff-note-processing">- Processing charges: 3% (India / CCAvenue), 9% (International / PayPal).</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>


                        <div class="col-12">
                            <label class="form-label">Title <span class="req">*</span></label>
                            <input class="form-control" name="title" maxlength="200"
                                value="{{ old('title', $draft->title ?? '') }}" required>
                        </div>
                    </div>
                </div>

                {{-- Lead Author --}}
                <div class="card-soft mb-3">
                    <h2 class="h5 fw-bold mb-3">Lead Author</h2>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Name of Lead Author <span class="req">*</span></label>
                            <input class="form-control" name="lead_name" maxlength="200"
                                value="{{ old('lead_name', $draft->lead_name ?? '') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Email <span class="req">*</span></label>
                            <input class="form-control" type="email" name="lead_email" maxlength="200"
                                value="{{ old('lead_email', $draft->lead_email ?? '') }}" required>
                            <div id="lead-email-alert" class="mt-2" style="display:none;"></div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">College Name/Organisation Name <span class="req">*</span></label>
                            <input class="form-control" name="lead_org" maxlength="250"
                                value="{{ old('lead_org', $draft->lead_org ?? '') }}" required>
                        </div>

                        <!-- <div class="col-md-4">
                            <label class="form-label">Country Code (optional)</label>
                            <input class="form-control" name="lead_ccode" maxlength="5"
                                value="{{ old('lead_ccode', $draft->lead_ccode ?? '') }}" placeholder="+91">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Phone <span class="req">*</span></label>
                            <input class="form-control" name="lead_phone" maxlength="15"
                               value="{{ old('lead_phone', $draft->lead_phone ?? '') }}" required>
                        </div> -->
                        <div class="col-md-4">
                            <label class="form-label">Mobile Number <span class="req">*</span></label>

                            <!-- visible phone input -->
                            <input
                                class="form-control"
                                id="lead_phone_input"
                                type="tel"
                                placeholder="Enter phone number"
                                value="{{ old('lead_phone_input', trim(($draft?->lead_ccode ?? '').' '.($draft?->lead_phone ?? ''))) }}"
                                required>

                            <!-- hidden fields submitted to backend -->
                            <input type="hidden" name="lead_ccode" id="lead_ccode" value="{{ old('lead_ccode', $draft->lead_ccode ?? '') }}">
                            <input type="hidden" name="lead_phone" id="lead_phone" value="{{ old('lead_phone', $draft->lead_phone ?? '') }}">

                            <div class="help mt-1"></div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">City <span class="req">*</span></label>
                            <input class="form-control" name="lead_city" maxlength="120"
                                value="{{ old('lead_city', $draft->lead_city ?? '') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">State <span class="req">*</span></label>
                            <input class="form-control" name="lead_state" maxlength="120"
                                value="{{ old('lead_state', $draft->lead_state ?? '') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Country <span class="req">*</span></label>
                            <select class="form-select" name="lead_country" required>
                                <option value="" disabled {{ old('lead_country', $draft->lead_country ?? '') ? '' : 'selected' }}>-- Select country --</option>
                                @foreach ($countryList as $code => $name)
                                <option value="{{ $name }}" {{ old('lead_country', $draft->lead_country ?? '') === $name ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Postal Code <span class="req">*</span></label>
                            <input class="form-control" name="lead_zip" maxlength="30"
                                value="{{ old('lead_zip', $draft->lead_zip ?? '') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Address <span class="req">*</span></label>
                            <textarea class="form-control" name="lead_addr" rows="3" required>{{ old('lead_addr', $draft->lead_addr ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Presenter --}}
                <div class="card-soft mb-3">
                    <h2 class="h5 fw-bold mb-3">Poster Presenter</h2>
                    <div class="d-flex justify-content-end mb-2">
                        <button type="button" class="btn btn-outline-primary btn-sm" id="copyLeadToPresenterBtn">
                            Copy from lead author information
                        </button>
                    </div>


                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Name of Poster Presenter <span class="req">*</span></label>
                            <input class="form-control" name="pp_name" maxlength="200"
                                value="{{ old('pp_name', $draft->pp_name ?? '') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Email <span class="req">*</span></label>
                            <input class="form-control" type="email" name="pp_email" maxlength="200"
                                value="{{ old('pp_email', $draft->pp_email ?? '') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">College Name/Organisation Name <span class="req">*</span></label>
                            <input class="form-control" name="pp_org" maxlength="250"
                                value="{{ old('pp_org', $draft->pp_org ?? '') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Website</label>
                            <input class="form-control" name="pp_website" maxlength="255"
                                value="{{ old('pp_website', $draft->pp_website ?? '') }}" placeholder="https://example.com">
                        </div>

                        <!-- <div class="col-md-4">
                            <label class="form-label">Country Code (optional)</label>
                            <input class="form-control" name="pp_ccode" maxlength="5"
                                value="{{ old('pp_ccode', $draft->pp_ccode ?? '') }}" placeholder="+91">
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Phone <span class="req">*</span></label>
                            <input class="form-control" name="pp_phone" maxlength="15"
                                value="{{ old('pp_phone', $draft->pp_phone ?? '') }}" required>
                        </div> -->

                        <div class="col-md-4">
                            <label class="form-label">Mobile Number <span class="req">*</span></label>

                            <input
                                class="form-control"
                                id="pp_phone_input"
                                type="tel"
                                placeholder="Enter phone number"
                                value="{{ old('pp_phone_input', trim(($draft?->pp_ccode ?? '').' '.($draft?->pp_phone ?? ''))) }}"
                                required>

                            <input type="hidden" name="pp_ccode" id="pp_ccode" value="{{ old('pp_ccode', $draft->pp_ccode ?? '') }}">
                            <input type="hidden" name="pp_phone" id="pp_phone" value="{{ old('pp_phone', $draft->pp_phone ?? '') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">City <span class="req">*</span></label>
                            <input class="form-control" name="pp_city" maxlength="120"
                                value="{{ old('pp_city', $draft->pp_city ?? '') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">State <span class="req">*</span></label>
                            <input class="form-control" name="pp_state" maxlength="120"
                                value="{{ old('pp_state', $draft->pp_state ?? '') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Country <span class="req">*</span></label>
                            <select class="form-select" name="pp_country" required>
                                <option value="" disabled {{ old('pp_country', $draft->pp_country ?? '') ? '' : 'selected' }}>-- Select country --</option>
                                @foreach ($countryList as $code => $name)
                                <option value="{{ $name }}" {{ old('pp_country', $draft->pp_country ?? '') === $name ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Postal Code <span class="req">*</span></label>
                            <input class="form-control" name="pp_zip" maxlength="30"
                                value="{{ old('pp_zip', $draft->pp_zip ?? '') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Address <span class="req">*</span></label>
                            <textarea class="form-control" name="pp_addr" rows="3" required>{{ old('pp_addr', $draft->pp_addr ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Co Authors --}}
                <div class="card-soft mb-3">
                    <h2 class="h5 fw-bold mb-3">Co-Authors</h2>
                    <div class="row g-3">
                        @for ($i=1; $i<=4; $i++)
                            <div class="col-md-4">
                            <label class="form-label">Co-Author {{ $i }}</label>
                            <input class="form-control" name="co_auth_name_{{ $i }}" maxlength="200"
                                value="{{ old('co_auth_name_'.$i, $draft->{'co_auth_name_'.$i} ?? '') }}">
                    </div>
                    @endfor
                </div>

                <hr class="my-4">

                <h2 class="h5 fw-bold mb-3">Please enter details of Accompanying Co-Author(s) at event</h2>
                <div class="alert alert-warning py-2 mb-2">
                    There will be a additional charges for each accompanying co-author.
                </div>
                <div class="d-flex justify-content-end mb-2">
                    <button type="button" class="btn btn-outline-primary btn-sm" id="copyCoToAccBtn">
                        Copy from co-authors
                    </button>
                </div>

                <div class="row g-3">
                    @for ($i=1; $i<=4; $i++)
                        <div class="col-md-4">
                        <label class="form-label">Accompanying Co-Author {{ $i }}</label>
                        <input class="form-control" name="acc_co_auth_name_{{ $i }}" maxlength="200"
                            value="{{ old('acc_co_auth_name_'.$i, $draft->{'acc_co_auth_name_'.$i} ?? '') }}">
                </div>
                @endfor
        </div>
    </div>

    {{-- Theme/Abstract/Files --}}
    <div class="card-soft mb-3">
        <h2 class="h5 fw-bold mb-3">Theme, Abstract & Files</h2>

        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">Theme</label>
                <input class="form-control" name="theme" maxlength="150"
                    value="{{ old('theme', $draft->theme ?? '') }}">
            </div>

            <div class="col-12">
                <label class="form-label">Abstract <span class="req">*</span></label>
                <textarea class="form-control" id="abstract_text" name="abstract_text" rows="4" required>{{ old('abstract_text', $draft->abstract_text ?? '') }}</textarea>
                <div class="help mt-1">
                    Word count: <b id="abstract_word_count">0</b> (minimum 250 words)
                </div>
                <!-- <div class="help mt-1">Required field.</div> -->
            </div>

            <div class="col-md-4">
                <label class="form-label">Abstract / Description of the Session <span class="req">*</span></label>
                <!-- <input class="form-control" type="file" name="sess_abstract" accept=".doc,.docx,.pdf"> -->
                <input class="form-control" type="file" name="sess_abstract" accept=".doc,.docx,.pdf"
                    {{ empty($draft?->sess_abstract_path) ? 'required' : '' }}>
                @if (!empty($draft?->sess_abstract_path))
                <div class="help mt-1">
                    Uploaded file: {{ $draft->sess_abstract_original_name ?? 'file' }}
                </div>
                @else
                <div class="help mt-1">Allowed: doc, docx, pdf. Max: 2MB</div>
                @endif
            </div>

            <div class="col-md-4">
                <label class="form-label">Lead Author CV <span class="req">*</span></label>
                <!-- <input class="form-control" type="file" name="lead_auth_cv" accept=".doc,.docx,.pdf"> -->
                <input class="form-control" type="file" name="lead_auth_cv" accept=".doc,.docx,.pdf"
                    {{ empty($draft?->lead_auth_cv_path) ? 'required' : '' }}>

                @if (!empty($draft?->lead_auth_cv_path))
                <div class="help mt-1">
                    Uploaded file: {{ $draft->lead_auth_cv_original_name ?? 'file' }}
                </div>
                @else
                <div class="help mt-1">Allowed: doc, docx, pdf. Max: 2MB</div>
                @endif
            </div>
        </div>
    </div>



    {{-- Payment Fields --}}
    <div class="card-soft mb-3">
        <h2 class="h5 fw-bold mb-3">Payment Mode</h2>

        <div class="row g-3">
            {{-- Payment --}}
            <div class="col-12">
                {{-- Payment Mode --}}
                <div class="mb-3">
                    <label class="form-label">Payment Mode <span class="req">*</span></label>
                    <select class="form-select" name="paymode" id="paymode" required>
                        <option value="" disabled selected>-- Select payment mode --</option>
                        {{-- options inserted by JS --}}
                    </select>
                </div>

                {{-- Price Calculation --}}
                <div class="price-box">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="fw-bold" style="color: #6d28d9; font-size: 1.1rem;">Price Calculation</div>
                        <div class="badge-soft" id="calc-badge">Select nationality</div>
                    </div>

                    <div class="price-line">
                        <div class="price-label">Currency</div>
                        <div class="price-value" id="calc-currency">—</div>
                    </div>
                    <div class="price-line">
                        <div class="price-label">Base Price</div>
                        <div class="price-value" id="calc-base">—</div>
                    </div>
                    <div class="price-line">
                        <div class="price-label">Additional Charges</div>
                        <div class="price-value" id="calc-additional">—</div>
                    </div>
                    <div class="price-line" hidden>
                        <div class="price-label">Discount</div>
                        <div class="price-value" id="calc-discount">—</div>
                    </div>
                    <div class="price-line">
                        <div class="price-label">GST (18%)</div>
                        <div class="price-value" id="calc-gst">—</div>
                    </div>
                    <div class="price-line">
                        <div class="price-label" id="calc-proc-label">Processing Charges</div>
                        <div class="price-value" id="calc-proc">—</div>
                    </div>
                    <div class="price-line price-total">
                        <div class="price-label">Total Price</div>
                        <div class="price-value" id="calc-total">—</div>
                    </div>
                </div>

                {{-- Hidden payment fields to store in DB --}}
                <input type="hidden" name="currency" id="currency" value="{{ old('currency', $draft?->currency ?? '') }}">
                <input type="hidden" name="base_amount" id="base_amount" value="{{ old('base_amount', $draft?->base_amount ?? '') }}">
                <input type="hidden" name="discount_code" id="discount_code" value="{{ old('discount_code', $draft?->discount_code ?? '') }}">
                <input type="hidden" name="discount_amount" id="discount_amount" value="{{ old('discount_amount', $draft?->discount_amount ?? '') }}">
                <input type="hidden" name="gst_amount" id="gst_amount" value="{{ old('gst_amount', $draft?->gst_amount ?? '') }}">
                <input type="hidden" name="processing_fee" id="processing_fee" value="{{ old('processing_fee', $draft?->processing_fee ?? '') }}">
                <input type="hidden" name="total_amount" id="total_amount" value="{{ old('total_amount', $draft?->total_amount ?? '') }}">

                <input type="hidden" name="acc_count" id="acc_count" value="{{ old('acc_count', $draft->acc_count ?? 0) }}">
                <input type="hidden" name="acc_unit_cost" id="acc_unit_cost" value="{{ old('acc_unit_cost', $draft->acc_unit_cost ?? 0) }}">
                <input type="hidden" name="additional_charge" id="additional_charge" value="{{ old('additional_charge', $draft->additional_charge ?? 0) }}">
            </div>



        </div>
    </div>
    {{-- You can add hidden inputs here later when UI sets them. --}}

    <div class="d-flex justify-content-end gap-2">
        <button type="submit" class="btn btn-primary btn-lg">
            Continue to Preview
        </button>
    </div>
    </form>
    </div>
    </div>

    <!-- JS for phone input -->
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@25.15.0/build/js/intlTelInput.min.js"></script>
    <script>
        function setupIntlTel(inputId, hiddenCodeId, hiddenPhoneId, preferredCountries = ["in", "us", "gb"]) {
            const input = document.getElementById(inputId);
            if (!input) return;

            const iti = window.intlTelInput(input, {
                initialCountry: "in",
                preferredCountries,
                separateDialCode: true,
                nationalMode: true,
                loadUtils: () => import("https://cdn.jsdelivr.net/npm/intl-tel-input@25.15.0/build/js/utils.js"),
            });

            const hiddenCode = document.getElementById(hiddenCodeId);
            const hiddenPhone = document.getElementById(hiddenPhoneId);

            const sync = () => {
                const data = iti.getSelectedCountryData();
                // dialCode is number only, store as +NN
                hiddenCode.value = data?.dialCode ? `+${data.dialCode}` : "";
                // store only digits for phone (national number)
                hiddenPhone.value = (input.value || "").replace(/\D/g, "");
            };

            // sync on input & country change
            input.addEventListener("input", sync);
            input.addEventListener("countrychange", sync);

            // sync immediately (handles prefilled edit mode)
            sync();

            return iti;
        }

        setupIntlTel("lead_phone_input", "lead_ccode", "lead_phone");
        setupIntlTel("pp_phone_input", "pp_ccode", "pp_phone");
    </script>
    <!-- JS for Payment and detail and tarrif table -->
    <script>
        (function() {
            const INR_BASE = 3000;
            const USD_BASE = 50;

            const GST_RATE = 0.18;
            const PROC_IN = 0.03; // India
            const PROC_INT = 0.09; // International

            const el = (id) => document.getElementById(id);

            function getNationality() {
                const checked = document.querySelector('input[name="nationality"]:checked');
                return checked ? checked.value : null;
            }

            function fmt(currency, amount) {
                const n = Number(amount || 0);
                const fixed = n.toFixed(2);
                if (currency === 'INR') return `₹${fixed}`;
                if (currency === 'USD') return `$${fixed}`;
                return fixed;
            }

            // Inject a small CSS block once (for highlight styling)
            function setTariffHighlight(nat) {
                const tariffContainer = document.getElementById('tariff-table-container');
                const indiaRow = el('tariff-row-india');
                const intlRow = el('tariff-row-intl');
                const helpText = document.getElementById('nationality-help');
                const noteProcessing = document.getElementById('tariff-note-processing');
                
                if (!indiaRow || !intlRow || !tariffContainer) return;

                // Hide table by default
                tariffContainer.style.display = 'none';
                indiaRow.style.display = 'none';
                intlRow.style.display = 'none';

                if (!nat) {
                    // No selection - hide table
                    if (helpText) helpText.textContent = 'Select your nationality to see applicable pricing';
                    return;
                }

                // Show table when nationality is selected
                tariffContainer.style.display = 'block';
                tariffContainer.style.animation = 'fadeIn 0.5s ease-out';
                
                if (nat === 'India') {
                    // Show only India row
                    indiaRow.style.display = 'table-row';
                    indiaRow.style.background = '#faf5ff';
                    indiaRow.style.outline = '3px solid #8b5cf6';
                    indiaRow.style.boxShadow = '0 4px 16px rgba(139, 92, 246, 0.3)';
                    
                    // Ensure text is black
                    const indiaCells = indiaRow.querySelectorAll('td');
                    indiaCells.forEach(cell => {
                        cell.style.color = '#000000';
                        const strong = cell.querySelector('strong');
                        if (strong) strong.style.color = '#000000';
                    });
                    
                    // Hide International row
                    intlRow.style.display = 'none';
                    
                    // Update note
                    if (noteProcessing) {
                        noteProcessing.textContent = '- Processing charges: 3% (India / CCAvenue).';
                        noteProcessing.style.color = '#000000';
                    }
                    
                    if (helpText) helpText.innerHTML = '<span style="color: #6d28d9;">✓</span> India pricing selected: ₹3,000 base + 18% GST + 3% processing';
                }
                if (nat === 'International') {
                    // Show only International row
                    intlRow.style.display = 'table-row';
                    intlRow.style.background = '#faf5ff';
                    intlRow.style.outline = '3px solid #8b5cf6';
                    intlRow.style.boxShadow = '0 4px 16px rgba(139, 92, 246, 0.3)';
                    
                    // Ensure text is black
                    const intlCells = intlRow.querySelectorAll('td');
                    intlCells.forEach(cell => {
                        cell.style.color = '#000000';
                        const strong = cell.querySelector('strong');
                        if (strong) strong.style.color = '#000000';
                    });
                    
                    // Hide India row
                    indiaRow.style.display = 'none';
                    
                    // Update note
                    if (noteProcessing) {
                        noteProcessing.textContent = '- Processing charges: 9% (International / PayPal).';
                        noteProcessing.style.color = '#000000';
                    }
                    
                    if (helpText) helpText.innerHTML = '<span style="color: #6d28d9;">✓</span> International pricing selected: $50 base + 18% GST + 9% processing';
                }
            }

            function setPaymodeOptions(nat) {
                const select = el('paymode');
                if (!select) return;

                select.innerHTML = `<option value="" disabled>-- Select payment mode --</option>`;

                if (nat === 'India') {
                    const opt = document.createElement('option');
                    opt.value = 'CCAvenue (Indian Payments)';
                    opt.textContent = 'CCAvenue (Indian Payments)';
                    select.appendChild(opt);
                    select.value = opt.value;
                } else if (nat === 'International') {
                    const opt = document.createElement('option');
                    opt.value = 'PayPal (International payments)';
                    opt.textContent = 'PayPal (International payments)';
                    select.appendChild(opt);
                    select.value = opt.value;
                } else {
                    // keep empty
                    select.selectedIndex = 0;
                }
            }

            function countAccompanying() {
                let count = 0;
                for (let i = 1; i <= 4; i++) {
                    const input = document.querySelector(`[name="acc_co_auth_name_${i}"]`);
                    if (input && input.value.trim() !== '') count++;
                }
                return count;
            }

            function safeNum(v) {
                const n = Number(v);
                return Number.isFinite(n) ? n : 0;
            }

            function recalc() {
                const nat = getNationality();

                // No nationality selected
                if (!nat) {
                    el('calc-badge').textContent = 'Select nationality';
                    el('calc-currency').textContent = '—';
                    el('calc-base').textContent = '—';
                    el('calc-additional').textContent = '—';
                    el('calc-gst').textContent = '—';
                    el('calc-proc').textContent = '—';
                    el('calc-total').textContent = '—';

                    // Clear hidden fields
                    el('currency').value = '';
                    el('base_amount').value = '';
                    el('gst_amount').value = '';
                    el('processing_fee').value = '';
                    el('total_amount').value = '';

                    el('acc_count').value = '0';
                    el('acc_unit_cost').value = '0.00';
                    el('additional_charge').value = '0.00';

                    setTariffHighlight(null);
                    setPaymodeOptions(null);
                    
                    // Hide price preview card
                    const previewCard = document.getElementById('price-preview-card');
                    if (previewCard) {
                        previewCard.style.display = 'none';
                    }
                    return;
                }

                setTariffHighlight(nat);
                setPaymodeOptions(nat);
                
                // Animate price box appearance
                const priceBox = document.querySelector('.price-box');
                if (priceBox) {
                    priceBox.style.animation = 'fadeIn 0.5s ease-out';
                }

                const currency = (nat === 'India') ? 'INR' : 'USD';
                const base = (nat === 'India') ? INR_BASE : USD_BASE;

                const discount = safeNum(el('discount_amount')?.value);

                // accompanying
                const accCount = countAccompanying();
                const accUnitCost = base; // current rule: unit cost = base
                const additional = accUnitCost * accCount;

                // subtotal = base + additional - discount (never negative)
                let subTotal = (base + additional) - discount;
                if (subTotal < 0) subTotal = 0;

                // GST 18% on subtotal
                const gst = subTotal * GST_RATE;

                // Processing charge on (subtotal + gst)
                const procRate = (nat === 'India') ? PROC_IN : PROC_INT;
                const procBase = subTotal + gst;
                const proc = procBase * procRate;

                const total = procBase + proc;

                // UI update
                el('calc-badge').textContent = nat;
                el('calc-currency').textContent = currency;
                el('calc-base').textContent = fmt(currency, base);
                el('calc-additional').textContent = fmt(currency, additional);
                el('calc-gst').textContent = fmt(currency, gst);

                el('calc-proc-label').textContent =
                    nat === 'India' ?
                    'Processing Charges (for India: 3.00%)' :
                    'Processing Charges (for International: 9.00%)';

                el('calc-proc').textContent = fmt(currency, proc);
                el('calc-total').textContent = fmt(currency, total);

                // Update price preview card
                const previewCard = document.getElementById('price-preview-card');
                const previewText = document.getElementById('price-preview-text');
                const previewAmount = document.getElementById('price-preview-amount');
                
                if (previewCard && previewText && previewAmount) {
                    previewCard.style.display = 'block';
                    previewText.textContent = `${nat} • Base: ${fmt(currency, base)} + GST + Processing`;
                    previewAmount.textContent = fmt(currency, total);
                    previewCard.style.animation = 'fadeIn 0.5s ease-out';
                }

                // Hidden fields (DB)
                el('currency').value = currency;
                el('base_amount').value = base.toFixed(2);

                el('acc_count').value = String(accCount);
                el('acc_unit_cost').value = accUnitCost.toFixed(2);
                el('additional_charge').value = additional.toFixed(2);

                el('gst_amount').value = gst.toFixed(2);
                el('processing_fee').value = proc.toFixed(2);
                el('total_amount').value = total.toFixed(2);
            }

            // nationality change
            document.querySelectorAll('input[name="nationality"]').forEach(r => {
                r.addEventListener('change', recalc);
            });

            // accompanying fields change
            for (let i = 1; i <= 4; i++) {
                const input = document.querySelector(`[name="acc_co_auth_name_${i}"]`);
                if (input) input.addEventListener('input', recalc);
            }

            // re-run after copy co-authors button
            const copyBtn = document.getElementById('copyCoToAccBtn');
            if (copyBtn) copyBtn.addEventListener('click', () => setTimeout(recalc, 0));

            // Run on load (edit mode)
            recalc();
        })();
    </script>

    <!-- Real time alter for email in lead auther -->
    <script>
        (function() {
            const emailInput = document.querySelector('input[name="lead_email"]');
            const alertBox = document.getElementById('lead-email-alert');

            if (!emailInput || !alertBox) return;

            let timer = null;
            let lastValue = '';

            function showAlert(type, message) {
                alertBox.className = 'alert alert-' + type;
                alertBox.textContent = message;
                alertBox.style.display = 'block';
            }

            function hideAlert() {
                alertBox.style.display = 'none';
                alertBox.textContent = '';
                alertBox.className = '';
            }

            async function checkEmail(email) {
                const url = new URL("{{ route('poster.checkEmail') }}", window.location.origin);
                url.searchParams.set('email', email);

                const res = await fetch(url.toString(), {
                    headers: {
                        "Accept": "application/json"
                    }
                });

                if (!res.ok) return null;
                return res.json();
            }

            emailInput.addEventListener('input', () => {
                const value = (emailInput.value || '').trim().toLowerCase();

                // don't spam
                if (value === lastValue) return;
                lastValue = value;

                // basic client-side email shape check
                if (!value || !value.includes('@') || !value.includes('.')) {
                    hideAlert();
                    return;
                }

                clearTimeout(timer);
                timer = setTimeout(async () => {
                    try {
                        const data = await checkEmail(value);
                        if (!data) return;

                        if (data.exists) {
                            showAlert('danger', 'This email is already registered. Please use another email.');
                        } else {
                            // showAlert('success', 'Email is available.');
                            // optional: auto-hide success after 1.5s
                            setTimeout(() => hideAlert(), 1500);
                        }
                    } catch (e) {
                        // fail silently (don't block user due to network error)
                        hideAlert();
                    }
                }, 350); // debounce delay
            });
        })();
    </script>
    <!-- Js for copy lead auther and co authors deatil -->
    <script>
        (function() {
            const $ = (name) => document.querySelector(`[name="${name}"]`);

            // Copy Lead -> Presenter
            const leadToPresenterBtn = document.getElementById('copyLeadToPresenterBtn');
            if (leadToPresenterBtn) {
                leadToPresenterBtn.addEventListener('click', () => {
                    // text fields
                    $('pp_name').value = $('lead_name').value || '';
                    $('pp_email').value = $('lead_email').value || '';
                    $('pp_org').value = $('lead_org').value || '';

                    // phone hidden fields (intl-tel-input)
                    const leadCode = document.getElementById('lead_ccode')?.value || '';
                    const leadPhone = document.getElementById('lead_phone')?.value || '';

                    if (document.getElementById('pp_ccode')) document.getElementById('pp_ccode').value = leadCode;
                    if (document.getElementById('pp_phone')) document.getElementById('pp_phone').value = leadPhone;

                    // Also update visible phone input (best-effort)
                    const ppVisible = document.getElementById('pp_phone_input');
                    if (ppVisible) ppVisible.value = leadPhone;

                    // address fields
                    $('pp_addr').value = $('lead_addr').value || '';
                    $('pp_city').value = $('lead_city').value || '';
                    $('pp_state').value = $('lead_state').value || '';
                    $('pp_country').value = $('lead_country').value || '';
                    $('pp_zip').value = $('lead_zip').value || '';
                });
            }

            // Copy Co-Authors -> Accompanying Co-Authors
            const coToAccBtn = document.getElementById('copyCoToAccBtn');
            if (coToAccBtn) {
                coToAccBtn.addEventListener('click', () => {
                    for (let i = 1; i <= 4; i++) {
                        const co = $(`co_auth_name_${i}`);
                        const acc = $(`acc_co_auth_name_${i}`);
                        if (co && acc) acc.value = co.value || '';
                    }
                });
            }
        })();
    </script>
    <!-- JS for real time count the words for abstract text-->
    <script>
        (function() {
            const textarea = document.getElementById('abstract_text');
            const counter = document.getElementById('abstract_word_count');
            if (!textarea || !counter) return;

            function countWords(text) {
                // split by whitespace, ignore empty
                return (text || '')
                    .trim()
                    .split(/\s+/)
                    .filter(Boolean)
                    .length;
            }

            function update() {
                const n = countWords(textarea.value);
                counter.textContent = n;
                if (n >= 250) {
                    counter.style.color = '#6d28d9';
                    counter.style.fontWeight = '700';
                } else {
                    counter.style.color = '#dc2626';
                    counter.style.fontWeight = '700';
                }
            }

            textarea.addEventListener('input', update);
            update();
        })();
    </script>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@25.15.0/build/js/intlTelInput.min.js"></script>
    <script>
        // Initialize intl-tel-input for phone fields
        document.addEventListener('DOMContentLoaded', function() {
            const leadPhoneInput = document.getElementById('lead_phone_input');
            const ppPhoneInput = document.getElementById('pp_phone_input');
            
            if (leadPhoneInput) {
                const leadIti = window.intlTelInput(leadPhoneInput, {
                    initialCountry: 'in',
                    separateDialCode: true,
                    utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@25.15.0/build/js/utils.js'
                });
                
                leadPhoneInput.addEventListener('countrychange', function() {
                    document.getElementById('lead_ccode').value = '+' + leadIti.getSelectedCountryData().dialCode;
                });
            }
            
            if (ppPhoneInput) {
                const ppIti = window.intlTelInput(ppPhoneInput, {
                    initialCountry: 'in',
                    separateDialCode: true,
                    utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@25.15.0/build/js/utils.js'
                });
                
                ppPhoneInput.addEventListener('countrychange', function() {
                    document.getElementById('pp_ccode').value = '+' + ppIti.getSelectedCountryData().dialCode;
                });
            }
        });
    </script>
@endpush