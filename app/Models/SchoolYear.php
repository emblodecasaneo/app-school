<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolYear extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function levels(){
        return $this->hasMany(Level::class);
    }

    public function attributions(){
        return $this->hasMany(Attributtion::class);
    }

    public function payments(){
        return $this->hasMany(Payment::class);
    }
}
