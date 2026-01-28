<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Special booth types (non-numeric interested_sqm / allocated_sqm)
    |--------------------------------------------------------------------------
    | When interested_sqm or allocated_sqm is one of these strings, allocation
    | uses the counts below. Roles are resolved to ticket_type_id via
    | ticket_type_roles (name/slug matching) or by ticket_type_ids override.
    |
    | Keys: display value (case-insensitive match).
    | Values: [ 'exhibitor' => count, 'standard_pass' => count ] or
    |         [ 'ticket_type_ids' => [ id => count, ... ] ] for explicit IDs.
    */
    'special_booth_types' => [
        'POD' => ['exhibitor' => 1, 'standard_pass' => 1],
        'Booth / POD' => ['exhibitor' => 1, 'standard_pass' => 1],
        'Startup Booth' => ['exhibitor' => 1, 'standard_pass' => 1],
        'Booth' => ['exhibitor' => 1, 'standard_pass' => 1],
    ],

    /*
    |--------------------------------------------------------------------------
    | How to resolve "exhibitor" and "standard_pass" to ticket_type_id
    |--------------------------------------------------------------------------
    | Used when special_booth_types uses role names. First matching ticket
    | type (by slug or name) per role is used. Optionally set
    | ticket_type_ids in special_booth_types to bypass resolution.
    */
    'ticket_type_roles' => [
        'exhibitor' => [
            'name_contains' => ['exhibitor', 'stall', 'stall manning', 'exhibitor pass'],
            'slug_contains' => ['exhibitor', 'stall'],
        ],
        'standard_pass' => [
            'name_contains' => ['standard', 'delegate', 'complimentary', 'inaugural', 'conference delegate'],
            'slug_contains' => ['standard', 'delegate', 'complimentary', 'inaugural'],
        ],
    ],
];
