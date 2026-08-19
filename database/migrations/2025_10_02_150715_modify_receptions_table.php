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
        Schema::table('receptions', function (Blueprint $table) {
            $table->integer('consecutivo')->nullable()->after('required_date');
            $table->unsignedBigInteger('intervention_type_id')->nullable();
            $table->foreign('intervention_type_id')->references('id')->on('intervention_types')->onDelete('cascade');
            $table->unsignedBigInteger('delivered_by')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receptions', function (Blueprint $table) {
            $table->dropForeign('intervention_type_id');
            $table->dropColumn('intervention_type_id');
            $table->dropColumn('consecutivo');
            $table->unsignedBigInteger('delivered_by')->nullable(false)->change();
        });
    }
};
