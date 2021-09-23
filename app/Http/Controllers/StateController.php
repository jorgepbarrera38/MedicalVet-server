<?php

namespace App\Http\Controllers;

use App\Models\State;
use Illuminate\Http\Request;

class StateController extends Controller
{
    public function __construct () {
        $this->middleware('has.permission:states.index')->only('index');
        $this->middleware('has.permission:states.store')->only('store');
        $this->middleware('has.permission:states.update')->only('update');
        $this->middleware('has.permission:states.destroy')->only('destroy');
    }

    public function index()
    {
        return State::where('name', 'like', '%' . request()->search . '%')->paginate(7);
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
            'name' => 'required|max:40|unique:states,name'
        ], [], [
            'name' => 'Nombre'
        ]);

        return State::create($data);
    }

    public function show(State $state) {
        return $state;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\State  $state
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, State $state)
    {
        $data = $request->validate([
            'name' => 'required|max:40|unique:states,name,' . $state->id
        ], [], [
            'name' => 'Nombre'
        ]);

        $state->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\State  $state
     * @return \Illuminate\Http\Response
     */
    public function destroy(State $state)
    {
        /*if ($state->pets()->exists()) {
            abort(422);
        }*/
        $state->delete();
    }
}
