@extends('layouts.users')
@section('title', 'Applicant Details ')
@section('content')
    <div class="container-fluid py-2">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="text-end mb-2" style="margin-right: 5px;">
                    <a href="javascript:" class="btn bg-gradient-dark ms-auto mb-0 text-end">Proforma Invoice</a>
                </div>
                <div class="card mb-4">
                    <div class="card-header p-3 pb-0">

                        <div class="d-flex justify-content-between align-items-center">
                            <div class="w-50">
                                <h6>Exhibitor Order Details</h6>
                                <p class="text-sm mb-0">
                                    Application No: <b>{{$application->application_id}}</b> <br> Date:
                                    <b>{{$application->approved_date}}</b>
                                </p>
                                {{--                            <p class="text-sm">--}}
                                {{--                                Code: <b>KF332</b>--}}
                                {{--                            </p>--}}
                            </div>

                        </div>
                    </div>
                    <div class="card-body p-3 pt-0">
                        <hr class="horizontal dark mt-0 mb-4">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-12">
                                <div class="d-flex">
                                    <div>
                                        <h6 class="text-lg mb-0 mt-2">{{$application->company_name}}</h6>
                                        <span class="mb-2 text-xs">Type of Business: <span
                                                class="text-dark font-weight-bold ms-2">{{$application->type_of_business}}</span></span><br>
                                        <span class="mb-2 text-xs">Product Category: <span
                                                class="text-dark font-weight-bold ms-2">{{$application->main_product_category}}</span></span><br>
                                        <span class="mb-2 text-xs">Sector(s): <span
                                                class="text-dark font-weight-bold ms-2">
                                                @foreach($application->sectors as $sector)
                                                    {{ $sector['name'] }}@if(!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
</span></span><br>

                                        <span class="mb-2 text-xs">Pay Status:</span>
                                        <span
                                            class="badge badge-sm bg-gradient-success">{{$application->invoices->payment_status}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-12 my-auto text-start">
                                <ul class="list-group">
                                    <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-3 text-sm">Order Details </h6>
                                            <span class="mb-2 text-xs">Stall Category: <span
                                                    class="text-dark font-weight-bold ms-2">{{$application->stall_category}}</span></span>
                                            <span class="mb-2 text-xs">Booth Size: <span
                                                    class="text-dark ms-2 font-weight-bold">{{$application->interested_sqm}} sqm</span></span>
                                            {{--                                    <span class="text-xs">GST Number: <span class="text-dark ms-2 font-weight-bold">FRB1235476</span></span>--}}
                                        </div>
                                    </li>
                                </ul>
                                {{--                            <a href="javascript:;" class="btn bg-gradient-dark btn-sm mb-0">Contact Us</a>--}}
                                {{--                            <p class="text-sm mt-2 mb-0">Do you like the product? Leave us a review <a href="javascript:;">here</a>.</p>--}}
                            </div>
                        </div>
                        <hr class="horizontal dark mt-4 mb-4">
                        <div class="row">
                            {{--                            <div class="col-lg-3 col-md-6 col-12">--}}
                            {{--                                <h6 class="mb-3">Track order</h6>--}}
                            {{--                                <div class="timeline timeline-one-side">--}}
                            {{--                                    <div class="timeline-block mb-3">--}}
                            {{--                      <span class="timeline-step">--}}
                            {{--                        <i class="material-symbols-rounded text-secondary text-lg">notifications</i>--}}
                            {{--                      </span>--}}
                            {{--                                        <div class="timeline-content">--}}
                            {{--                                            <h6 class="text-dark text-sm font-weight-bold mb-0">Order received</h6>--}}
                            {{--                                            <p class="text-secondary font-weight-normal text-xs mt-1 mb-0">22 DEC 7:20--}}
                            {{--                                                AM</p>--}}
                            {{--                                        </div>--}}
                            {{--                                    </div>--}}
                            {{--                                    <div class="timeline-block mb-3">--}}
                            {{--                      <span class="timeline-step">--}}
                            {{--                        <i class="material-symbols-rounded text-secondary text-lg">code</i>--}}
                            {{--                      </span>--}}
                            {{--                                        <div class="timeline-content">--}}
                            {{--                                            <h6 class="text-dark text-sm font-weight-bold mb-0">Generate order id--}}
                            {{--                                                #1832412</h6>--}}
                            {{--                                            <p class="text-secondary font-weight-normal text-xs mt-1 mb-0">22 DEC 7:21--}}
                            {{--                                                AM</p>--}}
                            {{--                                        </div>--}}
                            {{--                                    </div>--}}
                            {{--                                    <div class="timeline-block mb-3">--}}
                            {{--                      <span class="timeline-step">--}}
                            {{--                        <i class="material-symbols-rounded text-secondary text-lg">shopping_cart</i>--}}
                            {{--                      </span>--}}
                            {{--                                        <div class="timeline-content">--}}
                            {{--                                            <h6 class="text-dark text-sm font-weight-bold mb-0">Order transmited to--}}
                            {{--                                                courier</h6>--}}
                            {{--                                            <p class="text-secondary font-weight-normal text-xs mt-1 mb-0">22 DEC 8:10--}}
                            {{--                                                AM</p>--}}
                            {{--                                        </div>--}}
                            {{--                                    </div>--}}
                            {{--                                    <div class="timeline-block mb-3">--}}
                            {{--                      <span class="timeline-step">--}}
                            {{--                        <i class="material-symbols-rounded text-success text-gradient text-lg">done</i>--}}
                            {{--                      </span>--}}
                            {{--                                        <div class="timeline-content">--}}
                            {{--                                            <h6 class="text-dark text-sm font-weight-bold mb-0">Order delivered</h6>--}}
                            {{--                                            <p class="text-secondary font-weight-normal text-xs mt-1 mb-0">22 DEC 4:54--}}
                            {{--                                                PM</p>--}}
                            {{--                                        </div>--}}
                            {{--                                    </div>--}}
                            {{--                                </div>--}}
                            {{--                            </div>--}}
                            <div class="col-lg-5 col-md-6 col-12">
                                {{--                                <h6 class="mb-3">Payment details</h6>--}}
                                {{--                                <div--}}
                                {{--                                    class="card card-body border card-plain border-radius-lg d-flex align-items-center flex-row">--}}
                                {{--                                    <img class="w-10 me-3 mb-0" src="../../../assets/img/logos/mastercard.png"--}}
                                {{--                                         alt="logo">--}}
                                {{--                                    <h6 class="mb-0">****&nbsp;&nbsp;&nbsp;****&nbsp;&nbsp;&nbsp;****&nbsp;&nbsp;&nbsp;7852</h6>--}}
                                {{--                                    <button type="button"--}}
                                {{--                                            class="btn btn-icon-only btn-rounded btn-outline-secondary mb-0 ms-2 btn-sm d-flex align-items-center justify-content-center ms-auto"--}}
                                {{--                                            data-bs-toggle="tooltip" data-bs-placement="bottom" title=""--}}
                                {{--                                            data-bs-original-title="We do not store card details">--}}
                                {{--                                        <i class="material-symbols-rounded text-sm" aria-hidden="true">priority_high</i>--}}
                                {{--                                    </button>--}}
                                {{--                                </div>--}}
                                <h6 class="mb-3 mt-4">Billing Information</h6>
                                <ul class="list-group">
                                    <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-3 text-sm">{{$application->billingDetail->contact_name}}</h6>
                                            <span class="mb-2 text-xs">Company Name: <span
                                                    class="text-dark font-weight-bold ms-2">{{$application->billingDetail->billing_company}}</span></span>
                                            <span class="mb-2 text-xs">Email Address: <span
                                                    class="text-dark ms-2 font-weight-bold">{{$application->billingDetail->email}}</span></span>
                                            <span class="mb-2 text-xs">Mobile No: <span
                                                    class="text-dark ms-2 font-weight-bold">{{$application->billingDetail->phone}}</span></span>
                                            <span class="mb-2 text-xs">Address: <span
                                                    class="text-dark ms-2 font-weight-bold">{{$application->billingDetail->address}}, {{$application->billingDetail->city_id}}, {{$application->billingDetail->state->name}}, {{$application->billingDetail->country->name}}  </span></span>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-lg-5 col-12 ms-auto">
                                <h6 class="mb-3">Order Summary</h6>
                                <div class="d-flex justify-content-between">
                    <span class="mb-2 text-sm">
                      Product Price:
                    </span>
                                    <span
                                        class="text-dark font-weight-bold ms-2">{{$application->invoices->currency}} {{$application->invoices->price}}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                    <span class="mb-2 text-sm">
                      Processing Charge:
                    </span>
                                    <span
                                        class="text-dark ms-2 font-weight-bold">{{$application->invoices->currency}} {{$application->invoices->processing_charges}}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                    <span class="text-sm">
                      Taxes:
                    </span>
                                    <span
                                        class="text-dark ms-2 font-weight-bold">{{$application->invoices->currency}} {{$application->invoices->gst}}</span>
                                </div>
                                <div class="d-flex justify-content-between mt-4">
                    <span class="mb-2 text-lg">
                      Total:
                    </span>
                                    <span
                                        class="text-dark text-lg ms-2 font-weight-bold">{{$application->invoices->currency}} {{$application->invoices->amount}}</span>
                                </div>
                                @if($application->invoices->payment_status == 'partial')
                                    <div class="d-flex justify-content-between mt-4">
                    <span class="text-sm">
                      Total Pending Amount Payable :
                    </span>
                                        <span
                                            class="text-dark ms-2 font-weight-bold">{{$application->invoices->pending_amount}}</span>
                                    </div>
                                @endif
                                @if($application->invoices->partial_payment_percentage > 0)

                                    <div class="d-flex justify-content-between mt-4">
                    <span class="text-sm">
                      Total {{$application->invoices->partial_payment_percentage}}% Partial Amount Payable :
                    </span>
                                        <span
                                            class="text-dark ms-2 font-weight-bold">{{$application->invoices->currency}} {{$application->invoices->total_final_price}}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end" style="margin-right: 5px;">
                    <a href="#" class="btn bg-gradient-primary" data-bs-toggle="modal" data-bs-target="#paymentModal">Pay
                        Now</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Payment Options</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Please select your payment option:</p>
                    <form action="{{ route('payment.full') }}" method="POST" style="display:inline;">
                        @csrf
                        <input type="hidden" name="application_no" value="{{ $application->application_id }}">

                        <button type="submit" class="btn btn-primary" onclick="window.location.href='#'">Pay in Full

                        </button>
                    </form>
                    <form action="{{ route('payment.partial') }}" method="POST" style="display:inline;">
                        @csrf
                        <input type="hidden" name="application_no" value="{{ $application->application_id }}">
                        <button type="submit" class="btn btn-secondary">Partial Payment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
