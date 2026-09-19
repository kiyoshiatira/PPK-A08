<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // Kelas, Aula, Lapangan, dll
            $table->string('location');
            $table->integer('capacity');
            $table->text('description')->nullable(); // Bisa kosong
            $table->string('status')->default('aktif'); // aktif, dalam perbaikan, nonaktif
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};