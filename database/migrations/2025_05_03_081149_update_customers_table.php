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
        Schema::table('customer', function (Blueprint $table) {
            // Tambahkan kolom hp jika belum ada
            if (!Schema::hasColumn('customer', 'hp')) {
                $table->string('hp', 15)->nullable()->after('google_token');
            }
            
            // Tambahkan kolom foto jika belum ada
            if (!Schema::hasColumn('customer', 'foto')) {
                $table->string('foto')->nullable()->after('pos');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer', function (Blueprint $table) {
            // Hapus kolom jika rollback migration
            $table->dropColumn(['hp', 'foto']);
        });
    }
};