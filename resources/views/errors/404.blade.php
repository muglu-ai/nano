@extends('errors::minimal')

@section('title', __('Not Found'))
@section('code', '404')
@section('message')
<script>
    window.location.replace('https://bengalurutechsummit.com/portal/public');
    // Fallback: show link if JS is disabled
</script>
<noscript>
    <meta http-equiv="refresh" content="0;url=https://bengalurutechsummit.com/portal/public">
    <p>
        {{ __('Not Found') }} —
        <a href="https://bengalurutechsummit.com/portal/public">Continue to portal</a>.
    </p>
</noscript>
@endsection
