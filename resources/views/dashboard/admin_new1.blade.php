@extends('layouts.dashboard')
@section('title', 'Dashboard')
@section('content')
<div class="card mb-4">
	<div class="card-header bg-light">
		<h5 class="mb-0">Analysis for "{{ config('app.name') }}"</h5>
	</div>
	<div class="card-body p-0">
		<table class="table table-bordered mb-0">
			<tbody>
				<tr><td>Total EventDelegates</td><td>{{ $analytics['total_event_delegates'] }}</td></tr>
				<tr>
					<td>Total Registrations</td>
					<td>
						<a href="{{ route('admin.registration.category.details', ['category' => 'all']) }}">
							{{ $analytics['total_registrations'] ?? 0 }}
						</a>
					</td>
				</tr>
				@foreach($analytics['total_normal_registered'] as $categoryName => $count)
				<tr><td>Total {{ $categoryName }} Registration</td><td> <a href="{{ route('admin.registration.category.details', ['category' => $categoryName]) }}">{{ $count }}</a></td></tr>
				@endforeach
				<tr><td>Total Sponsors <b>Registered</b> Delegates</td><td>{{ $analytics['total_sponsors_registered'] }}</td></tr>
				<tr><td>Total Exihibitor <b>Registered</b> Delegates</td><td>{{ $analytics['total_exhibitor_registered'] }}</td></tr>
				<tr><td>Total Speaker <b>Registered</b> Delegates</td><td>{{ $analytics['total_speaker_registered'] }}</td></tr>
				<tr><td>Total Invitee <b>Registered</b> Delegates</td><td>{{ $analytics['total_invitee_registered'] }}</td></tr>
				<tr><td>Total <b>Complimentary</b> Registrations</td><td>{{ $analytics['total_complimentary'] }}</td></tr>
				<tr><td>Total <b>Unpaid</b> Delegates</td><td>{{ $analytics['total_unpaid'] }}</td></tr>
				<tr><td>Total <b>Paid</b> delegates</td><td>{{ $analytics['total_paid'] }}</td></tr>
			
				<tr><td>Total <b>Visitor Pass</b> Registrations</td><td>{{ $analytics['total_visitor_pass'] }}</td></tr>
				<tr><td>Total Number of Enquiries</td><td><a href="{{ route('enquiries.index') }}">{{ $analytics['total_enquiries'] }}</a></td></tr>
			</tbody>
		</table>
	</div>
</div>
@endsection