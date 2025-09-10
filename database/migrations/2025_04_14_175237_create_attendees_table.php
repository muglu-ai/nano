<?php 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendeesTable extends Migration
{
    public function up(): void
    {
        Schema::create('attendees', function (Blueprint $table) {
            $table->id();

            $table->string('unique_id')->unique();
            $table->enum('status', ['pending', 'approved', 'rejected','active'])->default('pending');
            $table->string('badge_category')->nullable();

            $table->string('title')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('designation');
            $table->string('company');
            $table->string('address')->nullable();
            $table->string('country');
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();

            $table->string('mobile');
            $table->string('email');

            $table->json('purpose')->nullable();      // checkbox values
            $table->json('products')->nullable();     // multi-select

            $table->string('business_nature')->nullable();
            $table->string('job_function')->nullable();
            $table->boolean('consent')->default(false);

            $table->timestamps(); // created_at and updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendees');
    }
}
