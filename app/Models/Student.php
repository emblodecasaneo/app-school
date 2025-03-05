<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'date_naissance',
        'lieu_naissance',
        'sexe',
        'adresse',
        'telephone',
        'email',
        'matricule'
    ];

    public function attributtions(){
        return $this->hasMany(Attributtion::class);
    }

    // Ajout de l'alias attributions pour la compatibilité
    public function attributions(){
        return $this->attributtions();
    }

    // Relation avec la classe actuelle de l'élève
    public function classe()
    {
        // Nous devons retourner une relation belongsTo
        // Nous allons utiliser une approche différente en utilisant une relation via l'attribution
        return $this->belongsToMany(Classe::class, 'attributtions', 'student_id', 'classe_id')
            ->withPivot('school_year_id')
            ->wherePivot('school_year_id', SchoolYear::where('active', '1')->first()->id ?? 0);
    }

    public function payments(){
        return $this->hasMany(Payment::class);
    }

    // Relation avec les notes
    public function grades(){
        return $this->hasMany(Grade::class);
    }

    // Méthode pour récupérer les notes d'une année scolaire spécifique
    public function gradesForYear($schoolYearId){
        return $this->grades()->where('school_year_id', $schoolYearId);
    }

    // Méthode pour calculer la moyenne générale pour une année scolaire
    public function calculateAverage($schoolYearId, $period = null)
    {
        $query = $this->grades()->where('school_year_id', $schoolYearId);
        
        if ($period) {
            $query->where('period', $period);
        }
        
        $grades = $query->get();
        
        if ($grades->isEmpty()) {
            return 0;
        }
        
        $totalWeightedValue = 0;
        $totalCoefficient = 0;
        
        foreach ($grades as $grade) {
            $totalWeightedValue += $grade->weighted_value;
            $totalCoefficient += $grade->coefficient;
        }
        
        return $totalCoefficient > 0 ? round($totalWeightedValue / $totalCoefficient, 2) : 0;
    }
}
