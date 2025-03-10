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
        Schema::create('classe_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classe_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('restrict');
            $table->float('coefficient')->default(1);
            $table->timestamps();
            
            // Assurer qu'une matière ne peut être associée qu'une seule fois à une classe
            $table->unique(['classe_id', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classe_subject');
    }
};
