@extends('layouts.startup-zone')

@section('title', 'Payment - ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-credit-card"></i> Payment</h4>
                </div>
                <div class="card-body">
                    {{-- Application Summary --}}
                    <div class="alert alert-info">
                        <strong>Application ID:</strong> {{ $application->application_id }}<br>
                        <strong>Company:</strong> {{ $application->company_name }}
                    </div>

                    {{-- Invoice Details --}}
                    <h5 class="mb-3">Invoice Details</h5>
                    <table class="table table-bordered mb-4">
                        <tr>
                            <td><strong>Base Price:</strong></td>
                            <td class="text-end">{{ $invoice->currency }} {{ number_format($invoice->price, 2) }}</td>
                        </tr>
                        <tr>
                            <td><strong>GST (18%):</strong></td>
                            <td class="text-end">{{ $invoice->currency }} {{ number_format($invoice->gst, 2) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Processing Charges ({{ $invoice->processing_chargesRate ?? 3 }}%):</strong></td>
                            <td class="text-end">{{ $invoice->currency }} {{ number_format($invoice->processing_charges, 2) }}</td>
                        </tr>
                        <tr class="table-success">
                            <td><strong>Total Amount:</strong></td>
                            <td class="text-end"><strong>{{ $invoice->currency }} {{ number_format($invoice->total_final_price, 2) }}</strong></td>
                        </tr>
                    </table>

                    @if($invoice->payment_status === 'paid')
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> Payment already completed!
                        </div>
                        <a href="{{ route('startup-zone.confirmation', $application->application_id) }}" class="btn btn-success">
                            View Confirmation <i class="fas fa-arrow-right"></i>
                        </a>
                    @else
                        {{-- Payment Options --}}
                        <h5 class="mb-3">Select Payment Method</h5>
                        
                        <form id="paymentForm" method="POST" action="{{ route('startup-zone.payment.process', $application->application_id) }}">
                            @csrf
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="ccavenue" 
                                           value="CCAvenue" {{ $invoice->currency === 'INR' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ccavenue">
                                        <strong>CCAvenue</strong> (Indian Payments - INR)
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="paypal" 
                                           value="PayPal" {{ $invoice->currency === 'USD' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="paypal">
                                        <strong>PayPal</strong> (International Payments - USD)
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="bank_transfer" 
                                           value="Bank Transfer">
                                    <label class="form-check-label" for="bank_transfer">
                                        <strong>Bank Transfer</strong>
                                    </label>
                                </div>
                            </div>

                            <div class="alert alert-warning">
                                <strong>Note:</strong> After clicking "Proceed to Payment", you will be redirected to the payment gateway.
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('startup-zone.preview', ['application_id' => $application->application_id]) }}" 
                                   class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                                <button type="submit" class="btn btn-success btn-lg">
                                    Proceed to Payment <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
