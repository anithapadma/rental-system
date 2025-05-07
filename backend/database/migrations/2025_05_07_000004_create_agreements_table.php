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
        Schema::create('agreements', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('customer');
            $table->string('customerEmail')->nullable();
            $table->string('customerPhone')->nullable();
            $table->date('startDate');
            $table->date('endDate');
            $table->decimal('value', 10, 2);
            $table->enum('status', ['Active', 'Completed', 'Expired', 'Cancelled'])->default('Active');
            $table->string('paymentMethod')->nullable();
            $table->string('paymentStatus')->nullable()->default('Paid');
            $table->text('notes')->nullable();
            $table->date('createdDate')->nullable();
            $table->date('updatedDate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agreements');
    }
};