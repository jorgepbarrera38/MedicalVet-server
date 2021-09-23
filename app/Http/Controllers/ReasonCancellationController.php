<?php

namespace App\Http\Controllers;

use App\Models\ReasonCancellation;
use Illuminate\Http\Request;

class ReasonCancellationController extends Controller
{
    public function __construct () {
        $this->middleware('has.permission:reason-cancellations.index')->only('index');
        $this->middleware('has.permission:reason-cancellations.store')->only('store');
        $this->middleware('has.permission:reason-cancellations.update')->only('update');
        $this->middleware('has.permission:reason-cancellations.destroy')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ReasonCancellation::where('name', 'like', '%' . request()->search . '%')->orderBy('name')->paginate(7);
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
            'name' => 'required|max:100|unique:reason_cancellations,name'
        ], [], [
            'name' => 'Nombre'
        ]);

        ReasonCancellation::create($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ReasonCancellation  $reasonCancellation
     * @return \Illuminate\Http\Response
     */
    public function show(ReasonCancellation $reasonCancellation)
    {
        return $reasonCancellation;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ReasonCancellation  $reasonCancellation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ReasonCancellation $reasonCancellation)
    {
        $data = $request->validate([
            'name' => 'required|max:100|unique:reason_cancellations,name,' . $reasonCancellation->id
        ], [], [
            'name' => 'Nombre'
        ]);

        $reasonCancellation->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ReasonCancellation  $reasonCancellation
     * @return \Illuminate\Http\Response
     */
    public function destroy(ReasonCancellation $reasonCancellation)
    {
        //
    }
}
