<div class="container">
    <h2>Event Expo Participation</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('event-participation.store') }}" method="POST">
        @csrf



        <!-- Participation Type -->
        <label for="participation_type">Participation Type</label>

        @foreach($participation_type as $type => $status)
            <div>
                <input type="radio" name="participation_type" value="{{ $type }}" id="participation_{{ $loop->index }}" {{ $status == 'disabled' ? 'disabled' : '' }} required>
                <label for="participation_{{ $loop->index }}">{{ $type }}</label>
            </div>
        @endforeach


        <br>

        <!-- Region -->
        <label for="region">Region</label>
        @foreach(['Indian', 'International'] as $region)
            <div>
                <input type="radio" name="region" value="{{ $region }}" id="region_{{ $loop->index }}" required>
                <label for="region_{{ $loop->index }}">{{ $region }}</label>
            </div>
        @endforeach
<br>
        <!-- Previous Participation -->
        <label for="previous_participation">Previous Participation</label>
        <div>
            <input type="radio" name="previous_participation" value="1" id="previous_participation_yes" required>
            <label for="previous_participation_yes">Yes</label>
        </div>
        <div>
            <input type="radio" name="previous_participation" value="0" id="previous_participation_no" required>
            <label for="previous_participation_no">No</label>
        </div>
<br>
        <!-- Stall Categories -->
        <label for="stall_category">Stall Categories</label>
        @foreach($stall_type as $type)
            <div>
                <input type="radio" name="stall_category" value="{{ $type }}" id="stall_{{ $loop->index }}" required>
                <label for="stall_{{ $loop->index }}">{{ $type }}</label>
            </div>
        @endforeach
<br>
        <!-- Interested SQM -->
        <label for="interested_sqm">Interested SQM</label>
        <input type="number" name="interested_sqm" id="interested_sqm" min="1" required>

        <br>

        <!-- Product Groups -->
        <label for="product_groups">Product Groups</label>
        @foreach($productGroups as $group)
            <div>
                <input type="checkbox" name="product_groups[]" value="{{ $group }}" id="group_{{ $loop->index }}">
                <label for="group_{{ $loop->index }}">{{ $group }}</label>
            </div>
        @endforeach
<br>
        <!-- Sectors -->
        <label for="sectors">Sectors</label>
        @foreach($sectors as $sector)
            <div>
                <input type="checkbox" name="sectors[]" value="{{ $sector->id }}" id="sector_{{ $loop->index }}">
                <label for="sector_{{ $loop->index }}">{{ $sector->name }}</label>
            </div>
        @endforeach
<br>
        <!-- Terms and Conditions -->
        <div>
            <input type="checkbox" name="terms_accepted" id="terms_accepted" required>
            <label for="terms_accepted">I accept the terms and conditions</label>
        </div>

        <button type="submit">Submit</button>
    </form>
</div>
