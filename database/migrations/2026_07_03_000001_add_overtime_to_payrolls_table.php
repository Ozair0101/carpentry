<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            // Overtime / extra-work earnings — the reason a payslip can be higher
            // than the base salary. Adds into the net owed for the period.
            $table->decimal('overtime', 12, 2)->default(0)->after('bonus');
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn('overtime');
        });
    }
};
