<?php

namespace App\Services;

use App\Models\Average;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Classe;
use App\Models\SchoolYear;
use Illuminate\Support\Collection;

class GradeCalculationService
{
    /**
     * Calcule la moyenne d'un élève pour une matière, une période et une année scolaire données
     *
     * @param int $studentId
     * @param string $subject
     * @param string $period
     * @param int $schoolYearId
     * @param int|null $classeId
     * @return float|null
     */
    public function calculateSubjectAverage(int $studentId, string $subject, string $period, int $schoolYearId, ?int $classeId = null): ?float
    {
        $query = Grade::where('student_id', $studentId)
            ->where('subject', $subject)
            ->where('period', $period)
            ->where('school_year_id', $schoolYearId);
            
        if ($classeId) {
            $query->where('classe_id', $classeId);
        }
        
        $grades = $query->get();
        
        if ($grades->isEmpty()) {
            return null;
        }
        
        $totalWeightedValue = 0;
        $totalCoefficient = 0;
        
        foreach ($grades as $grade) {
            $totalWeightedValue += $grade->value * $grade->coefficient;
            $totalCoefficient += $grade->coefficient;
        }
        
        if ($totalCoefficient === 0) {
            return null;
        }
        
        return round($totalWeightedValue / $totalCoefficient, 2);
    }
    
    /**
     * Calcule la moyenne générale d'un élève pour une période et une année scolaire données
     *
     * @param int $studentId
     * @param string $period
     * @param int $schoolYearId
     * @param int|null $classeId
     * @return float|null
     */
    public function calculatePeriodAverage(int $studentId, string $period, int $schoolYearId, ?int $classeId = null): ?float
    {
        $query = Grade::where('student_id', $studentId)
            ->where('period', $period)
            ->where('school_year_id', $schoolYearId);
            
        if ($classeId) {
            $query->where('classe_id', $classeId);
        }
        
        // Récupérer toutes les matières distinctes pour cet élève dans cette période
        $subjects = $query->distinct('subject')->pluck('subject');
        
        if ($subjects->isEmpty()) {
            return null;
        }
        
        $subjectAverages = [];
        
        // Calculer la moyenne pour chaque matière
        foreach ($subjects as $subject) {
            $average = $this->calculateSubjectAverage($studentId, $subject, $period, $schoolYearId, $classeId);
            if ($average !== null) {
                $subjectAverages[] = $average;
            }
        }
        
        if (empty($subjectAverages)) {
            return null;
        }
        
        // Calculer la moyenne générale (moyenne des moyennes par matière)
        return round(array_sum($subjectAverages) / count($subjectAverages), 2);
    }
    
    /**
     * Calcule la moyenne annuelle d'un élève à partir des moyennes trimestrielles
     *
     * @param int $studentId
     * @param int $schoolYearId
     * @param int $classeId
     * @return float|null
     */
    public function calculateAnnualAverage(int $studentId, int $schoolYearId, int $classeId): ?float
    {
        $trimesters = Average::where('student_id', $studentId)
            ->where('classe_id', $classeId)
            ->where('school_year_id', $schoolYearId)
            ->whereIn('period', ['Trimestre 1', 'Trimestre 2', 'Trimestre 3'])
            ->get();
            
        // Si aucun trimestre n'a de moyenne, on ne peut pas calculer la moyenne annuelle
        if ($trimesters->count() == 0) {
            return null;
        }
        
        // Calculer la moyenne des trimestres disponibles
        return round($trimesters->avg('value'), 2);
    }
    
    /**
     * Calcule et enregistre les moyennes trimestrielles pour tous les élèves d'une classe
     *
     * @param int $classeId
     * @param string $period
     * @param int $schoolYearId
     * @return int Nombre de moyennes calculées
     */
    public function calculateAndSaveClassPeriodAverages(int $classeId, string $period, int $schoolYearId): int
    {
        $classe = Classe::findOrFail($classeId);
        $students = $classe->students()
            ->whereHas('attributions', function($query) use ($classeId, $schoolYearId) {
                $query->where('classe_id', $classeId)
                    ->where('school_year_id', $schoolYearId);
            })
            ->get();
            
        $count = 0;
        
        foreach ($students as $student) {
            $average = $this->calculatePeriodAverage($student->id, $period, $schoolYearId, $classeId);
            
            if ($average !== null) {
                Average::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'classe_id' => $classeId,
                        'school_year_id' => $schoolYearId,
                        'period' => $period
                    ],
                    [
                        'value' => $average
                    ]
                );
                
                $count++;
            }
        }
        
        return $count;
    }
    
    /**
     * Calcule et enregistre les moyennes annuelles pour tous les élèves d'une classe
     *
     * @param int $classeId
     * @param int $schoolYearId
     * @return int Nombre de moyennes calculées
     */
    public function calculateAndSaveClassAnnualAverages(int $classeId, int $schoolYearId): int
    {
        $classe = Classe::findOrFail($classeId);
        $students = $classe->students()
            ->whereHas('attributions', function($query) use ($classeId, $schoolYearId) {
                $query->where('classe_id', $classeId)
                    ->where('school_year_id', $schoolYearId);
            })
            ->get();
            
        $count = 0;
        
        foreach ($students as $student) {
            $annualAverage = $this->calculateAnnualAverage($student->id, $schoolYearId, $classeId);
            
            if ($annualAverage !== null) {
                Average::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'classe_id' => $classeId,
                        'school_year_id' => $schoolYearId,
                        'period' => 'Annuelle'
                    ],
                    [
                        'value' => $annualAverage
                    ]
                );
                
                $count++;
            }
        }
        
        return $count;
    }
    
    /**
     * Calcule et attribue les rangs aux élèves d'une classe pour une période donnée
     *
     * @param int $classeId
     * @param string $period
     * @param int $schoolYearId
     * @return void
     */
    public function calculateAndSaveRanks(int $classeId, string $period, int $schoolYearId): void
    {
        $averages = Average::where('classe_id', $classeId)
            ->where('school_year_id', $schoolYearId)
            ->where('period', $period)
            ->orderByDesc('value')
            ->get();
            
        $rank = 1;
        $lastValue = null;
        $lastRank = 1;
        
        foreach ($averages as $average) {
            if ($lastValue !== null && $average->value < $lastValue) {
                $rank = $lastRank + 1;
            }
            
            $average->update(['rank' => $rank]);
            
            $lastValue = $average->value;
            $lastRank = $rank;
            $rank++;
        }
    }
} 