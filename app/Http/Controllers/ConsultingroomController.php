<?php

namespace App\Http\Controllers;

use App\Models\Consultingroom;
use Illuminate\Http\Request;

class ConsultingroomController extends Controller
{

    public function __construct () {
        $this->middleware('has.permission:consultingrooms.index')->only('index');
        $this->middleware('has.permission:consultingrooms.store')->only('store');
        $this->middleware('has.permission:consultingrooms.update')->only('update');
        $this->middleware('has.permission:consultingrooms.destroy')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Consultingroom::where('name', 'like', '%' . request()->search . '%')->paginate(7);
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
            'name' => 'required|max:80|unique:consultingrooms,name'
        ], [], [
            'name' => 'Nombre'
        ]);
        //return $data;
        return Consultingroom::create($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Consultingroom  $consultingroom
     * @return \Illuminate\Http\Response
     */
    public function show(Consultingroom $consultingroom)
    {
        return $consultingroom;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Consultingroom  $consultingroom
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Consultingroom $consultingroom)
    {
        $data = $request->validate([
            'name' => 'required|max:80|unique:consultingrooms,name,' . $consultingroom->id,
        ], [], [
            'name' => 'Nombre'
        ]);

        $consultingroom->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Consultingroom  $consultingroom
     * @return \Illuminate\Http\Response
     */
    public function destroy(Consultingroom $consultingroom)
    {
        if ($consultingroom->appointments()->exists()) {
            abort(422);
        }
        $consultingroom->delete();
    }
}
