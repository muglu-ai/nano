{{--@include('layouts.dashboard')--}}
@section('content')
<h1>Exhibitor Dashboard</h1>
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Logout</button>
</form>
<p>Welcome, {{ Auth::user()->name }}!</p>
@endsection
