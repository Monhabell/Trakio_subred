<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // receptions: columnas filtradas frecuentemente sin índice
        Schema::table('receptions', function (Blueprint $table) {
            $table->index('status', 'idx_receptions_status');
            $table->index('created_at', 'idx_receptions_created_at');
            $table->index('fecha_intervencion', 'idx_receptions_fecha_intervencion');
            $table->index('file_number', 'idx_receptions_file_number');
            $table->index(['environment', 'status'], 'idx_receptions_environment_status');
            $table->index(['status', 'created_at'], 'idx_receptions_status_created_at');
            $table->index(['package_id', 'status'], 'idx_receptions_package_status');
        });

        // productivity: filtros combinados dig_id + created_at son muy frecuentes
        Schema::table('productivity', function (Blueprint $table) {
            $table->index('created_at', 'idx_productivity_created_at');
            $table->index(['dig_id', 'created_at'], 'idx_productivity_dig_created_at');
            $table->index('file_id', 'idx_productivity_file_id');
        });

        // reception_status_histories: filtros por new_status y rango de fecha
        Schema::table('reception_status_histories', function (Blueprint $table) {
            $table->index('new_status', 'idx_rsh_new_status');
            $table->index('created_at', 'idx_rsh_created_at');
            $table->index(['reception_id', 'new_status'], 'idx_rsh_reception_new_status');
            $table->index(['reception_id', 'new_status', 'created_at'], 'idx_rsh_reception_new_status_date');
        });

        // notifications: consultas por destinatario, estado y lectura
        Schema::table('notifications', function (Blueprint $table) {
            $table->index('status', 'idx_notifications_status');
            $table->index('read_at', 'idx_notifications_read_at');
            $table->index(['to_user_id', 'status'], 'idx_notifications_to_user_status');
            $table->index(['to_user_id', 'read_at'], 'idx_notifications_to_user_read_at');
        });

        // consecutivos: búsquedas por id_ficha y consecutivo
        Schema::table('consecutivos', function (Blueprint $table) {
            $table->index('id_ficha', 'idx_consecutivos_id_ficha');
            $table->index('consecutivo', 'idx_consecutivos_consecutivo');
        });

        // productivity_calculation: filtros por fecha y usuario
        Schema::table('productivity_calculation', function (Blueprint $table) {
            $table->index('created_at', 'idx_pc_created_at');
            $table->index('user_sds', 'idx_pc_user_sds');
            $table->index('sds_id', 'idx_pc_sds_id');
            $table->index(['user_sds', 'created_at'], 'idx_pc_user_sds_created_at');
        });

        // reception_user: índice compuesto para JOINs eficientes
        Schema::table('reception_user', function (Blueprint $table) {
            $table->index(['reception_id', 'user_id'], 'idx_reception_user_composite');
            $table->index(['user_id', 'reception_id'], 'idx_reception_user_reverse');
        });
    }

    public function down(): void
    {
        Schema::table('receptions', function (Blueprint $table) {
            $table->dropIndex('idx_receptions_status');
            $table->dropIndex('idx_receptions_created_at');
            $table->dropIndex('idx_receptions_fecha_intervencion');
            $table->dropIndex('idx_receptions_file_number');
            $table->dropIndex('idx_receptions_environment_status');
            $table->dropIndex('idx_receptions_status_created_at');
            $table->dropIndex('idx_receptions_package_status');
        });

        Schema::table('productivity', function (Blueprint $table) {
            $table->dropIndex('idx_productivity_created_at');
            $table->dropIndex('idx_productivity_dig_created_at');
            $table->dropIndex('idx_productivity_file_id');
        });

        Schema::table('reception_status_histories', function (Blueprint $table) {
            $table->dropIndex('idx_rsh_new_status');
            $table->dropIndex('idx_rsh_created_at');
            $table->dropIndex('idx_rsh_reception_new_status');
            $table->dropIndex('idx_rsh_reception_new_status_date');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('idx_notifications_status');
            $table->dropIndex('idx_notifications_read_at');
            $table->dropIndex('idx_notifications_to_user_status');
            $table->dropIndex('idx_notifications_to_user_read_at');
        });

        Schema::table('consecutivos', function (Blueprint $table) {
            $table->dropIndex('idx_consecutivos_id_ficha');
            $table->dropIndex('idx_consecutivos_consecutivo');
        });

        Schema::table('productivity_calculation', function (Blueprint $table) {
            $table->dropIndex('idx_pc_created_at');
            $table->dropIndex('idx_pc_user_sds');
            $table->dropIndex('idx_pc_sds_id');
            $table->dropIndex('idx_pc_user_sds_created_at');
        });

        Schema::table('reception_user', function (Blueprint $table) {
            $table->dropIndex('idx_reception_user_composite');
            $table->dropIndex('idx_reception_user_reverse');
        });
    }
};
