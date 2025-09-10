@extends('layouts.bills')
@section('title', 'Semicon India 2025')
@section('content')
{{--@php--}}
{{--print_r($sponsor);--}}
{{--@endphp--}}
    <div class="container-fluid my-3 py-3 mt-3">
        <div class="row">
            <div class="col-md-10 col-lg-8 col-sm-10 mx-auto">
                <form class="" action="#" method="post">
                    <div class="card my-sm-5">
                        <div class="card-header text-center">
                            <div class="row justify-content-between">
                                <div class="row">
                                    <div class="col text-start">
                                        <svg class="navbar-brand-img " width="100" height="50" viewBox="0 0 163 40"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M43.751 18.973c-2.003-.363-4.369-.454-7.009-.363-8.011 9.623-20.846 17.974-29.403 19.064-2.093.272-3.641.091-4.915-.454.819.726 2.184 1.362 4.096 1.725 8.193 1.634 23.213-1.544 33.499-7.081 10.286-5.538 12.016-11.348 3.732-12.891zm-31.587 2.996c8.557-5.175 19.662-8.897 29.129-10.077C45.299 4.357 43.387-.454 35.923.545c-9.012 1.18-22.758 10.439-30.586 20.607-5.735 7.444-6.737 13.254-3.46 15.523-2.366-3.54 1.275-9.169 10.287-14.706zm58.35-.726l-4.643-1.271c-1.274-.363-1.911-.908-1.911-1.634 0-1.271 2.184-1.907 4.278-1.907 1.912 0 3.186.636 4.187 1.09.638.272 1.184.544 1.73.544 1.457 0 1.73-.635 1.73-1.18l-.182-.635c-.82-1.09-4.37-1.998-8.102-1.998-3.641 0-7.373 1.635-7.373 4.267 0 2.27 2.184 3.177 4.096 3.722l5.28 1.453c1.547.454 3.004.907 3.004 2.178 0 1.18-1.639 2.361-4.734 2.361-2.458 0-4.005-.817-5.098-1.453-.728-.363-1.274-.726-1.82-.726-.82 0-1.639.726-1.639 1.271 0 1.271 3.55 3.086 8.466 3.086 5.189 0 8.648-1.906 8.648-4.629-.091-2.724-3.004-3.722-5.917-4.539zm22.757-6.991c-6.554 0-10.013 4.086-10.013 8.08 0 3.722 2.731 8.079 10.559 8.079 5.371 0 9.103-2.178 9.103-3.268 0-1.271-1.183-1.271-1.638-1.271-.546 0-1.092.273-1.73.727-1.183.726-2.822 1.634-5.917 1.634-3.823 0-6.281-2.361-6.554-4.721h13.928c1.547 0 2.276-.454 2.276-1.452-.091-3.813-3.187-7.808-10.014-7.808zm6.19 6.991h-12.38c.273-2.452 2.367-4.812 6.19-4.812 3.732 0 5.917 2.451 6.19 4.812zm53.253-6.991c-1.093 0-1.73.545-1.73 1.544v12.981c0 .999.637 1.544 1.73 1.544 1.092 0 1.729-.545 1.729-1.544V15.796c0-.999-.637-1.544-1.729-1.544zm-26.399 2.633c1.457-1.543 4.096-2.633 6.645-2.633 4.006 0 8.375 1.816 8.375 5.72v8.896c0 .999-.637 1.543-1.73 1.543-1.092 0-1.729-.544-1.729-1.543v-8.442c0-2.542-1.639-3.722-4.916-3.722-2.458 0-5.006 1.361-5.006 3.722v8.442c0 .999-.638 1.543-1.73 1.543s-1.73-.544-1.73-1.543v-8.442c0-2.452-2.639-3.813-5.006-3.813-3.368 0-4.916 1.271-4.916 3.813v8.442c0 .999-.637 1.543-1.729 1.543-1.093 0-1.73-.544-1.73-1.543v-8.896c0-3.904 4.37-5.72 8.375-5.72 2.64 0 5.189.999 6.645 2.633l.182.091v-.091zm33.044-1.906h-.455a.196.196 0 0 1-.182-.182c0-.091.091-.181.182-.181h1.365c.091 0 .182.09.182.181a.196.196 0 0 1-.182.182h-.455v1.634c0 .091-.091.181-.182.181-.182 0-.182-.09-.182-.181v-1.634h-.091zm1.365 0c0-.273.091-.363.273-.363.091 0 .273 0 .364.181l.547 1.362.455-1.362c.091-.181.182-.181.364-.181s.273.09.273.363v1.634c0 .091-.091.181-.182.181s-.182-.09-.182-.181V15.07l-.546 1.543c0 .181-.091.181-.182.181s-.182-.09-.182-.181l-.547-1.543v1.543c0 .091-.091.181-.182.181s-.182-.09-.182-.181v-1.634h-.091z"
                                                id="Shape" fill-rule="nonzero"></path>
                                        </svg>
                                    </div>
                                    <div class="col text-end mt-3">
                                        <h3 class="text-dark mb-0">Proforma Invoice</h3>
                                    </div>
                                </div>
                                <div class="col-md-4 text-start">
                                    <h6>
                                        SEMI India,<br>
                                        Delhi


                                    </h6>
                                    Email: <a href="mailto:semiconindia@semi.org">semiconindia@semi.org </a><br>
                                    tel: +4 (074) 1090873<br>
                                    GSTIN: 27AABCD7055L1ZE<br>
                                    PAN: AABCD7055L<br>
                                    CIN: U72200PN2001PTC16234
                                </div>
                                <div class="col-lg-3 col-md-7 text-md-end text-start">

                                    <h6 class="d-block mt-2 mb-0">Billed to: {{$billing->contact_name}}</h6>
                                    <p class="text-secondary">{{$billing->address}}<br>
                                        {{$billing->city_id}}, {{$billing->state->name}}<br>
                                        {{$billing->country->name}}
                                        <br>
                                        GSTIN: {{$applications->gst_no}}
                                    </p>
                                </div>
                            </div>
                            <br>
                            <div class="row justify-content-md-between">
                                <div class="col-md-4 mt-auto">
                                    <h6 class="mb-0 text-start text-secondary font-weight-normal">
                                        Invoice no
                                    </h6>
                                    <h5 class="text-start mb-0">
                                        #{{$invoice->invoice_no}}
                                    </h5>
                                </div>
                                <div class="col-lg-5 col-md-7 mt-auto">
                                    <div class="row mt-md-5 mt-4 text-md-end text-start">
                                        <div class="col-md-6">
                                            <h6 class="text-secondary font-weight-normal mb-0">Invoice date:</h6>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-dark mb-0">{{ $invoice->created_at->format('d/m/Y') }}</h6>
                                        </div>
                                    </div>
                                    <div class="row text-md-end text-start">
                                        <div class="col-md-6">
                                            <h6 class="text-secondary font-weight-normal mb-0">Due date:</h6>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-dark mb-0">{{ \Carbon\Carbon::parse($invoice->payment_due_date)->format('d/m/Y') }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table text-right">
                                            <thead>
                                            <tr>
                                                <th scope="col" class="pe-2 text-start ps-2">Item</th>
                                                <th scope="col" class="pe-2">Qty</th>
                                                <th scope="col" class="pe-2" colspan="2">Rate</th>
                                                <th scope="col" class="pe-2">Amount</th>
                                            </tr>
                                            </thead>
                                            <tbody>
{{--                                            @php--}}
{{--                                            print_r($products);--}}
{{--                                                @endphp--}}
                                            <tr>
                                                <td class="text-start">{{$products['item']}}</td>
                                                <td class="ps-4">{{$products['quantity']}}</td>
                                                <td class="ps-4" colspan="2">{{$products['price']}}</td>
                                                <td class="ps-4">{{$products['price']}}</td>
                                            </tr>
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <th></th>
                                                <th></th>

                                                                                                <th class="h5 ps-4" colspan="2">GST</th>
                                                                                                <th colspan="1" class="text-right h5 ps-4">{{$applications->payment_currency}} {{$products['gst']}}</th>
{{--                                                <th class="h5 ps-4" colspan="2">Total</th>--}}
{{--                                                <th colspan="1" class="text-right h5 ps-4">{{$applications->payment_currency}} {{$products['total']}}</th>--}}

                                            </tr>
                                            <tr>
                                                <th></th>
                                                <th></th>

{{--                                                <th class="h5 ps-4" colspan="2">GST</th>--}}
{{--                                                <th colspan="1" class="text-right h5 ps-4">{{$applications->payment_currency}} {{$products['gst']}}</th>--}}
                                                <th class="h5 ps-4" colspan="2">Total</th>
                                                <th colspan="1" class="text-right h5 ps-4">{{$applications->payment_currency}} {{$products['total']}}</th>

                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer mt-md-5 mt-4">
                            <div class="row">
                                <div class="col-lg-5 text-left">
                                    <h5>Thank you!</h5>
                                    <p class="text-secondary text-sm">If you encounter any issues related to the invoice
                                        you can contact us at:</p>
                                    <h6 class="text-secondary font-weight-normal mb-0">
                                        email:
                                        <span class="text-dark"><a href="mailto:semiconindia@semi.org"
                                                                   class="__cf_email__"
                                                                   data-cfemail="e99a9c9999869b9da98a9b8c889d809f8cc49d8084c78a8684">semiconindia@semi.org</a></span>
                                    </h6>
                                </div>
                                <div class="col-lg-7 text-md-end mt-md-0 mt-3">
                                    <button class="btn bg-gradient-info mt-lg-7 mb-0" onClick="window.print()"
                                            type="button" name="button">Print
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
@endsection
