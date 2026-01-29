<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('del_ticket')) {
            Schema::create('del_ticket', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_type')->nullable();
            $table->string('nationality')->nullable();
            $table->date('early_bird_date')->nullable();
            $table->string('early_bird_price')->nullable();
            $table->string('normal_price')->nullable();
                $table->string('status')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('del_ticket');
    }
};

