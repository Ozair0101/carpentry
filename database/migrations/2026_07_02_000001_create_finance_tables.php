<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cash / bank accounts money flows through.
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('cash'); // cash, bank
            $table->decimal('opening_balance', 14, 2)->default(0);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Simple income / expense categories.
        Schema::create('transaction_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('kind')->default('expense'); // income, expense
            $table->timestamps();
        });

        // The single money ledger. Every real movement of cash is a row here.
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->string('direction'); // in, out
            $table->decimal('amount', 14, 2);
            $table->date('occurred_on');
            $table->foreignId('category_id')->nullable()->constrained('transaction_categories')->nullOnDelete();
            $table->string('description')->nullable();
            $table->string('reference')->nullable();
            // Polymorphic link back to what created it (invoice payment, supplier
            // bill payment, payroll, advance). Null for manual income/expense.
            $table->nullableMorphs('sourceable');
            // For transfers: the paired transaction id.
            $table->foreignId('transfer_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->timestamps();

            $table->index(['direction', 'occurred_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('transaction_categories');
        Schema::dropIfExists('accounts');
    }
};
