@extends('layouts.auth')

@section('title', 'Register')
@section('content')
    <div class="mn-content valign-wrapper">
        <main class="mn-inner container">
            <div class="valign">
                <div class="row">
                    <div class="col s12 m9 l6 offset-l4 offset-m6">
                        <div class="card white darken-1">
                            <div class="card-content">
                                <span class="card-title center-align">SEMICON 2025 Register</span>
                                <div class="row">
                                    @if ($errors->any())
                                        <div class="p-4 mb-4 text-red-700 bg-red-100 rounded">
                                            {{ $errors->first() }}
                                        </div>
                                    @endif
                                <form method="POST" action="{{ 'register' }}">
                                    @csrf
                                    <div class="input-field col s12">
                                        <label for="name">Name:</label>
                                        <input type="text" name="name" required>
                                    </div>
                                    <div class="input-field col s12">
                                        <label for="email">Email:</label>
                                        <input type="email" name="email" required>
                                    </div>
                                    <div class="input-field col s12">

                                        <label for="password">Password:</label>
                                        <input type="password" name="password" required><br>
                                    </div>
                                    <div class="input-field col s12">
                                        <label for="password_confirmation">Confirm Password:</label>
                                        <input type="password" name="password_confirmation" required><br>
                                    </div>
{{--                                    <div class="input-field col s12">--}}
{{--                                        <label for="phone">Phone (optional):</label>--}}
{{--                                        <input type="text" name="phone"><br>--}}
{{--                                    </div>--}}
                                    <div class="col s12 center-align m-t-sm">
                                        <button type="submit" class="waves-effect waves-light btn teal">Register</button>
                                    </div>
                                    <div class="col s12 center-align m-t-sm">
                                        <span>Already  have an account?<br> <a href="{{route('login')}}">Click here to login</a></span>
                                    </div>
                                </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
@endsection
