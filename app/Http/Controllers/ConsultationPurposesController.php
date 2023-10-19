<?php

namespace App\Http\Controllers;

use App\Models\ConsultationPurposes;
use Illuminate\Http\Request;

class ConsultationPurposesController extends Controller
{
    public function __construct () {
        $this->middleware('has.permission:consultation-purposes.index')->only('index');
        $this->middleware('has.permission:consultation-purposes.store')->only('store');
        $this->middleware('has.permission:consultation-purposes.update')->only('update');
        $this->middleware('has.permission:consultation-purposes.destroy')->only('destroy');
    }

    public function index()
    {
        return ConsultationPurposes::where('name', 'like', '%' . request()->search . '%')
                                        ->orderBy('name')->paginate(7);
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
            'name' => 'required|max:80|unique:consultation_purposes,name',
            'reason_consultation' => 'required|boolean',
            'symptoms' => 'required|boolean',
            'indications' => 'required|boolean',
            'records' => 'required|boolean',
            'physiological_constants' => 'required|boolean',
            'diagnoses' => 'required|boolean',
            'vaccines' => 'required|boolean',
            'dewormings' => 'required|boolean',
            'products_and_services_applied' => 'required|boolean',
            'attachments' => 'required|boolean',
            'anamnesis' => 'required|boolean',
            'prescription' => 'required|boolean',
            'review_by_systems' => 'required|boolean',
        ], [], [
            'name' => 'Nombre'
        ]);

        $data['user_id'] = auth()->user()->id;
        //return $data;
        return ConsultationPurposes::create($data);
    }

    public function show(ConsultationPurposes $consultation_purpose)
    {
        return $consultation_purpose;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ConsultationPurposes  $consultationPurposes
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ConsultationPurposes $consultation_purpose)
    {
        $data = $request->validate([
            'name' => 'required|max:80|unique:consultation_purposes,name,' . $consultation_purpose->id,
            'reason_consultation' => 'required|boolean',
            'symptoms' => 'required|boolean',
            'indications' => 'required|boolean',
            'records' => 'required|boolean',
            'physiological_constants' => 'required|boolean',
            'diagnoses' => 'required|boolean',
            'vaccines' => 'required|boolean',
            'dewormings' => 'required|boolean',
            'products_and_services_applied' => 'required|boolean',
            'attachments' => 'required|boolean',
            'anamnesis' => 'required|boolean',
            'prescription' => 'required|boolean',
            'review_by_systems' => 'required|boolean',
        ], [
            'name' => 'Nombre'
        ]);
        
        $consultation_purpose->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ConsultationPurposes  $consultationPurposes
     * @return \Illuminate\Http\Response
     */
    public function destroy(ConsultationPurposes $consultation_purpose)
    {
        if ($consultation_purpose->clinicHistories()->exists()) {
            return response()->json(['message' => 'No se puede eliminar por integridad referencial'], 422);
        }

        $consultation_purpose->delete();
    }
}
