<?php

namespace App\Http\Controllers;

use App\Models\Attributtion;
use App\Http\Requests\StoreAttributtionRequest;
use App\Http\Requests\UpdateAttributtionRequest;
use App\Models\SchoolYear;

class AttributtionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currentYear = SchoolYear::where('active', '1')->first();
        return view('inscriptions.list', compact('currentYear'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('inscriptions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAttributtionRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Attributtion $attributtion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attributtion $attributtion)
    {
        return view('inscriptions.update', compact('attributtion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttributtionRequest $request, Attributtion $attributtion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attributtion $attributtion)
    {
        //
    }
}
