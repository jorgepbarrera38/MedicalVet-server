<?php

namespace App\Http\Controllers;

use App\Models\Race;
use Illuminate\Http\Request;

class RaceController extends Controller
{
    public function __construct () {
        $this->middleware('has.permission:races.index')->only('index');
        $this->middleware('has.permission:races.store')->only('store');
        $this->middleware('has.permission:races.update')->only('update');
        $this->middleware('has.permission:races.destroy')->only('destroy');
    }

    public function index()
    {
        return Race::where('name', 'like', '%' . request()->search . '%')->paginate(7);
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
            'name' => 'required|max:40|unique:races,name'
        ], [], [
            'name' => 'Nombre'
        ]);

        $data['user_id'] = auth()->user()->id;
        //return $data;
        return Race::create($data);
    }

    public function show(Race $race) {
        return $race;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Race  $race
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Race $race)
    {
        $data = $request->validate([
            'name' => 'required|max:40|unique:races,name,' . $race->id
        ], [], [
            'name' => 'Nombre'
        ]);

        $race->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Race  $race
     * @return \Illuminate\Http\Response
     */
    public function destroy(Race $race)
    {
        if ($race->pets()->exists()) {
            abort(422);
        }
        $race->delete();
    }
}
