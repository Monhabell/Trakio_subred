<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('error_imports', function (Blueprint $table) {
            $table->unsignedBigInteger('format_id')->nullable()->after('environment_id');
            $table->foreign('format_id')->references('id')->on('formats')->onDelete('set null');
            $table->string('section')->nullable()->after('format_id');
        });
    }

    public function down(): void
    {
        Schema::table('error_imports', function (Blueprint $table) {
            $table->dropForeign(['format_id']);
            $table->dropColumn(['format_id', 'section']);
        });
    }
};
