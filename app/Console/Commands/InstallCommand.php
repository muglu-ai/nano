<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class InstallCommand extends Command
{
    protected $signature = 'portal:install {--skip-migrations : Skip running migrations}';
    protected $description = 'Install and configure the Event Portal SaaS application';

    public function handle()
    {
        $this->info('🚀 Event Portal SaaS Installation');
        $this->info('=====================================');
        $this->newLine();

        // Check if already installed
        if ($this->isInstalled()) {
            if (!$this->confirm('Application appears to be already installed. Continue anyway?', false)) {
                return 0;
            }
        }

        // Step 1: Run migrations
        if (!$this->option('skip-migrations')) {
            $this->info('📦 Step 1: Running database migrations...');
            try {
                Artisan::call('migrate', ['--force' => true]);
                $this->info('✅ Migrations completed successfully');
            } catch (\Exception $e) {
                $this->error('❌ Migration failed: ' . $e->getMessage());
                return 1;
            }
            $this->newLine();
        }

        // Step 2: Seed countries and states
        $this->info('🌍 Step 2: Seeding countries and states...');
        $apiKey = env('COUNTRY_STATE_API_KEY');
        if (empty($apiKey)) {
            $this->warn('⚠️  COUNTRY_STATE_API_KEY not found in .env file.');
            $this->info('💡 To fetch from API, add COUNTRY_STATE_API_KEY=your_api_key to your .env file.');
            $this->info('   The seeder will use fallback methods (JSON file or basic data).');
        }
        try {
            Artisan::call('db:seed', ['--class' => 'CountryStateSeeder', '--force' => true]);
            $this->info('✅ Countries and states seeded successfully');
        } catch (\Exception $e) {
            $this->warn('⚠️  Country/State seeder not found or failed. You may need to run it manually.');
            $this->warn('   Error: ' . $e->getMessage());
        }
        $this->newLine();

        // Step 3: Create super admin
        $this->info('👤 Step 3: Creating super admin account...');
        $this->createSuperAdmin();
        $this->newLine();

        // Step 4: Create default event configuration
        $this->info('⚙️  Step 4: Creating default event configuration...');
        $this->createDefaultEventConfig();
        $this->newLine();

        // Step 5: Seed default sectors
        $this->info('📋 Step 5: Seeding default sectors and organization types...');
        $this->seedDefaultSectors();
        $this->newLine();

        $this->info('✅ Installation completed successfully!');
        $this->newLine();
        $this->info('📝 Next steps:');
        $this->info('   1. Log in as super admin to configure your event');
        $this->info('   2. Visit /super-admin/event-config to set event details');
        $this->info('   3. Visit /super-admin/sectors to manage sectors and organization types');
        $this->newLine();

        return 0;
    }

    private function isInstalled(): bool
    {
        try {
            return DB::table('users')->where('role', 'super-admin')->exists();
        } catch (\Exception $e) {
            return false;
        }
    }

    private function createSuperAdmin()
    {
        $email = $this->ask('Super Admin Email', 'admin@example.com');
        $name = $this->ask('Super Admin Name', 'Super Admin');
        $password = $this->secret('Super Admin Password (min 8 characters)');

        if (strlen($password) < 8) {
            $this->error('Password must be at least 8 characters');
            $password = $this->secret('Please enter a valid password');
        }

        try {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make($password),
                    'role' => 'super-admin',
                    'email_verified_at' => now(),
                ]
            );
            $this->info("✅ Super admin created: {$email}");
        } catch (\Exception $e) {
            $this->error('❌ Failed to create super admin: ' . $e->getMessage());
        }
    }

    private function createDefaultEventConfig()
    {
        try {
            if (!DB::getSchemaBuilder()->hasTable('event_configurations')) {
                $this->warn('⚠️  event_configurations table does not exist. Run migrations first.');
                return;
            }

            $defaults = [
                'event_name' => 'Bengaluru Tech Summit',
                'event_year' => '2025',
                'short_name' => 'BTS',
                'event_website' => 'https://www.bengalurutechsummit.com',
                'event_date_start' => '19-11-2025',
                'event_date_end' => '21-11-2025',
                'event_venue' => 'Bengaluru International Exhibition Centre (BIEC), Bengaluru, India',
                'organizer_name' => 'MM Activ Sci-Tech Communications',
                'organizer_email' => 'enquiry@bengalurutechsummit.com',
                'organizer_phone' => '+91-8069328400',
                'organizer_website' => 'https://mmactiv.in/',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            DB::table('event_configurations')->updateOrInsert(
                ['id' => 1],
                $defaults
            );

            $this->info('✅ Default event configuration created');
        } catch (\Exception $e) {
            $this->warn('⚠️  Could not create event configuration: ' . $e->getMessage());
        }
    }

    private function seedDefaultSectors()
    {
        try {
            if (!DB::getSchemaBuilder()->hasTable('sectors')) {
                $this->warn('⚠️  sectors table does not exist.');
                return;
            }

            $sectors = config('constants.sectors', []);
            foreach ($sectors as $sectorName) {
                DB::table('sectors')->updateOrInsert(
                    ['name' => $sectorName],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }

            $this->info('✅ Default sectors seeded');
        } catch (\Exception $e) {
            $this->warn('⚠️  Could not seed sectors: ' . $e->getMessage());
        }
    }
}
