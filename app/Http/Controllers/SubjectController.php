<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Classe;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('subjects.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('subjects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:subjects',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        Subject::create($validated);

        return redirect()->route('subjects.index')
            ->with('success', 'Matière créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Subject $subject)
    {
        return view('subjects.show', compact('subject'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subject $subject)
    {
        return view('subjects.edit', compact('subject'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:subjects,name,' . $subject->id,
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $subject->update($validated);

        return redirect()->route('subjects.index')
            ->with('success', 'Matière mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject)
    {
        // Ne jamais supprimer, toujours désactiver
        $subject->update(['is_active' => false]);
        
        return redirect()->route('subjects.index')
            ->with('success', 'La matière a été désactivée avec succès.');
    }

    /**
     * Associe une matière à une classe avec un coefficient spécifique
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function attachToClass(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'classe_id' => 'required|exists:classes,id',
            'coefficient' => 'required|numeric|min:0.1|max:10',
        ]);
        
        $subject = Subject::findOrFail($request->subject_id);
        $classe = Classe::findOrFail($request->classe_id);
        
        // Associer la matière à la classe avec le coefficient spécifié
        $classe->subjects()->syncWithoutDetaching([
            $subject->id => ['coefficient' => $request->coefficient]
        ]);
        
        return response()->json([
            'message' => 'Matière associée à la classe avec succès',
            'subject' => $subject->name,
            'classe' => $classe->libelle,
            'coefficient' => $request->coefficient
        ]);
    }
    
    /**
     * Met à jour le coefficient d'une matière pour une classe
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateCoefficient(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'classe_id' => 'required|exists:classes,id',
            'coefficient' => 'required|numeric|min:0.1|max:10',
        ]);
        
        $subject = Subject::findOrFail($request->subject_id);
        $classe = Classe::findOrFail($request->classe_id);
        
        // Vérifier si la matière est déjà associée à la classe
        if (!$classe->subjects()->where('subject_id', $subject->id)->exists()) {
            return response()->json([
                'message' => 'La matière n\'est pas associée à cette classe'
            ], 404);
        }
        
        // Mettre à jour le coefficient
        $classe->subjects()->updateExistingPivot($subject->id, [
            'coefficient' => $request->coefficient
        ]);
        
        return response()->json([
            'message' => 'Coefficient mis à jour avec succès',
            'subject' => $subject->name,
            'classe' => $classe->libelle,
            'coefficient' => $request->coefficient
        ]);
    }
    
    /**
     * Récupère le coefficient d'une matière pour une classe
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getCoefficient(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'classe_id' => 'required|exists:classes,id',
        ]);
        
        $subject = Subject::findOrFail($request->subject_id);
        $classe = Classe::findOrFail($request->classe_id);
        
        // Récupérer la relation pivot
        $pivot = $classe->subjects()->where('subject_id', $subject->id)->first();
        
        if (!$pivot) {
            return response()->json([
                'message' => 'La matière n\'est pas associée à cette classe'
            ], 404);
        }
        
        return response()->json([
            'subject' => $subject->name,
            'classe' => $classe->libelle,
            'coefficient' => $pivot->pivot->coefficient
        ]);
    }
}
