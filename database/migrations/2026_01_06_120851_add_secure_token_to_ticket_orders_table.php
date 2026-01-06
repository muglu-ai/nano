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
        Schema::table('ticket_orders', function (Blueprint $table) {
            $table->string('secure_token', 64)->unique()->nullable()->after('order_no');
            $table->index('secure_token');
        });
        
        // Generate tokens for existing orders
        $orders = \App\Models\Ticket\TicketOrder::whereNull('secure_token')->get();
        foreach ($orders as $order) {
            $order->secure_token = bin2hex(random_bytes(32));
            $order->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_orders', function (Blueprint $table) {
            $table->dropIndex(['secure_token']);
            $table->dropColumn('secure_token');
        });
    }
};
