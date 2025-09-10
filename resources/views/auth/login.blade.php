@extends('layouts.auth')

@section('title', 'Login')
@section('content')

<div class="mn-content valign-wrapper">
    <main class="mn-inner container">
        <div class="valign">
            <div class="row">
                <div class="col s12 m6 l4 offset-l4 offset-m3">
                    <div class="card white darken-1">
                        <div class="card-content">
                            <span class="card-title center-align">SEMICON 2025 Login</span>
                                    @if ($errors->any())
                                        <div class="p-4 mb-4 text-red-700 bg-red-100 rounded">
                                            {{ $errors->first() }}
                                        </div>
                                    @endif
                            <div class="row">
                                <form  method="POST" action="{{ route('login.process') }}" class="col s12">
                                    @csrf
                                    <div class="input-field col s12">
                                        <label for="email">Email</label>
                                        <input id="email" name="email" type="email" class="validate" required>

                                    </div>
                                    <div class="input-field col s12">
                                        <label for="password">Password</label>
                                        <input id="password" type="password" class="validate" name="password" required>

                                    </div>
                                    <div class="col s12 center-align m-t-sm">
                                        <a href="#forgot-password" class="waves-effect waves-grey btn-flat">Forgotten Password?</a>

                                    </div>
                                    <div class="col s12 center-align m-t-sm">
                                        <button type="submit" class="waves-effect waves-light btn teal">Log In</button>
                                    </div>
                                    <div class="col s12 center-align m-t-sm">
                                        <span>Don't have an account?<br> <a href="{{ route('register.form') }}">Click here to create one</a></span>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection


{{--<div class="flex items-center justify-center min-h-screen bg-gray-100">--}}
{{--    <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-md">--}}
{{--        <h2 class="mb-6 text-xl font-bold text-center">Login</h2>--}}
{{--        @if ($errors->any())--}}
{{--            <div class="p-4 mb-4 text-red-700 bg-red-100 rounded">--}}
{{--                {{ $errors->first() }}--}}
{{--            </div>--}}
{{--        @endif--}}
{{--        <form method="POST" action="{{ route('login.process') }}">--}}
{{--            @csrf--}}
{{--            <div class="mb-4">--}}
{{--                <label for="email" class="block mb-2 text-sm font-medium text-gray-700">Email</label>--}}
{{--                <input type="email" name="email" id="email" class="w-full px-4 py-2 border rounded-lg" required>--}}
{{--            </div>--}}
{{--            <div class="mb-6">--}}
{{--                <label for="password" class="block mb-2 text-sm font-medium text-gray-700">Password</label>--}}
{{--                <input type="password" name="password" id="password" class="w-full px-4 py-2 border rounded-lg" required>--}}
{{--            </div>--}}
{{--            <button type="submit" class="w-full px-4 py-2 font-bold text-white bg-blue-500 rounded-lg hover:bg-blue-600">--}}
{{--                Login--}}
{{--            </button>--}}
{{--        </form>--}}
{{--    </div>--}}
{{--</div>--}}


