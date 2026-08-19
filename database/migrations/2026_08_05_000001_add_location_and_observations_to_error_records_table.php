<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('error_records', function (Blueprint $table) {
            $table->string('location_reference')->nullable()->after('username_raw');
            $table->text('observations')->nullable()->after('location_reference');
        });
    }

    public function down(): void
    {
        Schema::table('error_records', function (Blueprint $table) {
            $table->dropColumn(['location_reference', 'observations']);
        });
    }
};
