@extends('layouts.auth_new')
@section('title', 'Login - ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR'))
@section('content')
<div class="container px-3">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-lg border-0 overflow-hidden" style="border-radius: 0.75rem;">
                <div class="card-header p-0 border-0">
                    <div class="bg-gradient-success text-center py-4 px-3" style="border-radius: 0.75rem 0.75rem 0 0;">
                        <h4 class="font-weight-bolder text-white mb-1">Sign In</h4>
                        <p class="mb-0 text-white opacity-90 small">Enter your email and password to Sign In</p>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('login.process') }}" role="form" class="text-start">
                        @csrf
                        @if ($errors->any())
                            <div class="p-4 mb-2 text-center text-danger bg-green-100 rounded">
                                {{ $errors->first() }}
                            </div>
                        @endif
                        @if (session('message'))
                            <div class="p-4 mb-2  text-center text-success  bg-green-100 rounded">
                                {{ session('message') }}
                            </div>
                        @endif
                        @if (session('success'))
                            <div class="p-4 mb-2 text-center text-success rounded">
                                {{ session('success') }}
                            </div>
                        @endif
                        <div class="input-group input-group-static mb-4 ">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control validate"  required>
                        </div>
                        <div class="input-group input-group-static mb-4">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control validate" required>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn bg-gradient-dark w-100 mt-3 mb-0">Sign in</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center bg-white pt-0 pb-4 px-4 border-0" style="border-radius: 0 0 0.75rem 0.75rem;">
                    @if (now()->lt(\Carbon\Carbon::parse(config('constants.LATE_REGISTRATION_DEADLINE'))))
                        <p class="mb-2 text-sm text-secondary">
                            Don't have an account?
                            <a href="{{ config('constants.DEFAULT_REGISTRATION_LINK') }}" target="_blank" class="text-success fw-bold">Sign up</a>
                        </p>
                    @endif
                    <p class="mb-0 text-sm text-secondary">
                        <a href="{{ route('forgot.password') }}" class="text-success fw-bold">Forgot Password?</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
