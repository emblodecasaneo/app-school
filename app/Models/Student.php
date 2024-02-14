<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    public function attributions(){
        return $this->HasMany(Attributtion::class);
    }

    public function payments(){
        return $this->HasMany(Classe::class);
    }
}
