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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Total required amount (e.g. 500)
            $table->decimal('total_amount', 10, 2);

            // Paid amount (e.g. 300)
            $table->decimal('paid_amount', 10, 2)->default(0);

            // Remaining auto (optional store OR calculate)
            $table->decimal('remaining_amount', 10, 2)->default(0);

            // Status (dynamic)
            $table->enum('status', ['unpaid', 'partial', 'paid'])->default('unpaid');

            $table->enum('month', [
                'jan', 'feb', 'mar', 'apr', 'may', 'jun',
                'jul', 'aug', 'sep', 'oct', 'nov', 'dec',
            ]);

            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
