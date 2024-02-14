<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;


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
