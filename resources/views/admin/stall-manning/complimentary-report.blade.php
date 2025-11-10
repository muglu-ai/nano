@extends('layouts.dashboard')
@section('title', $slug)
@section('content')
<div class="container-fluid py-4">
	<div class="row">
		<div class="col-12">
			<div class="card mb-4">
				<div class="card-body d-flex justify-content-between align-items-center">
					<div>
						<h1 class="h4 mb-0">{{ $slug }}</h1>
						<p class="text-muted small mb-0">Company, allocated passes, used passes, and registrants</p>
					</div>
					<div>
						<a href="{{ route('passes.allocation') }}" class="btn btn-outline-secondary btn-sm">
							<i class="fas fa-arrow-left me-1"></i> Back to Allocation
						</a>
					</div>
				</div>
			</div>

			<div class="card">
				<div class="card-body">
					@if(isset($reportRows) && $reportRows->count())
						<div class="table-responsive">
							<table class="table table-hover align-middle">
								<thead>
								<tr>
									<th style="min-width: 240px;">Company</th>
									<th style="min-width: 140px; text-align: center;">Allocated Passes</th>
									<th style="min-width: 140px; text-align: center;">Used Passes</th>
									<th>Registrations</th>
								</tr>
								</thead>
								<tbody>
								@foreach($reportRows as $row)
									<tr>
										<td>
											<div class="d-flex align-items-center">
												<div class="avatar avatar-sm bg-gradient-primary rounded-circle me-3">
													<span class="text-white fw-bold">
														{{ strtoupper(substr($row->company_name ?? 'NA', 0, 2)) }}
													</span>
												</div>
												<div>
													<div class="fw-semibold">{{ $row->company_name ?? 'N/A' }}</div>
													<small class="text-muted">EP ID: {{ $row->exhibition_participant_id }}</small>
												</div>
											</div>
										</td>
										<td class="text-center">
											<span class="badge bg-info">{{ $row->allocated_passes }}</span>
										</td>
										<td class="text-center">
											<span class="badge bg-success">{{ $row->used_passes }}</span>
										</td>
										<td>
											@if($row->registrations->count())
												<div class="accordion" id="acc-{{ $row->exhibition_participant_id }}">
													<div class="accordion-item">
														<h2 class="accordion-header" id="heading-{{ $row->exhibition_participant_id }}">
															<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $row->exhibition_participant_id }}" aria-expanded="false" aria-controls="collapse-{{ $row->exhibition_participant_id }}">
																View {{ $row->registrations->count() }} registration(s)
															</button>
														</h2>
														<div id="collapse-{{ $row->exhibition_participant_id }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $row->exhibition_participant_id }}" data-bs-parent="#acc-{{ $row->exhibition_participant_id }}">
															<div class="accordion-body">
																<div class="table-responsive">
																	<table class="table table-sm mb-0">
																		<thead>
																		<tr>
																			<th style="min-width: 220px;">Name</th>
																			<th style="min-width: 220px;">Email</th>
																			<th style="min-width: 140px;">Mobile</th>
																			<th style="min-width: 140px;">Ticket</th>
																			<th style="min-width: 120px;">PIN</th>
																		</tr>
																		</thead>
																		<tbody>
																		@foreach($row->registrations as $reg)
																			@php
																				$fullName = trim(($reg->first_name ?? '').' '.($reg->middle_name ?? '').' '.($reg->last_name ?? ''));
																			@endphp
																			<tr>
																				<td>{{ $fullName ?: 'N/A' }}</td>
																				<td>{{ $reg->email ?? 'N/A' }}</td>
																				<td>{{ $reg->mobile ?? 'N/A' }}</td>
																				<td>{{ $reg->ticketType ?? 'N/A' }}</td>
																				<td>{{ $reg->unique_id ?? 'N/A' }}</td>
																			</tr>
																		@endforeach
																		</tbody>
																	</table>
																</div>
															</div>
														</div>
													</div>
												</div>
											@else
												<span class="text-muted">No registrations</span>
											@endif
										</td>
									</tr>
								@endforeach
								</tbody>
							</table>
						</div>
					@else
						<div class="text-center py-5">
							<i class="fas fa-inbox fa-3x text-muted mb-3"></i>
							<p class="text-muted mb-0">No complimentary registrations found.</p>
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

