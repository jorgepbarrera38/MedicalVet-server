<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\Company;
use App\Models\InformedConsentTemplate;
use Illuminate\Http\Request;

class InformedConsentController extends Controller
{
    public function getInformedConsent  (Request $request, Pet $pet, InformedConsentTemplate $informedConsentTemplate) {

        abort_if(!auth()->user()->isVet(), 422, 'El usuario no está definido como veterinario');

        $request->validate([
            'process' => 'nullable|max:400',
            'risks' => 'nullable|max:400',
        ]);

        $template = $informedConsentTemplate->template;
        $template = str_replace('{propietarioNombres}', $pet->owner->names . ' ' . $pet->owner->surnames, $template);
        $template = str_replace('{propietarioIdentificacion}', $pet->owner->identification, $template);
        $template = str_replace('{mascotaNombre}', $pet->name, $template);
        $template = str_replace('{propietarioUbicacion}', $pet->owner->city->name . '(' . $pet->owner->city->state->name . ')', $template);
        $template = str_replace('{propietarioDireccion}', $pet->owner->address, $template);
        $template = str_replace('{mascotaEspecie}', $pet->species->name, $template);
        $template = str_replace('{mascotaRaza}', $pet->race->name, $template);
        $template = str_replace('{mascotaSexo}', $pet->sex == 'HEMBRA' ? 'Hembra' : 'Macho', $template);
        $template = str_replace('{procedimiento}', request()->process, $template);
        $template = str_replace('{riesgos}', request()->risks, $template);
        $template = str_replace('{usuarioNombres}', auth()->user()->names . ' ' . auth()->user()->surnames, $template);
        $template = str_replace('{usuarioMatriculaProfesional}', auth()->user()->professional_register, $template);

        $company = Company::first();

        $pdf = \PDF::loadView('reports.informedConsent', compact('company', 'template'));
        return $pdf->stream('invoice.pdf');
    }

    public function getInformedConsentTemplates  () {
        return InformedConsentTemplate::orderBy('name', 'asc')->get(['id', 'name']);
    }
}
