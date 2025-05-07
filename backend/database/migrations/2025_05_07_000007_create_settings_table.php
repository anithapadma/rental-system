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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('companyName')->nullable();
            $table->string('contactEmail')->nullable();
            $table->string('phoneNumber')->nullable();
            $table->text('address')->nullable();
            $table->integer('defaultRentalPeriod')->default(7);
            $table->decimal('lateFeePercent', 5, 2)->default(5.00);
            $table->decimal('minimumRentalAmount', 10, 2)->default(25.00);
            $table->decimal('securityDepositPercent', 5, 2)->default(20.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};