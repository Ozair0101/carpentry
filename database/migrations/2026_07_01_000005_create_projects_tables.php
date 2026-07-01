<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Domain "Jobs" are stored as `projects` to avoid colliding with Laravel's
// framework `jobs` queue table. The UI labels these records as "Jobs".
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('estimate_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('site_address')->nullable();
            // lead, scheduled, in_progress, on_hold, completed, cancelled
            $table->string('status')->default('scheduled');
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('assigned_to')->nullable();
            $table->decimal('budget', 12, 2)->nullable();
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('project_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->boolean('is_done')->default(false);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('project_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('material'); // material, labour, subcontractor, other
            $table->string('description');
            $table->decimal('qty', 10, 2)->default(1);
            $table->decimal('unit_cost', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->date('incurred_on');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_expenses');
        Schema::dropIfExists('project_tasks');
        Schema::dropIfExists('projects');
    }
};
