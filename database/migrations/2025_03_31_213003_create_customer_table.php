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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('google_id', 255)->nullable();
            $table->text('google_token')->nullable(); // Diubah ke text karena token biasanya panjang
            $table->string('hp', 15)->nullable(); // Tambahkan kolom hp
            $table->text('alamat')->nullable(); // Diubah ke text untuk alamat yang lebih panjang
            $table->string('pos', 10)->nullable(); // Kode pos biasanya 5-10 digit
            $table->string('foto')->nullable(); // Tambahkan kolom foto
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users') // Pastikan nama tabel users (biasanya plural)
                  ->onDelete('cascade');
            
            // Index untuk pencarian
            $table->index('user_id');
            $table->index('google_id');
            $table->index('hp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};