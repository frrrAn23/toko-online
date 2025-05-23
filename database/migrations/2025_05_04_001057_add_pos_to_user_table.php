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
        Schema::table('user', function (Blueprint $table) {
            $table->string('pos')->nullable(); // Menambahkan kolom pos
        });
    }
    
    public function down()
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn('pos'); // Menghapus kolom pos saat rollback
        });
    }
    
};
