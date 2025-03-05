<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function level(){
        return $this->belongsTo(Level::class);
    }

    public function attributions(){
        return $this->hasMany(Attributtion::class);
    }


    public function payments(){
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the averages associated with the classe.
     */
    public function averages(){
        return $this->hasMany(Average::class);
    }

    /**
     * Get the students associated with the classe through attributions.
     */
    public function students()
    {
        return $this->hasManyThrough(
            Student::class,
            Attributtion::class,
            'classe_id', // Clé étrangère sur la table attributtions
            'id', // Clé primaire sur la table students
            'id', // Clé primaire sur la table classes
            'student_id' // Clé étrangère sur la table attributtions
        );
    }

    /**
     * Get the subjects associated with the classe.
     */
    public function subjects()
    {
        // Pour l'instant, on retourne une collection statique de matières
        // À remplacer par une vraie relation quand la table des matières sera créée
        return collect([
            ['id' => 1, 'name' => 'Français'],
            ['id' => 2, 'name' => 'Mathématiques'],
            ['id' => 3, 'name' => 'Histoire-Géographie'],
            ['id' => 4, 'name' => 'Anglais'],
            ['id' => 5, 'name' => 'Physique-Chimie'],
            ['id' => 6, 'name' => 'SVT'],
            ['id' => 7, 'name' => 'Éducation Physique'],
            ['id' => 8, 'name' => 'Arts Plastiques'],
            ['id' => 9, 'name' => 'Musique'],
        ]);
    }
}
