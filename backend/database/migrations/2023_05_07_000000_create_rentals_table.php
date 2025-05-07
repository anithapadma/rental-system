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
        Schema::create('rentals', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('customer');
            $table->text('items');
            $table->date('start_date');
            $table->date('due_date');
            $table->string('status')->default('Active'); // Active, Returned, Overdue, Maintenance
            $table->decimal('daily_rate', 10, 2)->default(40.00);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};