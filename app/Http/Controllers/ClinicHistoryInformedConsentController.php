<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pet;
use App\Models\ClinicHistoryInformedConsent;

class ClinicHistoryInformedConsentController extends Controller
{
    public function index () {
        return ClinicHistoryInformedConsent::with(['pet:id,name,owner_id', 'pet.owner:id,identification,names,surnames'])
                        ->orderBy('registration_date', 'desc')->paginate(10);
    }

    public function store (Request $request, Pet $pet) {

        $data = $request->validate([
            'procedure' => 'required|max:400',
            'risks' => 'required|max:400',
            'informed_consent_template_id' => 'required|exists:informed_consent_templates,id',
        ]);

        $data['pet_id'] = $pet->id;
        $data['registration_date'] = now();
        $data['user_id'] = auth()->user()->id;

        ClinicHistoryInformedConsent::create($data);
    }

    public function destroy (ClinicHistoryInformedConsent $informedConsent) {

        $informedConsent->delete();
    }

    public function print (ClinicHistoryInformedConsent $informedConsent) {

        //return $informedConsent->pet;
        //return "test";

        $template = $informedConsent->informedConsentTemplate->template;
        $template = str_replace('{propietarioNombres}', $informedConsent->pet->owner->names . ' ' . $informedConsent->pet->owner->surnames, $template);
        $template = str_replace('{propietarioIdentificacion}', $informedConsent->pet->owner->identification, $template);
        $template = str_replace('{mascotaNombre}', $informedConsent->pet->name, $template);
        $template = str_replace('{propietarioUbicacion}', $informedConsent->pet->owner->city->name . '(' . $informedConsent->pet->owner->city->state->name . ')', $template);
        $template = str_replace('{propietarioDireccion}', $informedConsent->pet->owner->address, $template);
        $template = str_replace('{mascotaEspecie}', $informedConsent->pet->species->name, $template);
        $template = str_replace('{mascotaRaza}', $informedConsent->pet->race->name, $template);
        $template = str_replace('{mascotaSexo}', $informedConsent->pet->sex == 'HEMBRA' ? 'Hembra' : 'Macho', $template);
        $template = str_replace('{procedimiento}', $informedConsent->procedure, $template);
        $template = str_replace('{riesgos}', $informedConsent->risks, $template);
        $template = str_replace('{usuarioNombres}', auth()->user()->names . ' ' . auth()->user()->surnames, $template);
        $template = str_replace('{usuarioMatriculaProfesional}', auth()->user()->professional_register, $template);

        $company = \App\Models\Company::first();

        $pdf = \PDF::loadView('reports.informedConsent', compact('company', 'template'));
        $pdf->setPaper('letter');
        return $pdf->stream('invoice.pdf');
    }
}
