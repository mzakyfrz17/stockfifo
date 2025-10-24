<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bar_keluar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bar_id')->constrained('bar')->onDelete('cascade');
            $table->date('tanggal');
            $table->integer('jumlah');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bar_keluar');
    }
};
