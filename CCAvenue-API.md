##Overview
The CCAvenue API is designed to enable you to interact securely with our API from your client-side web application. You can get XML, JSON or String responses from the API, including errors.

You need to have an active account to initiate an API call to the CCAvenue payment gateway.


##API Authentication
Merchant needs to have an active account to initiate an API call to the CCAvenue payment gateway. Merchants will have to log in to their CCAvenue M.A.R.S account and get the authentication credentials for initiating API calls.

Merchant must provide CCAvenue with the public IP address from where the API calls will be initiated. API calls will work only after CCAvenue registers the IP address provided.

Log in to your CCAvenue M.A.R.S account, under Settings tab -> API Keys page; copy the following credentials:

1. Merchant ID
2. Access Code
3. Encryption Key



##Encryption of request for API calls
The requests sent to CCAvenue will contain the parameters mentioned in the table below. enc_request has to be encrypted using AES similar to the method used for real-time transaction. The encryption key is mapped to the access code as mentioned in the API Authentication section.

Name	Description
enc_request(required)	AES encrypted request data.
access_code(required)	This is the access code for your application. You must send this with each request.
command(required)	This is the command to access the API Calls. You must send this with each request.
request_type(required)	API requests are accepted in XML, JSON or String formats.  You need to specify the request type.
response_type(optional)	API returns XML, JSON or String responses. If left blank, the response will be in the same format as the request.
 version(required)	This is the version to access API based on version calls; current value is 1.1
Example:
enc_request=63957FB55DD6E7B968A7588763E08B240878046EF2F520C44BBC63FB9CCE726209A4734877F5904445591304ABB2F5E598B951E39EAFB9A24584B00590ADB077ADE5E8C444EAC5A250B1EA96F68D22E44EA2515401C2CD753DBA91BD0E7DFE7341BE1E7B7550&access_code=8JXENNSSBEZCU8KQ&command=confirmOrder&request_type=XML&response_type=XML&version=1.1


##Decryption of response for API calls
The response received from CCAvenue will contain the parameters mentioned in the table below. enc_response when encrypted will have to be decrypted using AES similar to the method used for real-time transactions. The encryption key is mapped to the access code as mentioned in the API Authentication section.

Name	Description
enc_response	AES encrypted response containing the format as per response_type.
enc_error_code	enc_error_code contains the value if the status is “1”. Please refer to the below table for the enc_error code.
status	This states whether the call was successful or not. If the value of this parameter is “1” then you need not decrypt the enc_response as it will contain the plain error message.

Note:- Please refer to the below table for enc_response value when the status value is “1”.
Example:
Successful: status=0&enc_response=63957FB55DD6E7B968A7588763E08B240878046EF2F520C44BBC63FB9CCE726209A473457E6B13721EC6D05ED13A0483ACFDD6F11F284AE79755D47E79687478F93CFCD3CD97510B67B961CDB5279F209F5C451F3039696F13C990B963854C8CADF730

Error:
status=1&enc_response=Access_code: Invalid Parameter&enc_error_code=51407.



##CCAvenue API supports the following API calls.

Confirm
The Confirm API call allows you to confirm pending orders. Only confirmed orders are settled into the merchant's account. An order older than 5 days is automatically cancelled. Once an order has been automatically cancelled by the system, it cannot be confirmed.
Cancel
The Cancel API call allows you to cancel pending orders. Funds will be refunded to the credit card or debit card or net banking account that was originally charged. An order older than 5 days is automatically cancelled.
Refund
The Refund API call allows you to refund orders/transactions that have previously been executed but not yet refunded. Funds will be refunded to the credit card or debit card or net banking account that was originally charged.
Status
The Status API call can be used to ascertain the status of transactions/orders. You can use this call if you have not received status/information for transaction requests. It can also be used as an additional security measure to reconfirm the parameters posted back.
Order Lookup
The Order Lookup API call can be used to find transactions/orders based on a given criteria.
Pending Orders
The Pending Orders API call can be used to list transactions which are yet to be confirmed or cancelled. Pending orders need to be confirmed for them to be settled, those older than 5 days are automatically cancelled.
Update Merchant Param
Update Merchant params API is used to add some additional parameters against Merchant params if the same could not be done at the time of the transaction.
Update Billing Details
Update billing details API call is used to update customer billing information against an order.




##Confirm
Confirm API call allows you to confirm pending orders. Only confirmed orders are settled into the merchant's account. An order older than 5 days is automatically cancelled. Once an order has been auto-cancelled by the system, it cannot be confirmed. You also have the option to confirm only part of an order. This can be done only once for each order. The balance funds will be refunded to the credit card or debit card or net banking account that was originally charged.
Request Parameters
Name	Description	Note
enc_request (required)	AES encrypted string containing request parameters.	
access_code (required)	This is the unique CCAvenue access code which is generated when merchant registered their IP address. You must send this with each request.	
request_type (required)	API requests are accepted in XML, JSON or String formats. You need to specify the request type.	Possible value for request_type is “XML” or “JSON” or “STRING”.
response_type

(optional)	API returns responses in XML, JSON or String formats. If left blank, the response will be in the same format as the request.	Possible value for response_type is “XML” or “JSON” or “STRING”.
Command

 (required)	Command value specifies the API calls. You must send this with each request.	Possible value for this API call is “confirmOrder”.
reference_no

(required)	This is the unique CCAvenue reference number for the transaction.	Numeric(25)
amount

(required)	This is the transaction amount to be captured. Amount can be the full or partial transaction amount.	Decimal(12,2)

Example XML Request
Sample Code
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
   <Order_List>
      <order reference_no="123456789" amount="1.00" />
      <order reference_no="123456789" amount="2.00" />
</Order_List>

Note: You will have to encrypt the above request and store in the "enc_request" parameter before sending it to CCAvenue. Kindly refer to the encryption section.

Example JSON Request
Sample Code
{
   "order_List": [
      { "reference_no":"203000099429", "amount": "1.00"},
      { "reference_no": "203000104640", "amount": "1.00"}
   ]
}

Note: You will have to encrypt the above request and store in the "enc_request" parameter before sending it to CCAvenue. Kindly refer to the encryption section.
Example STRING Request
Format: reference_no$amt|reference_no$amt|reference_no$amt|
Example: 203000099429$1.00|203000104640$1.00|

Note: You will have to encrypt the above request and store in the "enc_request" parameter before sending it to CCAvenue. Kindly refer to the encryption section.
Response Parameters
Name	Description	Note
status	This states whether the call was successful or not. If value of this parameter is “1” then you need not decrypt the enc_response as it will contain plain error message.	
Value “0” denotes that the API call is successful.
Value “1” denotes that the API call has failed.
If enc_response is plain text, it represents the error message.

enc_response	AES encrypted response containing the format as per response_type	
success_count	Merchant checks the successfully processed records for confirmation of the transactions.	
Numeric

0<=success_count<=

Number of orders to be confirmed.
reference_no	The unique CCAvenue reference numbers for the transaction.	Numeric(25)
reason	Failure reason as per the given unique reference_no.	
String
Please refer to the below table for the failure message.

error_code	Error code for Failure.	
String
Please refer to the below table for the failure message.


Example XML Response
Sample Code
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Order_Result error_code="">
  <error_desc></error_desc>
  <success_count>0</success_count>
  <failed_List>
   <failed_order error_code="51304 " reason=" Invalid order/tracking id " reference_no="123456788"/>
   <failed_order error_code="51206" reason="Order List: Invalid Parameter" reference_no="123456788"/>
  </failed_List>
</Order_Result>

Note: You will have to decrypt the above response from "enc_response" parameter. Kindly refer to the decryption section.

Example JSON Response
Sample Code
{
   "failed_List":[
      {"reference_no":"123456788","reason":" Invalid order/tracking id ","error_code":"51304 "},
      {"reference_no":"123456788","reason":"Order List: Invalid Parameter","error_code":"51206"}
   ],
"error_desc":"",
"success_count":0,
"error_code":""
}

Note: You will have to decrypt the above response from "enc_response" parameter. Kindly refer to the decryption section.
Example STRING Response
Format: success_count|error_code$reference_no$reason^error_code$reference_no$reason|
Example: 0|51304 $123456788$ Invalid order/tracking id ^51206$123456788$Order List: Invalid Parameter|



##Status
The Status API call can be used to ascertain the status of transactions/orders. You can use this call if you have not received status/information for a transaction request. It can also be used as an additional security measure to reconfirm the parameters posted back.
Request Parameters
Name	Description	Note
Parameters Datatype (Parameters max length)
enc_request

(required)	AES encrypted request data	
access_code

(required)	The unique CCAvenue access code which is generated when merchant registered their IP address. You must send this with each request.	
request_type

(required)	API requests are accepted in XML, JSON or String formats. You need to specify the request type.	Possible value for request_type   is “XML” or “JSON” or “STRING”.
response_type

(optional)	API returns responses in XML, JSON or String format. If left blank, the response will be in the same format as the request.	Possible value for response_type is “XML” or “JSON” or “STRING”.
command (required)	Command value specifies the API calls. You must send this with each request.	Value is “orderStatusTracker”.
reference_no (conditional)	
CCAvenue reference no. allocated to the transaction.  

Reference number is required if you do not share order_no.	
Numeric(25).

order_no (conditional)	
This is the merchant reference number for the transaction.

Order number is required if you do not share reference_no.	
AlphaNumeric with special characters (hyphen and underscore)(40).


Example XML Request
Sample Code
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Order_Status_Query order_no="33231644" reference_no="225013271813"/>

Note: You will have to encrypt the above request and store in the "enc_request" parameter before sending it to CCAvenue. Kindly refer to the encryption section.

Example JSON Request
Sample Code
{
   "reference_no": "225013271813",
   "order_no": "33231644"
}

Note: You will have to encrypt the above request and store in the "enc_request" parameter before sending it to CCAvenue. Kindly refer to the encryption section.
Example STRING Request
Format: reference_no|order_no|
Example: 225013271813|33231644|

Note: You will have to encrypt the above request and store in the "enc_request" parameter before sending it to CCAvenue. Kindly refer to the encryption section.

Response Parameters
Name	Description	Note
status	This states whether the call was successful or not. If the value of this parameter is “1” then you need not decrypt the enc_response as it will contain the plain error message.	
Value “0” denotes that the API call is successful.
Value “1” denotes that the API call has failed.
If enc_response is plain text, it represents the error message.

enc_response	AES encrypted response containing format as per response_type	
order_amt	Amount for the transaction.	Decimal(12,2).
order_bill_address	Order billing address details for the order.	Possible value for address is Alphanumeric with special characters (space, hyphen, comma, ampersand(&), hash(#), circular brackets and dot)(315)
order_bill_city	Order billing City name for the order.	Possible value for city is Alphanumeric with special characters (space, comma, hyphen and dot)(30).
order_bill_country	Order billing country for the order.	Possible value for country is Alphanumeric with special characters (space)(30).
 order_bill_email	Email Address of the Order for notifications.	Possible value for email id is Alphanumeric with special characters (hyphen, underscore, dot, @)(70).
order_bill_name	Order billing name for the order.	Possible value for name is Alphanumeric with special characters (space, hyphen, apostrophe, underscore, dot)(60).
order_bill_state	Order billing state for the order.	Alphanumeric with special characters (hyphen, dot and space)(30).
order_bill_tel	Order billing telephone no for the order.	Numeric(10)
order_bill_zip	
Order billing address’s pin code for the order.

Possible value for zip is AlphaNumeric with special characters (hyphen and space) (15).
order_capt_amt	Captured amount for the transaction. Capture amount can be the full or partial transaction amount.	Decimal(12,2).
order_curr	
Possible order Currency in which merchant   processed the transaction.

String
Examples:
INR - Indian Rupee
USD - United States Dollar
SGD - Singapore Dollar
GBP - Pound Sterling
EUR - Euro, official currency of Eurozone

order_date_time	Order generated Date &Time.	DateTime in IST( yyyy-MM-dd HH:mm:ss.SSS) format.
order_device_type	This is the type of device using which the transaction was processed.	Possible value for device type is IVRS/MOB/PC.
order_discount	This is the Discount Value for the Order No.	Decimal(12,2).
order_fee_flat	Flat Fee for the Order No.	Decimal(12,2).
order_fee_perc	Provides the percentage fee for the same order No.	Decimal(12,2).
order_fee_perc_value	This attribute provides the percentage fee Value for the same order No.	Decimal(12,2).
order_fraud_status	
Specify whether orders are valid or not.

String
Possible Values are:
1) Value “High” denotes “High Risk”
2) Value “Low” denotes “Low Risk”
3) Value “NR”  denotes “No Risk”
4) Value “GA”   denotes “Go Ahead”
5) Value “NA” denotes “Not Applicable”

order_gross_amt	Total transaction amount.	Decimal(12,2).
order_ip	Customer IP Address (i.e. from where the transaction is being initiated)	IP V-4 Supported.
order_no	Order No for the transaction.	AlphaNumeric with special characters(hyphen and underscore)(40).
order_notes	Order information you wish to provide.	AlphaNumeric with special characters (space, comma, dot, hyphen and underscore)(60).
order_ship_address	Shipping Address for the order.	Possible value for address is Alphanumeric with special characters (space, hyphen, comma, ampersand(&), hash(#), circular brackets and dot)(315)
order_ship_city	Shipping city name for the orders.	Possible value for city is Alphanumeric with special characters (space, comma, hyphen and dot)(30).
order_ship_country	Shipping country name for the orders.	Possible value for country is Alphanumeric with special characters (space)(30).
order_ship_email	Shipping email ID for the notifications of the transaction.	Possible value for email id is Alphanumeric with special characters (hyphen, underscore, dot, @)(70).
order_ship_name	Shipping Name of the Customer for the order.	Possible value for name is Alphanumeric with special characters (space, hyphen, apostrophe, underscore, dot)(60).
order_ship_state	Shipping state for the order.	Alphanumeric with special characters (hyphen, dot and space)(30).
order_ship_tel	Telephone no for notifications of the transaction.	Numeric(10).
order_ship_zip	Order shipping address pin code for the order.	Possible value for zip is AlphaNumeric with special characters(hyphen and space) (15).
order_status	Status of the order. It can be single or multiple.	
String
Possible values are:
Aborted (transaction is cancelled by the User
Auto-Cancelled (transaction has not confirmed within 5 days hence auto cancelled by system)
Auto-Reversed   (two identical transactions for same order number, both were successful at bank's end but we got response for only one of them, then next day during reconciliation we mark one of the transaction as auto reversed )
Awaited (transaction is processed from billing shipping page but no response is received)
Cancelled (transaction is cancelled by merchant )
Chargeback()
Invalid(Transaction sent to CCAvenue with Invalid parameters, hence could not be processed further)
Fraud (we update this during recon, the amount is different at bank’s end and at CCAvenue due to tampering)
Initiated (transaction just arrived on billing shipping page and not processed further )
Refunded (Transaction is refunded.)
Shipped (transaction is confirmed)
Successful
System refund (Refunded by CCAvenue for various findings of reversals by CCAvenue)
Unsuccessful (transaction is not successful due to )

order_status_date_time	This is the latest date and time when the order status is modified.	DateTime in IST( yyyy-MM-dd HH:mm:ss.SSS) format.
order_TDS	Amount of TDS (tax deducted at source) for the Transaction.	Decimal (13,4)
order_tax	Tax Amount for the Transaction.	Decimal (13,4)
reference_no	CCAvenue reference no. allocated to the transaction.	
Numeric(25).

order_bank_ref_no	Unique reference number shared by the Bank after successful transaction.	Numeric(25).
order_bank_response	Description about the transaction shared by the bank after transaction.	String
order_gtw_id	Unique payment option - Bank name.	Alphabet(6)
order_card_name	Specify the card name for the transaction.	
Possible value for card name is
VISA","MASTERCARD","AMEX","JCB","ECRD","DINERS CLUB","DSNV","CTBL","CVMS".

order_option_type	Specify the payment option type for the order.	
String
Possible value for payment option type is

OPTCASHC-Cash card
OPTCRDC -Credit Card
OPTDBCRD-Debit Card
OPTEMI-EMI
OPTIVRS-IVRS
OPTMOBP-MobilePayments
OPTNBK-Net Banking

page_count	Total pages available based on no_of_records in the request	
Example: no_of_records sent in request was 100
total_records matching the lookup criteria were 1000

page_count will be 10 (total_records / no_of_records) rounded to the ceiling
total_records	Total no.of orders matching the lookup criteria
error_desc	Reason if search criteria did not find the orders for the transactions.	
String

Please refer to the below table for the failure message.
error_code	Error code for Failure reason.	
String
Please refer to the below table for the failure message.

Example XML Response
Sample Code
Success Response:
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Order_Status_Result error_code="">
<error_desc></error_desc>
<order_TDS>0.0</order_TDS>
<order_amt>1.0</order_amt>
<order_bank_ref_no>289049</order_bank_ref_no>
<order_bank_response>Approved</order_bank_response>
<order_bill_address>Room no 1101, near Railway station</order_bill_address>
<order_bill_city>Indore</order_bill_city>
    <order_bill_country>India</order_bill_country>
    <order_bill_email>xxxxx.xxx@xxxxx.xxxx</order_bill_email>
    <order_bill_name>Shashi</order_bill_name>
    <order_bill_state>MP</order_bill_state>
    <order_bill_tel>1234567890</order_bill_tel>
    <order_bill_zip>425001</order_bill_zip>
    <order_capt_amt>0.0</order_capt_amt>
    <order_card_name>Amex</order_card_name>
    <order_currncy>INR</order_currncy>
    <order_date_time>2015-04-13 10:59:05.517</order_date_time>
    <order_device_type>PC</order_device_type>
    <order_discount>0.0</order_discount>
    <order_fee_flat>0.0</order_fee_flat>
    <order_fee_perc>4.0</order_fee_perc>
    <order_fee_perc_value>0.04</order_fee_perc_value>
    <order_fraud_status>NA</order_fraud_status>
    <order_from_date></order_from_date>
    <order_gross_amt>1.0</order_gross_amt>
    <order_gtw_id>PGT</order_gtw_id>
    <order_ip>192.168.2.182</order_ip>
    <order_name></order_name>
    <order_no>66885810</order_no>
    <order_notes>order will be shipped</order_notes>
    <order_option_type>OPTCRDC</order_option_type>
    <order_ship_address>room no.701 near bus stand</order_ship_address>
    <order_ship_city>Hyderabad</order_ship_city>
    <order_ship_country>India</order_ship_country>
    <order_ship_name>Chaplin</order_ship_name>
    <order_ship_state>Andhra</order_ship_state>
    <order_ship_tel>1234567890</order_ship_tel>
    <order_ship_zip>425001</order_ship_zip>
    <order_status>Successful</order_status>
    <order_status_date_time>2015-04-13 10:59:53.217</order_status_date_time>
    <order_tax>0.0049</order_tax>
    <order_to_date></order_to_date>
    <reference_no>204000136232</reference_no>
    <status>0</status>
</Order_Status_Result>

Failure Response:
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Order_Status_Result error_code="51313">
    <error_desc>No record found</error_desc>
    <status>1</status>
</Order_Status_Result>

Note: You will have to decrypt the above response from "enc_response"" parameter. Kindly refer to the decryption section.
Example JSON Response
Sample Code
Success Response:
{
    "reference_no":204000136232,
    "order_no":"66885810",
    "order_currncy":"INR",
    "order_amt":1.0,
    "order_date_time":"2015-04-13 10:59:05.517",
    "order_bill_name":"Shashi",
    "order_bill_address":"Room no 1101,
     near Railway station Ambad",
    "order_bill_zip":"425001",
    "order_bill_tel":"1234567890",
    "order_bill_email":"chandrakant.patil@avenues.info",
    "order_bill_country":"India",
    "order_ship_name":"Chaplin",
    "order_ship_address":"room no.701 near bus stand",
    "order_ship_country":"India",
    "order_ship_tel":"1234567890",
    "order_bill_city":"Indore",
    "order_bill_state":"MP",
    "order_ship_city":"Hyderabad",
    "order_ship_state":"Andhra",
    "order_ship_zip":"425001",
    "order_notes":"order will be shipped",
    "order_ip":"192.168.2.182",
    "order_status":"Successful",
    "order_fraud_status":"NA",
    "order_status_date_time":"2015-04-13 10:59:53.217",
    "order_capt_amt":0.0,
    "order_card_name":"Amex",
    "order_fee_perc":4.0,
    "order_fee_perc_value":0.04,
    "order_fee_flat":0.0,
    "order_gross_amt":1.0,
    "order_discount":0.0,
    "order_tax":0.0049,
    "order_bank_ref_no":"289049",
    "order_gtw_id":"PGT",
    "order_bank_response":"Approved",	
    "order_option_type":"OPTCRDC",
    "order_TDS":0.0,
    "order_device_type":"PC",	
    "status":0,
    "error_desc":"",
    "error_code":"",
}


Failure Response:
{
    "error_desc":"No record found",
    "error_code":"51313",
    "status":1
}

Note: You will have to decrypt the above response from "enc_response" parameter. Kindly refer to the decryption section.
Example STRING Response
Format:
status|order_status|reference_no|order_bank_ref_no|order_bank_response| order_bill_name|order_bill_email|order_bill_address|order_bill_city|order_bill_state|order_bill_country|order_bill_telephone_no|order_bill_city_zip|order_card_name|order_currency|order_date_time|order_delivery_details|order_device_type|order_fraud_status|order_gateway_id|order_iP|order_no| order_notes|order_option_type|order_shiping_name|order_ship_email|order_ship_address|order_ship_city|order_ship_state|order_ship_country|order_ship_telephone_no|order_ship_zip|order_status_date_time|order_TDS|order_amount|order_capture_amount|order_discount|order_fee_flat||order_fee_perc|order_fee_perc_value|order_gross_amount|order_tax|

Example:
0|Successful|204000136232|289049|Approved|Shashi|xxxxx.xxx@xxxxx.xxxx|Room no 1101, near Railway station Ambad|Indore|MP|India|1234567890|425001|Amex|INR|2015-04-13 10:59:05.517||PC|NA|PGT|192.168.2.182|66885810|order will be shipped|OPTCRDC|Chaplin||room no.701 near bus stand|Hyderabad|Andhra|India|1234567890|425001|2015-04-13 10:59:53.217|0.0|1.0|0.0|0.0|0.0|4.0|0.04|1.0|0.0049|

Failure Response:
Format: statud|error_code|error_desc|
Example: 1|51313|No records found.|



##Order Lookup
The Lookup API call can be used to extract transaction details for a certain set of transactions. The functionality works similar to a search feature.
Request Parameters
Name	Description	Note
enc_request

(required)	AES encrypted request data	 
access_code

(required)	The unique CCAvenue access code which is generated when the merchant registered their IP address. You must send this with each request.	 
request_type

(required)	API requests are accepted in XML, JSON or String formats. You need to specify the request type.	Possible value for request_type  is “XML” or “JSON” or “STRING”.
response_type

(optional)	API returns responses in XML, JSON or String formats. If left blank, the response will be in the same format as the request.	Possible value for response_type is “XML” or “JSON” or “STRING”.
Command

 (required)	Command value specifies the API calls. You must send this with each request.	Possible value for this API call is “orderLookup”.
reference_no

 (optional)	The unique CCAvenue reference number for the transaction.	Numeric(25).
from_date

 (required)	Provide the Start Date to find the orders list.	Date must be in IST(dd-mm-yyyy )format.
to_date

(optional)	Provide the end date to search the orders between from date and to date. It should be greater than or equal to from date.	Date must be in IST(dd-mm-yyyy) format.
order_currency

 (optional)	Currency in which you processed the transaction. You can send the multiple currencies format.	
String

Example:
INR - Indian Rupee
USD - United States Dollar
SGD - Singapore Dollar
GBP - Pound Sterling
EUR - Euro, official currency of Eurozone

Multiple currency format:

INR|USD|GBP in JSON & XML request type but INR$USD$GBP in STRING request type
order_email

 (optional)	Email address used to purchase the order.	
Alphanumeric with special characters (hyphen, underscore, dot, @)(70).

order_fraud_status

(optional)	
Specify whether orders are valid or not.

String
Possible Values are:
1) Value “High” denotes “High Risk”
2) Value “Low” denotes “Low Risk”
3) Value “NR”  denotes “No Risk”
4) Value “GA”  denotes “Go Ahead”
5) Value “NA” denotes “Not Applicable”

order_min_amount

(optional)	Minimum amount limit for search criteria for the transaction.	Decimal(12,2).
order_max_amount

(optional)	Maximum amount limit for search criteria for the transaction.	Decimal(12,2).
order_name

 (optional)	The customer name for the transaction.	Alphanumeric with special characters (space, hyphen, apostrophe, underscore, dot)(60).
order_no

(optional)	The unique merchant order no for the transaction.	AlphaNumeric with special characters(hyphen and underscore)(40).
order_payment_type

(optional)	
Payment Mode for the transaction. It can be single or multiple.

String

Below are the Possible Values:
1) CASHC (Cash Card Payment Type)
2) CRDC (Credit Card Payment Type)
3) DBCRD (Debit Card Payment Type)
4) MOBP (Mobile Payment Type)
5) NBK (Net Banking )

Multiple values format:

MOBP|NBK for JSON & XML request type but MOBP$NBK for STRING request type.
order_status

 (optional)	Status of the order. It can be single or multiple.	
String
Possible values are:
Aborted (transaction is cancelled by the User)
Auto-Cancelled (transaction has not confirmed within 5 days hence auto cancelled by system)
Auto-Reversed   (two identical transactions for same order number, both were successful at bank's end but we got response for only one of them, then next day during reconciliation we mark one of the transaction as auto reversed )
Awaited (transaction is processed from billing shipping page but no response is received)
Cancelled (transaction is cancelled by merchant )
Chargeback()
Invalid(Transaction sent to CCAvenue with Invalid parameters, hence could not be processed further)
Fraud (we update this during recon, the amount is different at bank’s end and at CCAvenue due to tampering)
Initiated (transaction just arrived on billing shipping page and not processed further )
Refunded (Transaction is refunded.)
Shipped (transaction is confirmed)
Successful
System refund (Refunded by CCAvenue for various find of reversals by CCAvenue)

Unsuccessful (transaction is not successful due to )
order_type

 (optional)	Type of the order.	
String

Different types of Orders:

1) OT-INV denotes “Invoice”
2) OT-ORD denotes “Orders”
3) OT-ORDSC denotes “Shopping Cart Orders”
4) OT-PPAY denotes” Phone Pay”
5) OT-SNIP denotes “SNIP orders”

order_bill_tel

 (optional)	Customer mobile number for the transaction.	Numeric(10).
page_number

(required)	A limited number of records are shared as part of the response. The total records & number of pages are shared as part of the response to enable subsequent calls.	Numeric(4).
Example XML Request
Sample Code
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
   <Order_Lookup_Query>
      <order_no>xxxxxxx</order_no>
      <reference_no>xxxxxxxxx</reference_no>
      <order_email>xxx@xxxxx.com</order_email>
      <order_bill_tel>xxxxxxxxxxx</order_bill_tel>
      <order_country>xxxxx</order_country>
      <from_date>xx-xx-xxxx</from_date>
      <to_date>xx-xx-xxxx</to_date>
      <order_max_amount>xx.xx</order_max_amount>
      <order_min_amount>xx.xx</order_min_amount>
      <order_status>xxxxxxx</order_status>
      <order_fraud_status>xxxx</order_fraud_status>
      <order_currency>xxx</order_currency>
      <order_type>xx-xxx</order_type>
      <order_payment_type>xxxxxx</order_payment_type>
      <page_number>1</page_number>
</Order_Lookup_Query>

Note: You will have to encrypt the above request and store in the "enc_request" parameter before sending it to CCAvenue. Kindly refer to the encryption section.
Example JSON Request
Sample Code
{
   "order_no": "xxx",
   "reference_no":"xxxxx",
   "order_email": "xxx@xxxx.com",
   "order_bill_tel": "xxxxxxxxx",
   "order_country": "xxxxxxx",
   "from_date": "xx-xx-xxxx",
   "to_date": "xx-xx-xxxx",
   "order_max_amount": "xx.xx",
   "order_min_amount": "xx.xx",
   "order_status": "xxxxx",
   "order_fraud_status": "xxxx",
   "order_currency": "xxx",
   "order_type": "xxxxxx",
   "order_payment_type": "xxxxx",
   "page_number":1
}

Note: You will have to encrypt the above request and store in the "enc_request" parameter before sending it to CCAvenue. Kindly refer to the encryption section.
Example STRING Request

Format: reference_no|order_no|from_date|to_date|order_status|order_bill_tel|order_country|order_email|order_fraud_status|order_max_amount|order_min_amount|order_name|order_payment_type|order_type|order_currency|page_number|
Example: ||21-03-2015|03-04-2015||||||||||||1|

Note: You will have to encrypt the above request and store in the "enc_request" parameter before sending it to CCAvenue. Kindly refer to the encryption section.
Response Parameters
Name	Description	Note
status	This states whether the call was successful or not. If value of this parameter is “1” then you need not decrypt the enc_response as it will contain the plain error message.	
Value “0” denotes that the API call is successful.
Value “1” denotes that the API call has failed.
If enc_response is plain text, it represents the error message.

enc_response	AES encrypted response containing the format as per response_type	 
order_amt	Amount for the transaction.	Decimal(12,2).
order_bill_address	Order billing address details for the order.	Possible value for address is Alphanumeric with special characters (space, hyphen, comma, ampersand(&), hash(#), circular brackets and dot)(315)
order_bill_city	Order billing City name for the order.	Possible value for city is Alphanumeric with special characters (space, comma, hyphen and dot)(30).
order_bill_country	Order billing country for the Order.	Possible value for country is Alphanumeric with special characters (space)(30).
 order_bill_email	Email Address of the Order for notifications.	Possible value for email id is Alphanumeric with special characters (hyphen, underscore, dot,@)(70).
order_bill_name	Order billing name for the order.	Possible value for name is Alphanumeric with special characters (space, hyphen, apostrophe, underscore, dot)(60).
order_bill_state	Order billing state for the order.	Alphanumeric with special characters (hyphen, dot and space)(30).
order_bill_tel	Order billing telephone no for the order.	Numeric(10)
order_bill_zip	
Order billing address pin code for the order.

Possible value for zip is AlphaNumeric with special characters (hyphen and space) (15).
order_capt_amt	Captured amount for the transaction. Captured amount can be full or partial of the transaction amount.	Decimal(12,2).
order_curr	
Possible order Currency in which the merchant processed the transaction.

String
Examples:
INR - Indian Rupee
USD - United States Dollar
SGD - Singapore Dollar
GBP - Pound Sterling
EUR - Euro, official currency of Eurozone

order_date_time	Order Generated Date &Time.	DateTime in IST( yyyy-MM-dd HH:mm:ss.SSS) format.
order_device_type	This is the type of device using which the transaction was processed.	Possible value for device type is IVRS/MOB/PC.
order_discount	This is Discount Value for the Order No.	Decimal(12,2).
order_fee_flat	Flat Fee for the Order No.	Decimal(12,2).
order_fee_perc	Provides the percentage fee for the same order No.	Decimal(12,2).
order_fee_perc_value	This attribute provides the percentage fee Value for the same order No.	Decimal(12,2).
order_fraud_status	
Specify whether orders are valid or not.

String
Possible Values are:
1) Value “High” denotes “High Risk”
2) Value “Low” denotes “Low Risk”
3) Value “NR”  denotes “No Risk”
4) Value “GA”  denotes “Go Ahead”
5) Value “NA” denotes “Not Applicable”

order_gross_amt	Total transaction amount.	Decimal(12,2).
order_ip	Customer IP Address (i.e. from where transaction is being initiated)	IP V-4 Supported.
order_no	Order No. for the transaction.	AlphaNumeric with special characters(hyphen and underscore)(40).
order_notes	Order information you wish to provide.	AlphaNumeric with special characters(space, comma, dot, hyphen and underscore)(60).
order_ship_address	Shipping Address for the order.	Possible value for address is Alphanumeric with special characters (space, hyphen, comma, ampersand(&), hash(#), circular brackets and dot)(315)
order_ship_city	Shipping city name for the orders.	Possible value for city is Alphanumeric with special characters (space, comma, hyphen and dot)(30).
order_ship_country	Shipping country name for the orders.	Possible value for country is Alphanumeric with special characters (space)(30).
order_ship_email	Shipping email ID for the notifications of the transaction.	Possible value for email id is Alphanumeric with special characters (hyphen, underscore, dot,@)(70).
order_ship_name	Shipping Name of the Customer for the order.	Possible value for name is Alphanumeric with special characters (space, hyphen, apostrophe, underscore, dot)(60).
order_ship_state	Shipping state for the order.	Alphanumeric with special characters (hyphen, dot and space)(30).
order_ship_tel	Telephone no for notifications of the transaction.	Numeric(10).
order_ship_zip	Order shipping address pin code for the order.	Possible value for zip is AlphaNumeric with special characters(hyphen and space) (15).
order_status	Status of the order. It can be single or multiple.	
String
Possible values are:
Aborted (transaction is cancelled by the User)
Auto-Cancelled (transaction has not confirmed within 5 days hence auto cancelled by system)
Auto-Reversed   (two identical transactions for same order number, both were successful at bank's end but we got response for only one of them, then next day during reconciliation we mark one of the transaction as auto reversed )
Awaited (transaction is processed from billing shipping page but no response is received)
Cancelled (transaction is cancelled by merchant )
Chargeback()
Invalid(Transaction sent to CCAvenue with Invalid parameters, hence could not be processed further)
Fraud (we update this during recon, the amount is different at bank’s end and at CCAvenue due to tampering)
Initiated (transaction just arrived on billing shipping page and not processed further )
Refunded (Transaction is refunded.)
Shipped (transaction is confirmed)
Successful
System refund (Refunded by CCAvenue for various find of reversals by CCAvenue)
Unsuccessful (transaction is not successful due to )

order_status_date_time	This is the latest date and time when order status is modified.	DateTime in IST( yyyy-MM-dd HH:mm:ss.SSS) format.
order_TDS	Amount of TDS (tax deducted at source) for the Transaction.	Decimal(13,4).
order_tax	Tax Amount for the Transaction.	Decimal (13,4)
reference_no	CCAvenue reference no allocated to the transaction.	
Numeric(25).

order_bank_ref_no	Unique reference number shared by Bank after the successful transaction.	Numeric(25).
order_bank_response	Description about the transaction shared by the bank after transaction.	String
order_gtw_id	Unique payment option - Bank name.	Alphabet(6)
order_card_name	Specify the card name for the transaction.	
Possible value for card name is
VISA","MASTERCARD","AMEX","JCB","ECRD","DINERS CLUB","DSNV","CTBL","CVMS".

order_option_type	Specify the payment option type for the order.	
String
Possible value for payment option type is
OPTCASHC-Cash card
OPTCRDC -Credit Card
OPTDBCRD-Debit Card
OPTEMI-EMI
OPTIVRS-IVRS
OPTMOBP-MobilePayments
OPTNBK-Net Banking

error_desc	Reason if search criteria did not find the orders for the transactions.	
String

Please refer to the below table for the failure message.
error_code	Error code for Failure reason.	
String
Please refer to the below table for the failure message.

page_count	Total pages available based on no_of_records in the request	
Example: no_of_records sent in request was 100
total_records matching the lookup criteria were 1000

page_count will be 10 (total_records / no_of_records) rounded to the ceiling
total_records	Total no.of orders matching the lookup criteria
Example XML Response
Sample Code
Success Response:
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Order_Lookup_Result error_code="">
   <error_desc></error_desc>
   <order_Status_List>
      <order order_TDS="0.0"
         order_amt="1.0"
         order_bank_response="Invalid Credentials"
         order_bill_address="Room no 1101, near Railway station Ambad"
         order_bill_city="Indore"
         order_bill_country="India"
         order_bill_email="xxxxx.xxx@xxxxx.xxxx"
         order_bill_name="Shashi"
         order_bill_state="MP"
         order_bill_tel="1234567890"
         order_bill_zip="425001"
         order_capt_amt="0.0"
         order_card_name="MasterCard"
         order_currncy="INR"
         order_date_time="2015-03-31 11:20:44.47"
         order_device_type="PC"
         order_discount="0.0"
         order_fee_flat="0.0"
         order_fee_perc="12.0"
         order_fee_perc_value="0.12"
         order_fraud_status="NA"
         order_gross_amt="1.0"
         order_gtw_id="SBI"
         order_ip="192.168.2.182"
         order_no="45289752"
         order_notes="order will be shipped"
         order_option_type="OPTCRDC"
         order_ship_address="Room no 1101, near Railway station Ambad"
         order_ship_city="Indore"
         order_ship_country="India"
         order_ship_name="Shashi"
         order_ship_state="MP"
         order_ship_tel="1234567890"
         order_ship_zip="425001"
         order_status="Unsuccessful"
         order_status_date_time="2015-03-31 11:21:09.99"
         order_tax="0.0148"
         reference_no="204000134595"/>
      </order_Status_List>
      <page_count>1</page_count>
      <total_records>1</total_records>
</Order_Lookup_Result>


Failure Response:
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Order_Lookup_Result error_code="51313">
<error_desc>No records found</error_desc>
   <page_count>0</page_count>
   <total_records>0</total_records>
</Order_Lookup_Result>

Note: You will have to decrypt the above response from "enc_response" parameter. Kindly refer to the decryption section.

Example JSON Response
Sample Code
Success Response:
{
"order_Status_List":[{
    "reference_no":204000134595,
    "order_no":"45289752",
    "order_currncy":"INR",
    "order_amt":1.0,
    "order_date_time":"2015-03-31 11:20:44.47",
    "order_bill_name":"Shashi",
    "order_bill_address":"Room no 1101, near Railway station Ambad",
    "order_bill_zip":"425001",
    "order_bill_tel":"1234567890",
    "order_bill_email":"xxxx.xxxx@xxxx.xxxx",
    "order_bill_country":"India",
    "order_ship_name":"Shashi",
    "order_ship_address":"Room no 1101, near Railway station Ambad",
    "order_ship_country":"India",
    "order_ship_tel":"1234567890",
    "order_bill_city":"Indore",
    "order_bill_state":"MP",
    "order_ship_city":"Indore",
    "order_ship_state":"MP",
    "order_ship_zip":"425001",
    "order_notes":"order will be shipped",
    "order_ip":"192.168.2.182",
    "order_status":"Unsuccessful",
    "order_fraud_status":"NA",
    "order_status_date_time":"2015-03-31 11:21:09.99",
    "order_capt_amt":0.0,
    "order_card_name":"MasterCard",
    "order_fee_perc_value":0.12,
    "order_fee_perc":12.0,
    "order_fee_flat":0.0,
    "order_gross_amt":1.0,
    "order_discount":0.0,
    "order_tax":0.0148,
    "order_TDS":0.0,
    "order_gtw_id":"SBI",
    "order_bank_response":"Invalid Credentials",
    "order_option_type":"OPTCRDC",
    "order_device_type":"PC"
    }],
"page_count":1,
"total_records":1,
"error_desc":"",
"error_code":""
}

Failure Response:
{
    "page_count":0,
    "total_records":0,
    "error_desc":"No records found",
    "error_code":"51313"
}

Note: You will have to decrypt the above response from "enc_response" parameter. Kindly refer to the decryption section.
Example STRING Response
Success Response:
Format: page_count|total_records|reference_no$order_no$order_amount$order_status$order_bank_ref_no$order_bank_response$order_card_name$order_currancy$order_date_time$order_delivery_details$order_device_type$order_fraud_status$order_gateway_id$order_ip$order_notes$order_option_type$order_bill_name$order_bill_address$order_bill_city$order_bill_state$order_bill_country$order_bill_zip$order_bill_tel$order_bill_email$order_ship_name$order_ship_address$order_ship_city$order_ship_state$order_ship_county$order_ship_zip$order_ship_tel$order_ship_email$order_capture_amount$order_discount$order_gross_amount$order_fee_flat$order_fee_perc$order_fee_perc_value^|

Example:
Successful Response: 1|1|204000134595$45289752$1.0$Unsuccessful$$Invalid Credentials$MasterCard$INR$2015-03-31 11:20:44.47$$PC$NA$SBI$192.168.2.182$order will be shipped$OPTCRDC$Shashi$Room no 1101, near Railway station Ambad$Indore$MP$India$425001$9595226054$xxxxx.xxxx@xxxxxx.xxxx$Shashi$Room no 1101, near Railway station Ambad$Indore$MP$India$425001$1234567890$$0.0$0.0$1.0$0.0$12.0$0.12|

Failure Response:
Format: page_no|total_no_of_records|error_code|error_desc|
Example: 0|0|51313|No records found|
