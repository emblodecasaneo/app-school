<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function level(){
        return $this->belongsTo(Level::class);
    }

    public function attributions(){
        return $this->hasMany(Attributtion::class);
    }


    public function payments(){
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the averages associated with the classe.
     */
    public function averages(){
        return $this->hasMany(Average::class);
    }

    /**
     * Get the students associated with the classe through attributions.
     */
    public function students()
    {
        return $this->hasManyThrough(
            Student::class,
            Attributtion::class,
            'classe_id', // Clé étrangère sur la table attributtions
            'id', // Clé primaire sur la table students
            'id', // Clé primaire sur la table classes
            'student_id' // Clé étrangère sur la table attributtions
        )->select('students.*'); // Sélectionner explicitement toutes les colonnes de la table students
    }

    /**
     * Get the subjects associated with the classe.
     */
    public function subjects()
    {
        // Relation many-to-many avec la table des matières
        return $this->belongsToMany(Subject::class, 'classe_subject')
            ->withPivot('coefficient')
            ->withTimestamps()
            ->select('subjects.id', 'subjects.name', 'subjects.description', 'subjects.category', 'subjects.is_active', 'subjects.created_at', 'subjects.updated_at');
    }
    
    /**
     * Ajoute une matière à la classe avec un coefficient spécifique
     * 
     * @param int $subjectId
     * @param float $coefficient
     * @return void
     */
    public function addSubject($subjectId, $coefficient = 1)
    {
        $this->subjects()->syncWithoutDetaching([
            $subjectId => ['coefficient' => $coefficient]
        ]);
    }
    
    /**
     * Met à jour le coefficient d'une matière pour cette classe
     * 
     * @param int $subjectId
     * @param float $coefficient
     * @return void
     */
    public function updateSubjectCoefficient($subjectId, $coefficient)
    {
        if ($this->subjects()->where('subject_id', $subjectId)->exists()) {
            $this->subjects()->updateExistingPivot($subjectId, ['coefficient' => $coefficient]);
        }
    }
    
    /**
     * Récupère le coefficient d'une matière pour cette classe
     * 
     * @param int $subjectId
     * @return float
     */
    public function getSubjectCoefficient($subjectId)
    {
        $subject = $this->subjects()->where('subject_id', $subjectId)->first();
        return $subject ? $subject->pivot->coefficient : 1;
    }
    
    /**
     * Récupère les matières actives associées à la classe
     * Cette méthode est utilisée pour le calcul des moyennes
     */
    public function getActiveSubjects()
    {
        return $this->subjects()->where('is_active', true)->get();
    }
}
