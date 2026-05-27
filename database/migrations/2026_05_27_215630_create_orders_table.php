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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('whatsapp_phone');
            $table->string('customer_name')->nullable();
            $table->string('status')->default('collecting');
            $table->foreignId('print_size_id')->nullable()->constrained('print_sizes')->nullOnDelete();
            $table->integer('image_count')->default(0);
            $table->decimal('total_price', 8, 2)->nullable();
            $table->string('payment_method')->nullable(); // store, credit_card
            $table->string('payment_status')->default('pending'); // pending, paid, failed
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
