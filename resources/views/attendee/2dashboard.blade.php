@extends('layouts.dashboard')
@section('title', 'Visitor Registration Dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- Summary Cards --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="h3 mb-0">Visitor Registration Dashboard</h3>
        <a href="{{ route('visitor.list') }}" class="btn btn-primary">
            <i class="fas fa-list me-2"></i>View List
        </a>
    </div>

    <div class="flex items-center justify-between mb-2">
        <h4 class="text-xl font-semibold">Quick Numbers</h4>
        {{-- <a href="{{ route('visitor.list') }}" class="btn btn-primary">
            View List
        </a> --}}
    </div>
    <div class="row mb-5">
        {{-- <div class="col-12 col-sm-6 col-md-3 mb-3"> --}}
            <x-dashboard.card title="Total Registrations" value="{{ $summary['total_registrations'] }}" icon="user-plus" />
        {{-- </div> --}}
        {{-- <div class="col-12 col-sm-6 col-md-3 mb-3"> --}}
            <x-dashboard.card title="Today's Registrations" value="{{ $summary['today_registrations'] }}" icon="calendar" />
        {{-- </div> --}}
        {{-- <div class="col-12 col-sm-6 col-md-3 mb-3"> --}}
            <x-dashboard.card title="Countries Represented" value="{{ $summary['countries_represented'] }}" icon="globe" />
        {{-- </div> --}}
        {{-- <div class="col-12 col-sm-6 col-md-3 mb-3"> --}}
            <x-dashboard.card title="Inauguration Applicants" value="{{ $summary['inauguration_applicants'] }}" icon="star" />
        {{-- </div> --}}
    </div>


    {{-- Chart: Registrations Over Time --}}
    <div class="row g-4 mb-4">
        {{-- Chart: Registrations Over Time --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary bg-gradient text-white">
                    <h5 class="card-title mb-0">Registrations Over Time</h5>
                </div>
                <div class="card-body">
                    <div id="dateChart" style="height: 400px;"></div>
                </div>
            </div>
        </div>

        {{-- Chart: Country Representation --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-secondary bg-gradient text-white">
                    <h5 class="card-title mb-0">Country Representation</h5>
                </div>
                <div class="card-body">
                    <div id="countryChart" style="height: 400px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart: Sector Participation (Full Width) --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-success bg-gradient text-white">
            <h5 class="card-title mb-0">Sector-wise Participation</h5>
        </div>
        <div class="card-body">
            <div id="sectorChart" style="height: 600px;"></div>
        </div>
    </div>
</div>


{{-- Highcharts Scripts --}}
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<script>
    // Prepare the data
    const dateLabels = @json($chartData['dates']);
    const dateCounts = @json($chartData['counts']);
    const countryLabels = @json($chartData['countries']);
    const countryCounts = @json($chartData['countryData']);
    const sectorLabels = @json($chartData['sectors']);
    const sectorCounts = @json($chartData['sectorData']);

    // Daily Registrations Line Chart
    Highcharts.chart('dateChart', {
        chart: {
            type: 'areaspline'
        },
        title: {
            text: null
        },
        xAxis: {
            categories: dateLabels,
            title: {
                text: 'Date'
            }
        },
        yAxis: {
            title: {
                text: 'Number of Registrations'
            }
        },
        plotOptions: {
            areaspline: {
                fillOpacity: 0.3,
                dataLabels: {
                    enabled: true,
                    format: '{y}'
                }
            }
        },
        series: [{
            name: 'Registrations',
            data: dateCounts,
            color: '#3b82f6'
        }],
        credits: {
            enabled: false
        }
    });

    // Country Representation Chart
    Highcharts.chart('countryChart', {
        chart: {
            type: 'column'
        },
        title: {
            text: null
        },
        xAxis: {
            categories: countryLabels,
            title: {
                text: 'Countries'
            }
        },
        yAxis: {
            title: {
                text: 'Number of Participants'
            }
        },
        plotOptions: {
            column: {
                colorByPoint: true,
                dataLabels: {
                    enabled: true,
                    format: '{y}',
                    style: {
                        fontWeight: 'bold'
                    }
                }
            }
        },
        tooltip: {
            pointFormat: '<b>{point.y}</b> participants'
        },
        series: [{
            name: 'Participants',
            data: countryCounts,
            showInLegend: false
        }],
        credits: {
            enabled: false
        }
    });

    // Sector-wise Participation Chart
    Highcharts.chart('sectorChart', {
        chart: {
            type: 'bar'
        },
        title: {
            text: null
        },
        xAxis: {
            categories: sectorLabels,
            title: {
                text: null
            }
        },
        yAxis: {
            title: {
                text: 'Number of Participants'
            }
        },
        plotOptions: {
            bar: {
                dataLabels: {
                    enabled: true,
                    format: '{y}',
                    align: 'right',
                    inside: false,
                    style: {
                        fontWeight: 'bold'
                    }
                }
            }
        },
        legend: {
            enabled: false
        },
        tooltip: {
            pointFormat: '<b>{point.y}</b> participants'
        },
        series: [{
            name: 'Participants',
            data: sectorCounts,
            color: '#6366f1'
        }],
        credits: {
            enabled: false
        }
    });
</script>
@endsection
