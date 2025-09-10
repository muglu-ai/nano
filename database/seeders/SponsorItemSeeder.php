<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SponsorItem;  // Ensure the correct namespace for the model


class SponsorItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        SponsorItem::create([
            'name' => 'State Sponsor',
            'price' => 1000000,  // Example price, adjust as necessary
            'no_of_items' => 1,
            'deliverables' => json_encode([
                'Logo featured on event website, in event program, and marketing materials.',
                'A dedicated mention in the opening and closing speeches.',
                'Banner placement at event (entrance, stage, networking area).',
                'Acknowledgement during sponsor networking dinner.',
                'VIP event passes for sponsor delegates.',
                'Access to VIP areas and priority networking opportunities.',
                'Social media shoutouts and post-event recognition.'
            ]),
            'status' => 'active',
        ]);

        SponsorItem::create([
            'name' => 'Shuttle Sponsor',
            'price' => 500000,  // Example price, adjust as necessary
            'no_of_items' => 1,
            'deliverables' => json_encode([
                'Logo placement on event shuttle buses (exterior and interior).',
                'Official Shuttle Sponsor mention in all event communications.',
                'Acknowledgement during the sponsor networking dinner.',
                'Shuttle passes for sponsor representatives.',
                'Promotional materials inside shuttle buses.',
                'Access to networking events and the sponsor networking dinner.',
                'Thank-you message and recognition in post-event emails and on social media.'
            ]),
            'status' => 'active',
        ]);

        SponsorItem::create([
            'name' => 'Kit Sponsor',
            'price' => 450000,  // Example price, adjust as necessary
            'no_of_items' => 1,
            'deliverables' => json_encode([
                'Logo featured on event kits (bags, folders, notebooks).',
                'Official Kit Sponsor mention in event program and marketing materials.',
                'Acknowledgement during sponsor networking dinner.',
                'Branded promotional items inside event kits.',
                'VIP event passes for sponsor representatives.',
                'Invitations to exclusive networking events.',
                'Thank-you message and sponsor highlights in post-event materials.'
            ]),
            'status' => 'active',
        ]);

        SponsorItem::create([
            'name' => 'Lanyard Sponsor',
            'price' => 300000,  // Example price, adjust as necessary
            'no_of_items' => 1,
            'deliverables' => json_encode([
                'Logo prominently displayed on all event lanyards.',
                'Official Lanyard Sponsor mention in event program and marketing materials.',
                'Acknowledgement during sponsor networking dinner.',
                'VIP event passes for sponsor representatives.',
                'Opportunity to include promotional items in attendee kits.',
                'Invitations to exclusive networking events and sponsor dinner.',
                'Social media shoutouts and post-event thank-you messages.'
            ]),
            'status' => 'active',
        ]);
    }
}
