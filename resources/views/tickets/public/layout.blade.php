@extends('layouts.registration')

@section('header-title')
    <h1>Ticket Registration</h1>
    <p>{{ $event->event_name ?? config('constants.EVENT_NAME') }} {{ $event->event_year ?? config('constants.EVENT_YEAR', date('Y')) }}</p>
@endsection
