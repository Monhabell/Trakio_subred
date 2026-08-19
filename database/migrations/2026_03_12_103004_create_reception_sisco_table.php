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
        Schema::create('reception_sisco', function (Blueprint $table) {
            $table->id();

            $table->foreignId('localidad_id')->nullable()->constrained('localidades')->nullOnDelete();
            $table->foreignId('environment_id')->nullable()->constrained('environments')->nullOnDelete();
            $table->foreignId('subnet_id')->nullable()->constrained('subnets')->nullOnDelete();
            $table->foreignId('format_id')->nullable()->constrained('formats')->nullOnDelete();
            $table->foreignId('package_id')->nullable()->constrained('packages')->nullOnDelete();
            $table->foreignId('delivered_by')->nullable()->constrained('users')->nullOnDelete();

            $table->integer('consecutivo');
            $table->integer('batch_number')->nullable();
            $table->date('intervention_date');

            $table->integer('status');
            $table->integer('order_file')->nullable();

            $table->timestamps();
        });

        Schema::table('reception_user', function (Blueprint $table) {
            $table->unsignedBigInteger('reception_sisco_id')->nullable();
            $table->foreign('reception_sisco_id')->references('id')->on('reception_sisco')->onDelete('set null');
            $table->unsignedBigInteger('reception_id')->nullable()->change();
        });

        Schema::table('reception_status_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('reception_sisco_id')->nullable();
            $table->foreign('reception_sisco_id')->references('id')->on('reception_sisco')->onDelete('set null');
            $table->unsignedBigInteger('reception_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reception_user', function (Blueprint $table) {
            $table->dropForeign(['reception_sisco_id']);
            $table->dropColumn('reception_sisco_id');
            $table->unsignedBigInteger('reception_id')->nullable(false)->change();
        });

        Schema::table('reception_status_histories', function (Blueprint $table) {
            $table->dropForeign(['reception_sisco_id']);
            $table->dropColumn('reception_sisco_id');
            $table->unsignedBigInteger('reception_id')->nullable(false)->change();
        });

        Schema::dropIfExists('reception_sisco');
    }
};
