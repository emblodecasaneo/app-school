<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'student_id',
        'school_year_id',
        'classe_id',
        'subject_id',
        'period',
        'value',
        'coefficient',
        'comment',
        'type',
        'date'
    ];
    
    // Relation avec l'étudiant
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    
    // Relation avec l'année scolaire
    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }
    
    // Relation avec la classe
    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }
    
    // Relation avec la matière
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    
    // Méthode pour calculer la note pondérée (note * coefficient)
    public function getWeightedValueAttribute()
    {
        return $this->value * $this->coefficient;
    }
    
    /**
     * Récupère le nom de la matière
     */
    public function getSubjectNameAttribute()
    {
        if ($this->subject) {
            return $this->subject->name;
        }
        
        return null;
    }
    
    /**
     * Récupère le coefficient de la matière pour la classe de cette note
     * Si le coefficient n'est pas défini dans la note, on le récupère depuis la relation classe-matière
     */
    public function getEffectiveCoefficientAttribute()
    {
        // Si un coefficient est déjà défini dans la note, on l'utilise
        if ($this->coefficient) {
            return $this->coefficient;
        }
        
        // Sinon, on récupère le coefficient depuis la relation classe-matière
        if ($this->classe_id && $this->subject_id) {
            $classe = Classe::find($this->classe_id);
            if ($classe) {
                return $classe->getSubjectCoefficient($this->subject_id);
            }
        }
        
        // Valeur par défaut
        return 1;
    }
}
