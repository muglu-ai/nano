<!DOCTYPE html>
<html>
<head>
    <title>Laravel Captcha Example</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .captcha-image { display: flex; align-items: center; gap: 10px; }
    </style>
</head>
<body>

    <h2>Captcha Form Example</h2>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div style="color: green;">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="">
        @csrf
        <label>Name:</label><br>
        <input type="text" name="name" required><br><br>

        <label>Captcha:</label><br>
        <div class="captcha-image">
            <span id="captcha-img">{!! captcha_img() !!}</span>
            <button type="button" id="reload">⟳ Reload</button>
        </div><br>
        <input type="text" name="captcha" placeholder="Enter Captcha" required><br><br>

        <button type="submit">Submit</button>
    </form>

    <script>
    document.getElementById('reload').addEventListener('click', function () {
        fetch('/reload-captcha')
            .then(response => response.json())
            .then(data => {
                document.getElementById('captcha-img').innerHTML = data.captcha;
            });
    });
    </script>

</body>
</html>
