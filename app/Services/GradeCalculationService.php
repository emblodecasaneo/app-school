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
     * en utilisant le coefficient spécifique à la classe pour cette matière
     *
     * @param int $studentId
     * @param int $subjectId
     * @param string $period
     * @param int $schoolYearId
     * @param int|null $classeId
     * @return float|null
     */
    public function calculateSubjectAverage(int $studentId, int $subjectId, string $period, int $schoolYearId, ?int $classeId = null): ?float
    {
        $query = Grade::where('student_id', $studentId)
            ->where('subject_id', $subjectId)
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
            // Utiliser le coefficient effectif qui prend en compte le coefficient de la matière pour la classe
            $gradeCoefficient = $grade->coefficient;
            $totalWeightedValue += $grade->value * $gradeCoefficient;
            $totalCoefficient += $gradeCoefficient;
        }
        
        if ($totalCoefficient === 0) {
            return null;
        }
        
        return round($totalWeightedValue / $totalCoefficient, 2);
    }
    
    /**
     * Calcule la moyenne générale d'un élève pour une période et une année scolaire données
     * en utilisant les coefficients spécifiques à la classe pour chaque matière
     *
     * @param int $studentId
     * @param string $period
     * @param int $schoolYearId
     * @param int|null $classeId
     * @return float|null
     */
    public function calculatePeriodAverage(int $studentId, string $period, int $schoolYearId, ?int $classeId = null): ?float
    {
        // Vérifier si la classe est spécifiée
        if (!$classeId) {
            // Trouver la classe de l'élève pour cette année scolaire
            $attribution = \App\Models\Attributtion::where('student_id', $studentId)
                ->where('school_year_id', $schoolYearId)
                ->first();
                
            if ($attribution) {
                $classeId = $attribution->classe_id;
            } else {
                return null; // Impossible de calculer la moyenne sans classe
            }
        }
        
        // Récupérer la classe et ses matières
        $classe = \App\Models\Classe::find($classeId);
        if (!$classe) {
            return null;
        }
        
        // Récupérer toutes les matières actives associées à la classe
        $allSubjects = $classe->getActiveSubjects();
        if ($allSubjects->isEmpty()) {
            return null; // Pas de matières associées à cette classe
        }
        
        // Récupérer les notes de l'élève pour cette période
        $grades = Grade::where('student_id', $studentId)
            ->where('period', $period)
            ->where('school_year_id', $schoolYearId)
            ->where('classe_id', $classeId)
            ->get()
            ->groupBy('subject_id');
        
        $totalWeightedValue = 0;
        $totalCoefficient = 0;
        $subjectCount = 0;
        
        // Calculer la moyenne pour chaque matière
        foreach ($allSubjects as $subject) {
            $subjectId = $subject->id;
            // Récupérer le coefficient de la matière depuis la relation pivot
            $subjectCoefficient = $subject->pivot->coefficient ?? 1;
            
            // Vérifier si l'élève a des notes pour cette matière
            if ($grades->has($subjectId)) {
                $subjectGrades = $grades[$subjectId];
                $totalSubjectValue = 0;
                $totalSubjectCoefficient = 0;
                
                foreach ($subjectGrades as $grade) {
                    // Utiliser le coefficient de la note
                    $gradeCoefficient = $grade->coefficient;
                    $totalSubjectValue += $grade->value * $gradeCoefficient;
                    $totalSubjectCoefficient += $gradeCoefficient;
                }
                
                if ($totalSubjectCoefficient > 0) {
                    $subjectAverage = $totalSubjectValue / $totalSubjectCoefficient;
                    // Utiliser le coefficient de la matière pour la classe
                    $totalWeightedValue += $subjectAverage * $subjectCoefficient;
                    $totalCoefficient += $subjectCoefficient;
                    $subjectCount++;
                }
            }
        }
        
        // Retourner la moyenne générale si des notes ont été trouvées
        if ($totalCoefficient > 0) {
            return round($totalWeightedValue / $totalCoefficient, 2);
        }
        
        return null;
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