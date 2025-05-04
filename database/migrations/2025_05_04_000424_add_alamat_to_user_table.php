<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('user', function (Blueprint $table) {  // Pastikan nama tabel 'user'
            $table->text('alamat')->nullable(); // Menambah kolom alamat
        });
    }
    
    public function down()
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn('alamat'); // Menghapus kolom alamat jika rollback
        });
    }
    
};
