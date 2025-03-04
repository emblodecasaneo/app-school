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
