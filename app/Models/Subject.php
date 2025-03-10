<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'is_active'
    ];

    /**
     * Get the classes that have this subject.
     * The pivot table contains the coefficient for each class.
     */
    public function classes()
    {
        return $this->belongsToMany(Classe::class, 'classe_subject')
            ->withPivot('coefficient')
            ->withTimestamps();
    }

    /**
     * Get the coefficient for a specific class
     * 
     * @param int $classeId
     * @return float
     */
    public function getCoefficientForClass($classeId)
    {
        $pivot = $this->classes()->where('classe_id', $classeId)->first();
        return $pivot ? $pivot->pivot->coefficient : 1;
    }

    /**
     * Get the grades associated with this subject.
     */
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
} 