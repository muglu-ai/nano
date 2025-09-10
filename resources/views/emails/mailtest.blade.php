<!DOCTYPE html>
<html>
<head>
    <title>Test Email Form</title>
</head>
<body>
    <h1>Send Test Email</h1>
    <form action="{{ route('send.email') }}" method="POST">
        @csrf
        <label for="email_type">Email Type:</label>
        <select name="email_type" id="email_type">
            <option value="general">General</option>
            <option value="invoice">Invoice</option>
            <option value="reminder">Reminder</option>
            <option value="thank_you">Thank You</option>
        </select>
        <br><br>
        <label for="to">Recipient Email:</label>
        <input type="email" name="to" id="to" required>
        <br><br>
        <label for="name">Name:</label>
        <input type="text" name="names" id="name" required>
        <br><br>
        <label for="message">Message:</label>
        <textarea name="data" id="message" required></textarea>
        <br><br>
        <button type="submit">Send Email</button>
    </form>
</body>
</html>
