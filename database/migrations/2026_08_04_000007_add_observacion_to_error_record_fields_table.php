<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('error_record_fields', function (Blueprint $table) {
            $table->text('observacion')->nullable()->after('value');
        });
    }

    public function down(): void
    {
        Schema::table('error_record_fields', function (Blueprint $table) {
            $table->dropColumn('observacion');
        });
    }
};
