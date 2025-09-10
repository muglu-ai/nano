@extends('layouts.dashboard')
@section('title', 'Visitor Registration Matrix Report')

@section('content')


{{-- resources/views/reports/jobs-matrix.blade.php --}}
@php
    // Fallback color if not mapped
    $defaultBg = '#f7f7f7';
@endphp

<div class="table-responsive">
  <table class="table table-bordered text-center align-middle">
    <thead class="table-dark">
      <tr>
        <th style="width:18%">Job Category</th>
        <th style="width:36%">Job Subcategory</th>
        <th style="width:12%">Count</th>
        <th style="width:12%">Inaug. Count</th>
        <th style="width:12%">Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach($grouped as $category => $group)
        @php
          $rowspan = $group['rowspan'];
          $catBg   = $bgMap[$category] ?? $defaultBg;
          $items   = $group['items'];
        @endphp

        {{-- First row for this category (prints category cell + total with rowspan) --}}
        @if($items->isNotEmpty())
          @php $first = $items->first(); @endphp
          <tr style="background-color: {{ $catBg }}">
            <td rowspan="{{ $rowspan }}" class="fw-bold align-middle">{{ $category }}</td>
            <td class="text-start">{{ $first->job_subcategory }}</td>
            <td>{{ number_format($first->cnt) }}</td>
            <td>{{ number_format($first->inaug_cnt) }}</td>
            <td rowspan="{{ $rowspan }}" class="fw-bold align-middle">
              {{ number_format($group['total']) }}
            </td>
          </tr>

          {{-- Remaining rows (same category) --}}
          @foreach($items->slice(1) as $r)
            <tr style="background-color: {{ $catBg }}">
              <td class="text-start">{{ $r->job_subcategory }}</td>
              <td>{{ number_format($r->cnt) }}</td>
              <td>{{ number_format($r->inaug_cnt) }}</td>
            </tr>
          @endforeach
        @endif
      @endforeach

      {{-- Grand Total --}}
      <tr class="table-dark">
        <td class="text-start fw-bold" colspan="4">Total</td>
        <td class="fw-bold">{{ number_format($grandTotal) }}</td>
      </tr>
    </tbody>
  </table>
</div>
@endsection