<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\State;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function __construct () {
        $this->middleware('has.permission:cities.index')->only('index');
        $this->middleware('has.permission:cities.store')->only('store');
        $this->middleware('has.permission:cities.update')->only('update');
        $this->middleware('has.permission:cities.destroy')->only('destroy');
    }

    public function index()
    {
        return City::with('state')->where('name', 'like', '%' . request()->search . '%')->paginate(7);
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
            'name' => 'required|max:40|unique:cities,name',
            'state_id' => 'required|exists:states,id'
        ], [], [
            'name' => 'Nombre',
            'state_id' => 'Departamento'
        ]);

        return City::create($data);
    }

    public function show(City $city) {
        $states = State::orderBy('name')->get(['id', 'name']);
        return [
            'city' => $city,
            'states' => $states
        ];
    }

    public function getStates () {
        return State::orderBy('name')->get(['id', 'name']);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, City $city)
    {
        $data = $request->validate([
            'name' => 'required|max:40|unique:cities,name,' . $city->id,
            'state_id' => 'required|exists:states,id'
        ], [], [
            'name' => 'Nombre',
            'state_id' => 'Departamento'
        ]);

        $city->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function destroy(City $city)
    {
        if ($city->owners()->exists()) {
            abort(422);
        }
        $city->delete();
    }
}

