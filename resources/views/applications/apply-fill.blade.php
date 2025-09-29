@extends('layouts.application')

@section('title', 'Onboarding Form')
@section('content')
    <main class="mn-inner2">
        <div class="row">
            <div class="col s12">
                <div class="page-title">@yield('title')</div>
            </div>
            <div class="col s12 m12 l12">
                <div class="card">
                    <div class="card-content">


                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        <style>
                            .radio-group {
                                display: flex;
                                gap: 15px;
                                align-items: center;
                            }

                            .radio-item {
                                display: flex;
                                align-items: center;
                            }

                            .radio-item input {
                                margin-right: 5px;
                            }

                            .radio-group {
                                display: flex;
                                gap: 20px;
                                align-items: center;
                            }

                            .radio-item {
                                display: flex;
                                align-items: center;
                            }

                            .radio-item input {
                                margin-right: 5px;
                            }
                        </style>
                        @php
                            $isDisabled = $application->submission_status != 'in progress' ? 'disabled' : '';
                            $selectedSectors = $application->sectors->pluck('id')->toArray() ?? [];
                        @endphp
                        <form id="example-form" action="{{ route('event-participation.store') }}" method="POST">
                            @csrf
                            <div>
                                <div class="row">
                                    <div class="col s12">
                                        <ul class="tabs">
                                            <li class="tab col s3"><a href="#"> Personal Info</a></li>
                                            <li class="tab col s3"><a href="#" class="active waves-effect waves-teal">
                                                    Product Info </a></li>
                                            <li class="tab col s3"><a href="#"> Terms and Conditions </a></li>
                                            <li class="tab col s3"><a href="#"> Review</a></li>
                                        </ul>
                                    </div>
                                </div>

                                <section>
                                    <!-- Participation Type -->
                                    <div class="col m12">
                                        <label for="participation_type">Please select an option of your participation in
                                            {{config('constants.EVENT_NAME')}}</label>
                                        <div
                                            style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin-left: 5px;">
                                            @foreach($participation_type as $type => $status)
                                                <div style="display: flex; align-items: center;">
                                                    <input
                                                        type="radio"
                                                        name="participation_type"
                                                        value="{{ $type }}"
                                                        id="participation_{{ $loop->index }}"
                                                        {{ $status == 'disabled' || $isDisabled }}
                                                        {{ old('participation_type', $application->participation_type) == $type ? 'checked' : '' }}
                                                        required
                                                        {{$isDisabled}}
                                                        style="margin-right: 5px;"
                                                    >
                                                    <label for="participation_{{ $loop->index }}">{{ $type }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>


                                    <!-- Region -->
                                    <div class="col m12">
                                        <label for="region">Region</label>
                                        <div style="display: flex; gap: 15px; align-items: center;">
                                            @foreach(['Indian', 'International'] as $region)
                                                <div style="display: flex; align-items: center;">
                                                    <input
                                                        type="radio"
                                                        name="region"
                                                        value="{{ $region }}"
                                                        id="region_{{ $loop->index }}"
                                                        {{ $isDisabled }}
                                                        {{ old('region', $application->region) == $region ? 'checked' : '' }}
                                                        required
                                                        style="margin-right: 5px;"
                                                    >
                                                    <label for="region_{{ $loop->index }}">{{ $region }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>


                                    <!-- Previous Participation -->
                                    <div class="col m12">
                                        <label for="previous_participation">Previous Participation</label>
                                        <div style="display: flex; gap: 20px; align-items: center;">
                                            <div style="display: flex; align-items: center;">
                                                <input
                                                    type="radio"
                                                    name="previous_participation"
                                                    value="1"
                                                    id="previous_participation_yes"
                                                    {{ $isDisabled }}
                                                    {{ old('previous_participation', $application->previous_participation) == 1 ? 'checked' : '' }}
                                                    required
                                                    style="margin-right: 5px;"
                                                >
                                                <label for="previous_participation_yes">Yes</label>
                                            </div>
                                            <div style="display: flex; align-items: center;">
                                                <input
                                                    type="radio"
                                                    name="previous_participation"
                                                    value="0"
                                                    id="previous_participation_no"
                                                    {{ $isDisabled }}
                                                    {{ old('previous_participation', $application->previous_participation) == 0 ? 'checked' : '' }}
                                                    required
                                                    style="margin-right: 5px;"
                                                >
                                                <label for="previous_participation_no">No</label>
                                            </div>
                                        </div>
                                    </div>


                                    <!-- Stall Categories -->
                                    <div class="col m12">
                                        <label for="stall_category">Stall Categories</label>
                                        <div class="radio-group">
                                            @foreach($stall_type as $type)
                                                <div class="radio-item">
                                                    <input
                                                        type="radio"
                                                        name="stall_category"
                                                        value="{{ $type }}"
                                                        id="stall_{{ $loop->index }}"
                                                        {{ $isDisabled }}
                                                        {{ old('stall_category', $application->stall_category) == $type ? 'checked' : '' }}
                                                        required
                                                    >
                                                    <label for="stall_{{ $loop->index }}">{{ $type }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="col m12 ">
                                        <div class="col m4">
                                            <!-- Interested SQM -->
                                            <label for="interested_sqm">Interested SQM</label>
                                            <input
                                                type="number"
                                                name="interested_sqm"
                                                id="interested_sqm"
                                                min="1"
                                                {{ $isDisabled }}
                                                value="{{ old('interested_sqm', $application->interested_sqm) }}"
                                                required
                                            >
                                        </div>
                                        <div class="col m4">
                                        </div>
                                        <div class="col m4">
                                        </div>
                                    </div>

                                    <div class="col m12">
                                        <!-- Product Groups -->
                                        <label for="product_groups">Product Groups</label>
                                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
                                            @foreach($productGroups as $group)
                                                <div style="display: flex; align-items: center;">
                                                    <input
                                                        type="checkbox"
                                                        name="product_groups[]"
                                                        value="{{ $group }}"
                                                        id="group_{{ $loop->index }}"
                                                        {{ $isDisabled }}
                                                        {{ in_array($group, old('product_groups', json_decode($application->product_groups, true) ?? [])) ? 'checked' : '' }}
                                                        style="margin-right: 5px;"
                                                    >
                                                    <label for="group_{{ $loop->index }}">{{ $group }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>


                                    <div class="col m12">

                                        <!-- Sectors -->
                                        <label for="sectors">Sectors</label>
                                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
                                            @foreach($sectors as $sector)
                                                <div  class=" text-dark" style="display: flex; align-items: center ; color:#000">
                                                    <input
                                                        type="checkbox"
                                                        name="sectors[]"
                                                        value="{{ $sector->id }}"
                                                        id="sector_{{ $loop->index }}"
                                                        {{ $isDisabled }}
                                                        {{ in_array($sector->id, old('sectors', $selectedSectors)) ? 'checked' : '' }}
                                                        style="margin-right: 5px;"
                                                    >
                                                    <label for="sector_{{ $loop->index }}">{{ $sector->name }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <!-- Terms and Conditions -->
                                    <div class="m-l-lg">
                                        <input
                                            type="checkbox"
                                            name="terms_accepted"
                                            id="terms_accepted"
                                            {{ $isDisabled }}
                                            {{ old('terms_accepted', $application->terms_accepted) ? 'checked' : '' }}
                                            required
                                        >
                                        <label for="terms_accepted" style="color:#000">I accept the terms and conditions</label>
                                    </div>

                                    @if ($application->submission_status == 'in progress')
                                        <button type="submit" {{ $isDisabled }}>Submit Application</button>
                                    @else
                                        <a href="{{ route('terms') }}" class="btn submit">Next</a>
                                    @endif
                                </section>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
