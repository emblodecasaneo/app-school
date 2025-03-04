<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Average extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'classe_id',
        'school_year_id',
        'period',
        'value',
        'rank',
        'teacher_comment',
        'decision',
    ];

    /**
     * Get the student associated with the average.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the classe associated with the average.
     */
    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

    /**
     * Get the school year associated with the average.
     */
    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }
}
