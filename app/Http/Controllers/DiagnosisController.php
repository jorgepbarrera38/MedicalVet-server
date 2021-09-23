<?php

namespace App\Http\Controllers;

use App\Models\Diagnosis;
use Illuminate\Http\Request;

class DiagnosisController extends Controller
{
    public function __construct () {
        $this->middleware('has.permission:diagnoses.index')->only('index');
        $this->middleware('has.permission:diagnoses.store')->only('store');
        $this->middleware('has.permission:diagnoses.update')->only('update');
        $this->middleware('has.permission:diagnoses.destroy')->only('destroy');
    }

    public function index()
    {
        return Diagnosis::where('name', 'like', '%' . request()->search . '%')
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
            'name' => 'required|max:50|unique:diagnoses,name'
        ], [], [
            'name' => 'Nombre'
        ]);

        $data['user_id'] = auth()->user()->id;
        //return $data;
        return Diagnosis::create($data);
    }

    public function show(Diagnosis $diagnosis) {
        return $diagnosis;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Diagnosis  $diagnosis
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Diagnosis $diagnosis)
    {
        $data = $request->validate([
            'name' => 'required|max:50|unique:diagnoses,name,' . $diagnosis->id
        ], [], [
            'name' => 'Nombre'
        ]);

        $diagnosis->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Diagnosis  $diagnosis
     * @return \Illuminate\Http\Response
     */
    public function destroy(Diagnosis $diagnosis)
    {
        if ($diagnosis->clinicHistoryDiagnoses()->exists()) {
            abort(422);
        }
        $diagnosis->delete();
    }
}
