<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role')->nullable(); // carpenter, foreman, finisher...
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('salary_type')->default('monthly'); // monthly, daily, hourly
            $table->decimal('salary_rate', 12, 2)->default(0);
            $table->date('joined_on')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('name');
        });

        // A salary run for one employee for a period.
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('period_label'); // e.g. "June 2026"
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->decimal('base_amount', 12, 2)->default(0);
            $table->decimal('bonus', 12, 2)->default(0);
            $table->decimal('deductions', 12, 2)->default(0);
            $table->decimal('advance_deducted', 12, 2)->default(0);
            $table->decimal('net_amount', 12, 2)->default(0);
            $table->string('status')->default('pending'); // pending, paid
            $table->date('paid_on')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        // Cash advances / loans given to employees (they owe it back; usually
        // recovered from a future payroll via advance_deducted).
        Schema::create('employee_advances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->decimal('recovered', 12, 2)->default(0);
            $table->date('advanced_on');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_advances');
        Schema::dropIfExists('payrolls');
        Schema::dropIfExists('employees');
    }
};
