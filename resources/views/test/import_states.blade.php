<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import States</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<h2>Select Country & State</h2>

<label for="country">Select Country:</label>
<select id="country" name="country">
    <option value="">--Select Country--</option>
    @foreach($countries as $country)
        <option value="{{ $country->id }}">{{ $country->name }}</option>
    @endforeach
</select>

<label for="state">Select State:</label>
<select id="state" name="state">
    <option value="">--Select State--</option>
</select>

<script>
    $(document).ready(function () {
        $('#country').change(function () {
            var countryId = $(this).val();

            if (countryId) {
                $.ajax({
                    url: "{{ route('get.states') }}",
                    type: "POST",
                    data: {
                        country_id: countryId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (states) {
                        $('#state').empty().append('<option value="">--Select State--</option>');

                        $.each(states, function (key, state) {
                            $('#state').append('<option value="' + state.id + '">' + state.name + '</option>');
                        });
                    }
                });
            } else {
                $('#state').empty().append('<option value="">--Select State--</option>');
            }
        });
    });
</script>

</body>
</html>
