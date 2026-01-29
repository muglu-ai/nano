@extends('layouts.registration')

@push('head-links')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.min.css">
@endpush

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js"></script>
@endpush
{{--
@section('header-title')
    <h1>Startup Registration</h1>
    <p>{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</p>
@endsection
--}}