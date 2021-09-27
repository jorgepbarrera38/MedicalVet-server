<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use App\Models\City;
use App\Models\Pet;
use Illuminate\Http\Request;

class OwnerController extends Controller
{
    public function __construct () {
        $this->middleware('has.permission:owners.index')->only('index');
        $this->middleware('has.permission:owners.store')->only('store');
        $this->middleware('has.permission:owners.update')->only('update');
        $this->middleware('has.permission:owners.destroy')->only('destroy');
    }

    public function index()
    {
        return Owner::where('identification', 'like', '%' . request()->search . '%')
                      ->orWhere('names', 'like', '%' . request()->search . '%')
                      ->orWhere('surnames', 'like', '%' . request()->search . '%')
                      ->with('city.state')
                      ->with('pets.species')
                      ->with('pets.race')
                      ->paginate(7);
    }

    public function getPetsFromOwner($owner)
    {
        return Pet::with(['species', 'race'])->where('owner_id', $owner)->get();
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
            'identification' => 'required|max:15|unique:owners,identification',
            'names' => 'required|max:40',
            'surnames' => 'required|max:40',
            'city_id' => 'required|exists:cities,id',
            'address' => 'nullable|max:130',
            'phonenumber' => 'nullable|max:12',
            'phonenumber2' => 'nullable|max:12',
            'email' => 'nullable|email|max:40'
        ], [], [
            'identification' => 'Identificación',
            'names' => 'Nombres',
            'surnames' => 'Apellidos',
            'address' => 'Dirección',
            'phonenumber' => 'Teléfono 1',
            'phonenumber2' => 'Teléfono 2',
            'email' => 'Correo electrónico',
            'city_id' => 'Ciudad'
        ]);

        $data['user_id'] = auth()->user()->id;
        //return $data;
        return Owner::create($data);
    }

    public function show(Owner $owner) {
        $cities = City::with('state')->orderBy('name')->get();
        return [
            'owner' => $owner,
            'cities' => $cities
        ];
    }

    public function getCities() {
        return City::with('state')->orderBy('name')->get();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Owner  $owner
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Owner $owner)
    {
        $data = $request->validate([
            'identification' => 'required|max:15|unique:owners,identification,' . $owner->id,
            'names' => 'required|max:40',
            'surnames' => 'required|max:40',
            'city_id' => 'required|exists:cities,id',
            'address' => 'nullable|max:130',
            'phonenumber' => 'nullable|max:12',
            'phonenumber2' => 'nullable|max:12',
            'email' => 'nullable|email|max:40'
        ], [], [
            'identification' => 'Identificación',
            'names' => 'Nombres',
            'surnames' => 'Apellidos',
            'address' => 'Dirección',
            'phonenumber' => 'Teléfono 1',
            'phonenumber2' => 'Teléfono 2',
            'email' => 'Correo electrónico',
            'city_id' => 'Ciudad'
        ]);

        $owner->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Owner  $owner
     * @return \Illuminate\Http\Response
     */
    public function destroy(Owner $owner)
    {
        if ($owner->pets()->exists()) {
            abort(422);
        }
        $owner->delete();
    }
}
