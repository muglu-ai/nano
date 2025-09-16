@extends('layouts.auth_new')
@section('title', 'Register - ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR'))
@section('content')

    <div class="container mb-4">
        <div class="row mt-lg-n12 mt-md-n12 mt-n12 justify-content-center">
            <div class="col-xl-4 col-lg-5 col-md-7 mx-auto mt-5">
                <div class="card mt-5">
                    <div
                        class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 mb-4 mt-3"
                    >
                        <div
                            class="bg-gradient-success shadow-success border-radius-lg py-3 pe-1 text-center py-4"
                        >
                            <h4 class="font-weight-bolder text-white mt-1">Register</h4>
                            <p class="mb-1 text-sm text-white">
                                Enter your details to register
                            </p>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="p-4 mb-4 text-red-700 bg-red-100 rounded">
                                {{ $errors->first() }}
                            </div>
                        @endif
                        <form method="POST" action="{{ route('register') }}" role="form" class="text-start">
                            @csrf
                        <div class="input-group input-group-static mb-4">
                            <label for="name">Name:</label>
                            <input type="text" name="name" class="form-control validate" required />
                        </div>
                        <div class="input-group input-group-static mb-4">
                            <label for="email">Email:</label>
                            <input type="email" name="email" class="form-control validate" required />
                        </div>
                        <div class="input-group input-group-static mb-4">
                            <label for="password">Password:</label>
                            <input type="password" id="password" name="password" class="form-control validate" required minlength="6" /><br />
                        </div>
                        <div class="input-group input-group-static mb-4">
                            <label for="password_confirmation">Confirm Password:</label>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="form-control validate"
                                required
                                minlength="6"
                            />
                        </div>

                        <div class="text-center">
                            <button
                                onclick="return validatePasswords()"
                                type="submit"
                                class="btn bg-gradient-dark w-100 mt-3 mb-0"
                            >
                                Register
                            </button>
                        </div>
                            <div class="card-footer text-center pt-0 px-lg-2 px-1">
                                <p class="mb-1 text-sm mx-auto">
                      Already have an account?<br />
                      <a href="{{route('login')}}" class="text-success text-gradient font-weight-bold">Click here to login</a>
                                </p>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        function validatePasswords() {
            var password = document.getElementById('password').value;
            var confirmPassword = document.getElementById('password_confirmation').value;
            if (password !== confirmPassword) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Passwords do not match!',
                });
                return false;
            }
            return true;
        }
    </script>


</main>

<script>
    document.getElementById("currentYear").textContent = new Date().getFullYear();
</script>

<style>
    .footer {
        background-color: #3f504e; /* Dark background for a strong footer band */
        padding: 20px 0;
        border-top: 3px solid #ffffff20; /* Light border for a sleek look */
        box-shadow: 0px -2px 5px rgba(0, 0, 0, 0.2); /* Soft shadow effect */
    }

    .separator {
        width: 2px !important;  /* Forces exact width */
        height: 25px !important; /* Forces exact height */
        background-color: #FFFFFF;
        margin: 0 10px !important; /* Ensures no extra spacing */
        padding: 0 !important; /* Removes any internal padding */
        display: inline-block; /* Prevents extra spacing issues */
    }

    .text-sm {
        font-size: 14px;
    }

    .nav-link {
        color: #ffffff !important;
        font-weight: 500;
    }

    .nav-link:hover {
        text-decoration: underline;
    }
</style>


@endsection
