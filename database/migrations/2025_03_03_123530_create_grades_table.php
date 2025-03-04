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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('school_year_id')->constrained()->onDelete('cascade');
            $table->foreignId('classe_id')->constrained()->onDelete('cascade');
            $table->string('subject'); // Matière
            $table->string('period')->nullable(); // Trimestre ou semestre
            $table->decimal('value', 5, 2); // Valeur de la note (sur 20)
            $table->decimal('coefficient', 3, 1)->default(1.0); // Coefficient de la note
            $table->text('comment')->nullable(); // Commentaire optionnel
            $table->string('type')->default('exam'); // Type: exam, devoir, contrôle, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
