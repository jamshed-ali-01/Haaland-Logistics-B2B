<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->foreignId('origin_id')->nullable()->constrained('warehouses');
            $table->foreignId('country_id')->nullable()->constrained('countries');
            $table->foreignId('region_id')->nullable()->constrained('regions');
            $table->decimal('volume_cft', 10, 2)->nullable();
            $table->string('service_type')->nullable();
            $table->text('message')->nullable();
            $table->string('status')->default('new'); // new, contacted, converted
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
