@props(['index', 'data'])

@php
  $titles = ['mr' => 'Mr.', 'mrs' => 'Mrs.', 'ms' => 'Ms.', 'dr' => 'Dr.', 'prof' => 'Prof.'];
  $jobCategories = ['Industry', 'Government', 'Exhibitor', 'Academic', 'Media', 'Others'];
  $purposes = [
    'Purchase new products and services',
    'Source new vendors for an ongoing project',
    'Join the buyer-seller program & meet potential suppliers',
    'To connect & engage with existing suppliers',
    'Stay up to date with the latest innovations',
    'Compare and Benchmark technologies / solutions'
  ];
@endphp

<div class="card mb-4">
  <div class="card-body">
    <h5>Attendee Information</h5>
    <div class="row g-3">
      <div class="col-md-4">
        <label>Title <span class="important">*</span></label>
        <select name="attendees[{{ $index }}][title]" class="form-control" required>
          <option value="" disabled {{ empty($data['title']) ? 'selected' : '' }}>--- Title ---</option>
          @foreach ($titles as $val => $label)
            <option value="{{ $val }}" {{ $data['title'] === $val ? 'selected' : '' }}>{{ $label }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-md-4">
        <label>First Name <span class="important">*</span></label>
        <input type="text" name="attendees[{{ $index }}][first_name]" class="form-control" required value="{{ $data['first_name'] ?? '' }}">
      </div>

      <div class="col-md-4">
        <label>Last Name <span class="important">*</span></label>
        <input type="text" name="attendees[{{ $index }}][last_name]" class="form-control" required value="{{ $data['last_name'] ?? '' }}">
      </div>

      <!-- Additional fields follow the same pattern -->

      <div class="col-md-4">
        <label>Email Address <span id="verification" class="text-warning">(UNVERIFIED)</span> <span class="important">*</span></label>
        <div class="input-group">
          <input type="email" name="attendees[{{ $index }}][email]" class="form-control" required value="{{ $data['email'] ?? '' }}">
          <button type="button" class="btn btn-outline-primary" onclick="verifyEmail(this, {{ $index }})">Verify</button>
        </div>
      </div>

      <!-- Purpose checkboxes -->
      <div class="col-md-12">
        <label class="form-label d-block">The purpose of your visit:<span class="text-danger">*</span></label>
        <div class="row">
          @foreach ($purposes as $i => $label)
            <div class="col-md-6">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="purpose{{ $i + 1 }}_{{ $index }}" name="attendees[{{ $index }}][purpose][]" value="{{ $label }}" {{ in_array($label, $data['purpose'] ?? []) ? 'checked' : '' }}>
                <label class="form-check-label" for="purpose{{ $i + 1 }}_{{ $index }}">{{ $label }}</label>
              </div>
            </div>
          @endforeach
        </div>
      </div>

      <!-- Additional fields: product categories, business nature, etc. -->

      <input type="hidden" name="attendees[{{ $index }}][source]" value="{{ $data['source'] ?? 'default_source' }}">

    </div>
  </div>
</div>
