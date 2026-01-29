<h1>Onsite Sponsorship Participation</h1><div class="card">
    <h2>Lanyard Partner</h2>

    <p>Limit: 10,000</p>
    <p>Price: $5,000</p>
    <label for="lanyard_quantity">Number of items sponsoring:</label>
    <select id="lanyard_quantity" name="lanyard_quantity">
        @for ($i = 1; $i <= 5; $i++)
            <option value="{{ $i }}">{{ $i }}</option>
        @endfor
    </select>
</div>

<div class="card">
    <h2>Kit Sponsorship</h2>
    <p>Deliverables: Custom branded kits for all attendees.</p>
    <p>Limit: 15,000</p>
    <p>Price: $10,000</p>
    <label for="kit_quantity">Number of items sponsoring:</label>
    <select id="kit_quantity" name="kit_quantity">
        @for ($i = 1; $i <= 5; $i++)
            <option value="{{ $i }}">{{ $i }}</option>
        @endfor
    </select>
</div>
