<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attributtion extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'student_id',
        'classe_id',
        'school_year_id'
    ];

    public function schoolyear(){
        return $this->belongsTo(SchoolYear::class);
    }

    public function student(){
        return $this->belongsTo(Student::class);
    }

    public function classe(){
        return $this->belongsTo(Classe::class);
    }
}
