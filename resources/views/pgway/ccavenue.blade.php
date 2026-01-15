<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting to CCAvenue...</title>
</head>
<body onload="document.ccavenueForm.submit();">
    <form method="post" name="ccavenueForm" action="https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction">
        <input type="hidden" name="encRequest" value="{{ $encryptedData }}">
        <input type="hidden" name="access_code" value="{{ config('constants.CCAVENUE_ACCESS_CODE') }}">
        <p>Redirecting to payment gateway...</p>
        <p> {{ $encryptedData }} </p>
        <button type="submit">Click here if not redirected</button>
    </form>
</body>
</html>
