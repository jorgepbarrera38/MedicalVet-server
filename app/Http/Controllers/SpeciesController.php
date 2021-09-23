<?php

namespace App\Http\Controllers;

use App\Models\Species;
use App\Models\Pet;
use Illuminate\Http\Request;

class SpeciesController extends Controller
{
    public function __construct () {
        $this->middleware('has.permission:species.index')->only('index');
        $this->middleware('has.permission:species.store')->only('store');
        $this->middleware('has.permission:species.update')->only('update');
        $this->middleware('has.permission:species.destroy')->only('destroy');
    }

    public function index()
    {
        return Species::where('name', 'like', '%' . request()->search . '%')
                        ->paginate(7);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:40|unique:species,name'
        ], [], [
            'name' => 'Nombre'
        ]);

        $data['user_id'] = auth()->user()->id;
        //return $data;
        return Species::create($data);
    }

    public function show(Species $species) {
        return $species;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Species  $species
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Species $species)
    {
        $data = $request->validate([
            'name' => 'required|max:40|unique:species,name,' . $species->id
        ], [], [
            'name' => 'Nombre'
        ]);

        $species->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Species  $species
     * @return \Illuminate\Http\Response
     */
    public function destroy(Species $species)
    {
        if ($species->pets()->exists()) {
            abort(422);
        }
        $species->delete();
    }
}