<?php

namespace App\Http\Controllers;

use App\Models\InformedConsentTemplate;
use Illuminate\Http\Request;

class InformedConsentTemplateController extends Controller
{
    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->middleware('has.permission:informed-consent-templates.index')->only('index');
        $this->middleware('has.permission:informed-consent-templates.store')->only('store');
        $this->middleware('has.permission:informed-consent-templates.update')->only('update');
        $this->middleware('has.permission:informed-consent-templates.destroy')->only('destroy');
        $this->middleware('has.permission:informed-consent-templates.customize')->only('customize');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return InformedConsentTemplate::where('name', 'like', '%' . request()->search . '%')->paginate(7);
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
            'name' => 'required|max:80|unique:informed_consent_templates,name'
        ], [], [
            'name' => 'Nombre'
        ]);

        InformedConsentTemplate::create($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\InformedConsentTemplate  $informedConsentTemplate
     * @return \Illuminate\Http\Response
     */
    public function show(InformedConsentTemplate $informedConsentTemplate)
    {
        return $informedConsentTemplate;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\InformedConsentTemplate  $informedConsentTemplate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InformedConsentTemplate $informedConsentTemplate)
    {
        $data = $request->validate([
            'name' => 'required|max:80|unique:informed_consent_templates,name,' . $informedConsentTemplate->id,
        ], [], [
            'name' => 'Nombre'
        ]);

        $informedConsentTemplate->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\InformedConsentTemplate  $informedConsentTemplate
     * @return \Illuminate\Http\Response
     */
    public function destroy(InformedConsentTemplate $informedConsentTemplate)
    {
        $informedConsentTemplate->delete();
    }

    public function customize (InformedConsentTemplate $informedConsentTemplate, Request $request) {
        $data = $request->validate([
            'template' => 'required|max:20000'
        ], [], [
            'template' => 'Plantilla'
        ]);

        $informedConsentTemplate->update($data);
        return $request->all();
    }
}
