<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('facility_id')->constrained('facilities')->onDelete('cascade');
            
            $table->string('category'); // Kerusakan fisik, kebersihan, dll
            $table->text('description');
            
            // Foto opsional, menyimpan path file
            $table->string('photo_path')->nullable(); 
            
            $table->string('status')->default('Baru'); // Baru, Diproses, Selesai, Ditolak
            $table->text('resolution_notes')->nullable(); // Catatan dari petugas setelah perbaikan
            
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};