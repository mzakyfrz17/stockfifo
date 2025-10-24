<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bar', function (Blueprint $table) {
            $table->id();
            $table->string('kd_bar')->unique();
            $table->string('nama');
            $table->string('satuan');
            $table->integer('stok_minimal')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bar');
    }
};
