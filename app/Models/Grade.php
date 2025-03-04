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
        'subject',
        'period',
        'value',
        'coefficient',
        'comment',
        'type'
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
    
    // Méthode pour calculer la note pondérée (note * coefficient)
    public function getWeightedValueAttribute()
    {
        return $this->value * $this->coefficient;
    }
}
