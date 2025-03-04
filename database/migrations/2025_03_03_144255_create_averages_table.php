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
        Schema::create('averages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('classe_id')->constrained()->onDelete('cascade');
            $table->foreignId('school_year_id')->constrained()->onDelete('cascade');
            $table->enum('period', ['Trimestre 1', 'Trimestre 2', 'Trimestre 3', 'Annuelle']);
            $table->decimal('value', 5, 2); // Valeur de la moyenne (ex: 12.75)
            $table->integer('rank')->nullable(); // Classement de l'élève
            $table->text('teacher_comment')->nullable(); // Commentaire de l'enseignant
            $table->string('decision')->nullable(); // Décision (Passage, Redoublement, etc.)
            $table->timestamps();
            
            // Contrainte d'unicité pour éviter les doublons
            $table->unique(['student_id', 'classe_id', 'school_year_id', 'period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('averages');
    }
};
