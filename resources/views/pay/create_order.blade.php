

{{--/*--}}

{{--//$order = json_decode($order, true);--}}
{{--$amount = (float)$order['order']['amount'];--}}
{{--$description =  $order['order']['type'];--}}
{{--$billing_cust_name = $order['order']['billing_details']['contact_name'];--}}
{{--$billing_cust_email = $order['order']['billing_details']['email'];--}}
{{--$billing_cust_tel = $order['order']['billing_details']['phone'];--}}
{{--$billing_cust_address = $order['order']['billing_details']['address'];--}}
{{--$application_id = $order['order']['application']['application_id'];--}}


{{--//click to pay success button and fail button--}}

{{--//$success_url = route('pay.success', ['application_id' => $application_id]);--}}
{{--$success_url = '';--}}
{{--//$fail_url = route('pay.fail', ['application_id' => $application_id]);--}}
{{--$fail_url = '';--}}
{{--*/--}}

Amount : {{$amount}}
<br>
Customer Name : {{$billing_cust_name}}
<br>
Customer Email : {{$billing_cust_email}}
<br>
Customer Phone : {{$billing_cust_tel}}
<br>
Customer Address : {{$billing_cust_address}}
<br>
Application ID : {{$application_id}}
<br>
<br>
<form action="{{ route('payment.verify') }}" method="POST">
    @csrf
    <input type="hidden" name="application_no" value="{{$application_id}}">
    <button type="submit">Pay Success</button>
</form>
<br>

<button onclick="window.location.href=''">Pay Fail</button>

<div>

</div>










