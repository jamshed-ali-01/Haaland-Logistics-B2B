<?php
/**
 * Title: Add billable fields to quotes table
 * Description: Adds missing columns for billable volume and rate per CFT to the quotes table to ensure data persistency and correct display.
 */

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
        Schema::table('quotes', function (Blueprint $table) {
            $table->decimal('billable_volume_cft', 10, 2)->after('volume_cft')->nullable();
            $table->decimal('rate_per_cft', 10, 2)->after('billable_volume_cft')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn(['billable_volume_cft', 'rate_per_cft']);
        });
    }
};
