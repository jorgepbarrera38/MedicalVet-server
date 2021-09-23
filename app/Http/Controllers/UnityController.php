<?php

namespace App\Http\Controllers;

use App\Models\Unity;
use Illuminate\Http\Request;

class UnityController extends Controller
{
    public function __construct () {
        $this->middleware('has.permission:unities.index')->only('index');
        $this->middleware('has.permission:unities.store')->only('store');
        $this->middleware('has.permission:unities.update')->only('update');
        $this->middleware('has.permission:unities.destroy')->only('destroy');
    }

    public function index()
    {
        return Unity::where('description', 'like', '%' . request()->search . '%')->paginate(7);
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
            'description' => 'required|max:40|unique:unities,description'
        ], [], [
            'description' => 'Descripción'
        ]);
        //return $data;
        return Unity::create($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Unity  $unity
     * @return \Illuminate\Http\Response
     */
    public function show(Unity $unity)
    {
        return $unity;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Unity  $unity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Unity $unity)
    {
        $data = $request->validate([
            'description' => 'required|max:40|unique:unities,description,' . $unity->id
        ], [], [
            'description' => 'Descripción'
        ]);

        $unity->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Unity  $unity
     * @return \Illuminate\Http\Response
     */
    public function destroy(Unity $unity)
    {
        if ($unity->dewormings()->exists()) {
            abort(422);
        }
        $unity->delete();
    }
}
