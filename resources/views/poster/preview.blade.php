@extends('layouts.registration')

@section('title', 'Poster Registration Preview - ' . config('constants.EVENT_NAME', 'Bengaluru Tech Summit') . ' ' . config('constants.EVENT_YEAR', '2026'))

@push('head-links')
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
            width: 66.66%;
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

        .block {
            border: none;
            border-radius: 0;
            padding: 0;
            background: transparent;
            margin-bottom: 0;
        }

        .glass {
            background: #ffffff;
            border: 3px solid #8b5cf6;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .glass:hover {
            border-color: #6d28d9;
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.2);
        }

        .section-card {
            padding: 28px;
            margin-bottom: 24px;
        }

        .section-title {
            font-family: 'Varela Round', sans-serif;
            font-weight: 700;
            color: #6d28d9;
            margin: 0 0 24px 0;
            font-size: 1.4rem;
            border-bottom: 3px solid #e9d5ff;
            padding-bottom: 12px;
        }

        .sep {
            height: 2px;
            background: #e5e7eb;
            margin: 32px 0;
            opacity: 0.5;
        }

        .info-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            overflow: hidden;
            border-radius: 16px;
            border: 2px solid #e5e7eb;
            background: #ffffff;
        }

        .info-table tr:not(:last-child) td {
            border-bottom: 2px solid #e5e7eb;
        }

        .info-table td {
            padding: 14px 16px;
            vertical-align: top;
        }

        .label-cell {
            width: 34%;
            color: #000000;
            font-weight: 700;
            background: #faf5ff;
        }

        .value-cell {
            color: #000000;
            font-weight: 600;
        }

        .value-cell a {
            text-decoration: none;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            border: 2px solid rgba(139, 92, 246, 0.3);
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);
            color: #6d28d9;
        }

        .pill-blue {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);
            border-color: rgba(139, 92, 246, 0.3);
            color: #6d28d9;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 12px;
        }

        .btn-soft {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);
            border: 2px solid rgba(139, 92, 246, 0.3);
            color: #6d28d9;
            font-weight: 800;
        }

        .btn-soft:hover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.25) 0%, rgba(118, 75, 162, 0.25) 100%);
            border-color: #8b5cf6;
        }

        .muted {
            color: #000000;
            font-weight: 600;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 700;
            transition: all 0.3s ease;
            box-shadow: 0 4px 16px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.4);
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        .btn-success {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            padding: 14px 32px;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 16px rgba(102, 126, 234, 0.3);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.4);
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        .btn-outline-secondary {
            border: 2px solid #8b5cf6;
            color: #8b5cf6;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
            background: transparent;
        }

        .btn-outline-secondary:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #8b5cf6;
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .alert-warning {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            border-left: 4px solid #f59e0b;
            border-radius: 12px;
            border: none;
            padding: 16px 20px;
        }

        @media (max-width: 768px) {
            .panel {
                padding: 24px 16px;
                margin: 10px 0;
                border-radius: 16px;
            }

            .step-header {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .section-card {
                padding: 20px 16px;
            }

            .panel h1 {
                font-size: 1.5rem;
            }

            .label-cell {
                width: 45%;
            }

            .info-table {
                font-size: 0.875rem;
            }

            .info-table td {
                padding: 10px 12px;
            }
        }

        @media (max-width: 576px) {
            .panel {
                padding: 20px 12px;
            }

            .section-card {
                padding: 16px 12px;
            }

            .section-title {
                font-size: 1.1rem;
            }

            .label-cell {
                width: 40%;
                font-size: 0.85rem;
            }

            .value-cell {
                font-size: 0.85rem;
            }

            .btn-success,
            .btn-outline-secondary {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
@endpush

@section('header-title')
    {{-- Header title removed as requested --}}
@endsection

@section('content')

    @php
    // Safe helpers for view
    $leadPhone = trim(($draft?->lead_ccode ?? '').' '.($draft?->lead_phone ?? ''));
    $ppPhone = trim(($draft?->pp_ccode ?? '').' '.($draft?->pp_phone ?? ''));

    $sessUrl = $draft?->sess_abstract_path ? route('poster.downloadFile', ['type' => 'sess_abstract', 'token' => $draft->token]) : null;
    $cvUrl = $draft?->lead_auth_cv_path ? route('poster.downloadFile', ['type' => 'lead_auth_cv', 'token' => $draft->token]) : null;

    $hasCoAuthors =
    ($draft?->co_auth_name_1 || $draft?->co_auth_name_2 || $draft?->co_auth_name_3 || $draft?->co_auth_name_4);

    $hasAccCoAuthors =
    ($draft?->acc_co_auth_name_1 || $draft?->acc_co_auth_name_2 || $draft?->acc_co_auth_name_3 || $draft?->acc_co_auth_name_4);
    @endphp

    <div class="wrap">
        <div class="panel">
            <div class="text-center mb-4">
                <div style="font-family: 'Varela Round', sans-serif; font-size: 0.9rem; font-weight: 600; color: #8b5cf6; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 8px;">
                    Bengaluru Tech Summit 2026
                </div>
                <h1 class="h3 fw-bold mb-1">Preview</h1>
                <div class="text-secondary mb-3">Step 2 of 3 — Review and submit.</div>
            </div>

            <div class="step-progress-bar">
                <div class="progress-indicator"></div>
            </div>

            <div class="step-header">
                <div class="step-item">
                    <div class="step-number">1</div>
                    <div class="step-title">Poster Details</div>
                </div>
                <div class="step-item active">
                    <div class="step-number">2</div>
                    <div class="step-title">Preview</div>
                </div>
                <div class="step-item">
                    <div class="step-number">3</div>
                    <div class="step-title">Success</div>
                </div>
            </div>

            <div class="block mb-3">
                {{-- Core --}}
                <div class="glass section-card">
                    <h4 class="section-title">
                        Core Information
                    </h4>

                    <table class="info-table">
                        <tr>
                            <td class="label-cell">Sector</td>
                            <td class="value-cell">{{ $draft->sector }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Nationality</td>
                            <td class="value-cell">{{ $draft->nationality }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Title</td>
                            <td class="value-cell"><strong>{{ $draft->title }}</strong></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Theme</td>
                            <td class="value-cell">{{ $draft->theme ?: '—' }}</td>
                        </tr>
                    </table>
                </div>

                <div class="sep"></div>

                {{-- Lead Author --}}
                <div class="glass section-card">
                    <h4 class="section-title">
                        Lead Author
                    </h4>

                    <table class="info-table">
                        <tr>
                            <td class="label-cell">Name</td>
                            <td class="value-cell"><strong>{{ $draft->lead_name }}</strong></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Email</td>
                            <td class="value-cell">{{ $draft->lead_email }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">College Name/Organisation Name</td>
                            <td class="value-cell">{{ $draft->lead_org }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Mobile Number</td>
                            <td class="value-cell">{{ $leadPhone ?: '—' }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Address</td>
                            <td class="value-cell">{{ $draft->lead_addr }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">City</td>
                            <td class="value-cell">{{ $draft->lead_city }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">State</td>
                            <td class="value-cell">{{ $draft->lead_state }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Country</td>
                            <td class="value-cell">{{ $draft->lead_country }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Postal Code</td>
                            <td class="value-cell">{{ $draft->lead_zip }}</td>
                        </tr>
                    </table>
                </div>

                <div class="sep"></div>

                {{-- Poster Presenter --}}
                <div class="glass section-card">
                    <h4 class="section-title">
                        Poster Presenter
                    </h4>

                    <table class="info-table">
                        <tr>
                            <td class="label-cell">Name</td>
                            <td class="value-cell"><strong>{{ $draft->pp_name }}</strong></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Email</td>
                            <td class="value-cell">{{ $draft->pp_email }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">College Name/Organisation Name</td>
                            <td class="value-cell">{{ $draft->pp_org }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Mobile Number</td>
                            <td class="value-cell">{{ $ppPhone ?: '—' }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Website</td>
                            <td class="value-cell">
                                @if($draft->pp_website)
                                <a href="{{ $draft->pp_website }}" target="_blank">{{ $draft->pp_website }}</a>
                                @else
                                —
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="label-cell">Address</td>
                            <td class="value-cell">{{ $draft->pp_addr }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">City</td>
                            <td class="value-cell">{{ $draft->pp_city }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">State</td>
                            <td class="value-cell">{{ $draft->pp_state }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Country</td>
                            <td class="value-cell">{{ $draft->pp_country }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Postal Code</td>
                            <td class="value-cell">{{ $draft->pp_zip }}</td>
                        </tr>
                    </table>
                </div>

                <div class="sep"></div>

                {{-- Co-authors --}}
                <div class="glass section-card">
                    <h4 class="section-title">
                        Co-Authors
                    </h4>

                    <table class="info-table">
                        <tr>
                            <td class="label-cell">Co-Author 1</td>
                            <td class="value-cell">{{ $draft->co_auth_name_1 ?: '—' }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Co-Author 2</td>
                            <td class="value-cell">{{ $draft->co_auth_name_2 ?: '—' }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Co-Author 3</td>
                            <td class="value-cell">{{ $draft->co_auth_name_3 ?: '—' }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Co-Author 4</td>
                            <td class="value-cell">{{ $draft->co_auth_name_4 ?: '—' }}</td>
                        </tr>
                    </table>

                    @if(!$hasCoAuthors)
                    <div class="muted mt-2">No co-authors added.</div>
                    @endif
                </div>

                <div class="sep"></div>

                {{-- Accompanying Co-authors --}}
                <div class="glass section-card">
                    <h4 class="section-title">
                        Please enter details of Accompanying Co-Author(s) at event
                    </h4>

                    <table class="info-table">
                        <tr>
                            <td class="label-cell">Accompanying 1</td>
                            <td class="value-cell">{{ $draft->acc_co_auth_name_1 ?: '—' }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Accompanying 2</td>
                            <td class="value-cell">{{ $draft->acc_co_auth_name_2 ?: '—' }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Accompanying 3</td>
                            <td class="value-cell">{{ $draft->acc_co_auth_name_3 ?: '—' }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Accompanying 4</td>
                            <td class="value-cell">{{ $draft->acc_co_auth_name_4 ?: '—' }}</td>
                        </tr>
                    </table>

                    @if(!$hasAccCoAuthors)
                    <div class="muted mt-2">No accompanying co-authors added.</div>
                    @endif
                </div>

                <div class="sep"></div>

                {{-- Abstract + Session abstract --}}
                <div class="glass section-card">
                    <h4 class="section-title">
                        Abstract
                    </h4>

                    <table class="info-table">
                        <tr>
                            <td class="label-cell">Abstract Text</td>
                            <td class="value-cell" style="white-space: pre-wrap;">{{ $draft->abstract_text }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Abstract / Description of the Session</td>
                            <td class="value-cell">
                                @if($sessUrl)
                                <a class="btn btn-sm btn-primary file-btn" target="_blank" href="{{ $sessUrl }}">
                                    View Abstract / Session Description
                                </a>
                                <div class="muted mt-1">
                                    {{ $draft->sess_abstract_original_name }} ({{ number_format(($draft->sess_abstract_size ?? 0)/1024, 0) }} KB)
                                </div>
                                @else
                                <span class="muted">No file uploaded</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="label-cell">Lead Author CV</td>
                            <td class="value-cell">
                                @if($cvUrl)
                                <a class="btn btn-sm btn-primary file-btn" target="_blank" href="{{ $cvUrl }}">
                                    View CV
                                </a>
                                <div class="muted mt-1">
                                    {{ $draft->lead_auth_cv_original_name }} ({{ number_format(($draft->lead_auth_cv_size ?? 0)/1024, 0) }} KB)
                                </div>
                                @else
                                <span class="muted">No file uploaded</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="sep"></div>

                {{-- Payment --}}
                <div class="glass section-card">
                    <h4 class="section-title">
                        Payment Summary
                    </h4>

                    <table class="info-table">
                        <tr>
                            <td class="label-cell">Payment Mode</td>
                            <td class="value-cell">{{ $draft->paymode ?: '—' }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Currency</td>
                            <td class="value-cell">{{ $draft->currency ?: '—' }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Base Amount</td>
                            <td class="value-cell">{{ $draft->base_amount ?: '—' }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Additional Charges</td>
                            <td class="value-cell">{{ $draft->additional_charge ?: '—' }}</td>
                        </tr>
                        <tr hidden>
                            <td class="label-cell">Discount Code</td>
                            <td class="value-cell">{{ $draft->discount_code ?: '—' }}</td>
                        </tr>
                        <tr hidden>
                            <td class="label-cell">Discount Amount</td>
                            <td class="value-cell">{{ $draft->discount_amount ?: '—' }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">GST Amount</td>
                            <td class="value-cell">{{ $draft->gst_amount ?: '—' }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Processing Fee</td>
                            <td class="value-cell">{{ $draft->processing_fee ?: '—' }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Total Amount</td>
                            <td class="value-cell"><strong>{{ $draft->total_amount ?: '—' }}</strong></td>
                        </tr>
                    </table>
                </div>

                @if(session('error'))
                <div class="alert alert-danger mt-3 mb-0">
                    <strong>Error:</strong> {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success mt-3 mb-0">
                    {{ session('success') }}
                </div>
            @endif

            <div class="alert alert-warning mt-3 mb-0">
                    Please review all information carefully before submitting. Once submitted, changes cannot be made.
                </div>

            </div>

            <div class="d-flex flex-wrap gap-2 justify-content-between">
                <a class="btn btn-outline-secondary" href="{{ route('poster.register.edit', ['token' => $draft->token]) }}">
                    Back to Edit
                </a>

                <form method="POST" action="{{ route('poster.submit', ['token' => $draft->token]) }}">
                    @csrf
                    <button type="submit" class="btn btn-success btn-lg">
                        Proceed to Payment</button>
                </form>
            </div>

            <!-- <div class="text-secondary mt-3" style="font-size: 13px;">
            Submitting will finalize your registration and move it to the poster table. Your draft remains as backup.
        </div> -->
        </div>
    </div>
@endsection