<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Pet;
use App\Models\Unity;
use App\Models\Product;
use App\Models\ClinicHistoryPrescription;
use App\Models\Vaccine;
use App\Models\Deworming;
use App\Models\ClinicHistory;
use App\Models\ClinicHistoryService;
use App\Models\ClinicHistoryProduct;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendClinicHistory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Exports\ConsultationsExport;
use App\Exports\ServicesProvidedExport;

class ReportController extends Controller
{
    public function __construct () {
        $this->middleware('has.permission:clinic-histories.show')->only('getClinicHistory');
        $this->middleware('has.permission:clinic-histories.show-prescriptions')->only(['getPrescriptionsFromPet', 'getPrescriptionFromPet', 'exportPrescriptionInPdf']);
        $this->middleware('has.permission:clinic-histories.show-vaccine')->only('getVaccine');
        $this->middleware('has.permission:clinic-histories.show-product-and-services-applied')->only('productsAndServicesApplied');
        $this->middleware('has.permission:reports.consultations')->only(['getConsultations', 'exportConsultationsInExcel']);
        $this->middleware('has.permission:reports.services-provided')->only(['getServicesProvided', 'exportServicesProvidedInExcel']);
    }

    public function getPrescriptionsFromPet (Pet $pet) {
        return ClinicHistoryPrescription::whereHas('clinicHistory.pet', function (Builder $query) use($pet) {
                                                $query->where('id', $pet->id);
                                            })
                                            ->with('clinicHistory:id,number_folio,created_at')
                                            ->orderBy('created_at', 'desc')
                                            ->get(['id', 'clinic_history_id']);
    }

    public function getPrescriptionFromPet ($prescription) {
        $prescription = ClinicHistoryPrescription::whereId($prescription)
                                                ->with('clinicHistoryPrescriptionDetails.product:id,name')
                                                ->with('clinicHistoryPrescriptionDetails:id,prescription_id,product_id,quantity,posology')
                                                ->firstOrFail(['id', 'observations']);

        return $prescription;
    }

    public function exportPrescriptionInPdf ($prescription) {

        $prescription = ClinicHistoryPrescription::whereId($prescription)
                                                ->with('clinicHistoryPrescriptionDetails.product:id,name')
                                                ->with('clinicHistoryPrescriptionDetails:id,prescription_id,product_id,quantity,posology')
                                                ->firstOrFail(['id', 'observations', 'clinic_history_id']);     
                        
        $company = Company::first();

        $pdf = \PDF::loadView('reports.prescription', compact('company', 'prescription'));
        return $pdf->stream('invoice.pdf');                        
                
    }

    public function sendPrescriptionFromPetForEmail ($prescription) {
        if ($company = Company::first()) {

            $prescription = ClinicHistoryPrescription::whereId($prescription)
                                                ->with('clinicHistoryPrescriptionDetails.product:id,name')
                                                ->with('clinicHistoryPrescriptionDetails:id,prescription_id,product_id,quantity,posology')
                                                ->firstOrFail(['id', 'observations', 'clinic_history_id']); 

            //Verificando que el propietario tenga correo
            if (!$prescription->clinicHistory->pet->owner->email) {
                return response()->json(['message' => 'El propietario de la mascota no tiene correo asociado'], 422);
            }

            \Config::set('mail.from.address', $company->email);
            \Config::set('mail.from.name', $company->email);

            \Config::set('mail.mailers.smtp.host', $company->email_host);
            \Config::set('mail.mailers.smtp.port', $company->email_port);
            \Config::set('mail.mailers.smtp.encryption', $company->email_encryption);
            \Config::set('mail.mailers.smtp.username', $company->email);
            \Config::set('mail.mailers.smtp.password', $company->email_password);

            $pdf = \PDF::loadView('reports.prescription', compact('company', 'prescription'));
            Storage::put('temp.pdf', $pdf->output());
            //Fin buscando folios y guardandolos como temporal
            
            Mail::to($prescription->clinicHistory->pet->owner->email)
                        ->send(new SendClinicHistory($company, $prescription->clinicHistory->pet->name, $prescription->clinicHistory->pet->owner, 'receta veterinaria'));
    
            return "Enviado";

        } else {
            return response()->json(['message' => 'No ha digitado los datos de la empresa'], 422);
        }
    }

    public function getVaccine ($pet) {
        $vaccines = Vaccine::where('pet_id', $pet)
                        ->with('clinicHistory:id,number_folio,created_at')
                        ->with('product:id,name')
                        ->with('user:id,identification,names,surnames')
                        ->with('clinicHistory.user:id,identification,username,names,surnames')
                        ->orderBy('id')
                        ->get(['id', 'clinic_history_id', 'product_id', 'next_date', 'created_at', 'user_id']);

        $dewormings = Deworming::where('pet_id', $pet)
                        ->with('clinicHistory:id,number_folio,created_at')
                        ->with('product:id,name')
                        ->with('user:id,identification,names,surnames')
                        ->with('unity:id,description')
                        ->with('clinicHistory.user:id,identification,username,names,surnames')
                        ->orderBy('id')
                        ->get(['id', 'clinic_history_id', 'product_id', 'next_date', 'unity_id', 'dose', 'created_at', 'user_id']);     
                        
        $allVaccines = Product::where('vaccine', true)->orderBy('name')->get(['id', 'name']);
        $allDewormings = Product::where('deworming', true)->orderBy('name')->get(['id', 'name']);
        $allUnities = Unity::orderBy('description')->get(['id', 'description']);
                    
        return [
            'vaccines' => $vaccines,
            'dewormings' => $dewormings,
            'allVaccines' => $allVaccines,
            'allDewormings' => $allDewormings,
            'allUnities' => $allUnities,
        ];
    }

    public function getVaccineInPdf ($pet) {

        $pet = Pet::findOrFail($pet);

        $vaccines = Vaccine::where('pet_id', $pet->id)
                        ->with('clinicHistory:id,number_folio,created_at')
                        ->with('product:id,name')
                        ->with('clinicHistory.user:id,identification,username,names,surnames')
                        ->orderBy('id')
                        ->get(['id', 'clinic_history_id', 'product_id', 'next_date', 'created_at']);

        $dewormings = Deworming::where('pet_id', $pet->id)
                        ->with('clinicHistory:id,number_folio,created_at')
                        ->with('product:id,name')
                        ->with('unity:id,description')
                        ->with('clinicHistory.user:id,identification,username,names,surnames')
                        ->orderBy('id')
                        ->get(['id', 'clinic_history_id', 'product_id', 'next_date', 'unity_id', 'dose', 'created_at']);
                        
        $company = Company::first();

        $pdf = \PDF::loadView('reports.vaccines', compact('company', 'pet', 'vaccines', 'dewormings'));
        return $pdf->stream('invoice.pdf');                        
                
    }

    public function sendVaccinesAndDewormingsForEmail ($pet) {
        if ($company = Company::first()) {

            $pet = Pet::findOrFail($pet);

            $vaccines = Vaccine::where('pet_id', $pet->id)
                            ->with('clinicHistory:id,number_folio,created_at')
                            ->with('product:id,name')
                            ->with('clinicHistory.user:id,identification,username,names,surnames')
                            ->orderBy('id')
                            ->get(['id', 'clinic_history_id', 'product_id', 'next_date', 'created_at']);

            $dewormings = Deworming::where('pet_id', $pet->id)
                            ->with('clinicHistory:id,number_folio,created_at')
                            ->with('product:id,name')
                            ->with('unity:id,description')
                            ->with('clinicHistory.user:id,identification,username,names,surnames')
                            ->orderBy('id')
                            ->get(['id', 'clinic_history_id', 'product_id', 'next_date', 'unity_id', 'dose', 'created_at']);

            //Verificando que el propietario tenga correo
            if (!$pet->owner->email) {
                return response()->json(['message' => 'El propietario de la mascota no tiene correo asociado'], 422);
            }

            \Config::set('mail.from.address', $company->email);
            \Config::set('mail.from.name', $company->email);

            \Config::set('mail.mailers.smtp.host', $company->email_host);
            \Config::set('mail.mailers.smtp.port', $company->email_port);
            \Config::set('mail.mailers.smtp.encryption', $company->email_encryption);
            \Config::set('mail.mailers.smtp.username', $company->email);
            \Config::set('mail.mailers.smtp.password', $company->email_password);

            $pdf = \PDF::loadView('reports.vaccines', compact('company', 'pet', 'vaccines', 'dewormings'));
            Storage::put('temp.pdf', $pdf->output());
            //Fin buscando folios y guardandolos como temporal
            
            Mail::to($pet->owner->email)
                        ->send(new SendClinicHistory($company, $pet->name, $pet->owner, 'Informe de vacunación y desparasitación'));
    
            return "Enviado";

        } else {
            return response()->json(['message' => 'No ha digitado los datos de la empresa'], 422);
        }
    }

    public function clinicHistoryFolios ($pet) {
        return ClinicHistory::where('pet_id', $pet)
                                ->with('consultationPurposes:id,name')
                                ->with('clinicHistoryAttachments.attachmentType:id,name')
                                ->with('clinicHistoryAttachments:id,clinic_history_id,attachment_type_id,file')
                                ->with('user:id,names,surnames')
                                ->orderBy('number_folio', 'desc')
                                ->get(['id', 'number_folio', 'consultation_purposes_id', 'user_id', 'created_at']);

    }

    public function getClinicHistory (Request $request, $pet) {

        $request->validate([
            'folios' => 'required|array'
        ]);

        $folios = [];

        if ($request->folios[0] == 'latest') {
            $folios[0] = ClinicHistory::where('pet_id', $pet)->latest()->first()->number_folio;
        } else {
            $folios = $request->folios;
        }
        
        $clinicHistoriesFromPet = Pet::where('id', $pet)
                                                        ->with('species')
                                                        ->with('owner')
                                                        ->with('clinicHistories.consultationPurposes')
                                                        ->with('clinicHistories.records')
                                                        ->with('clinicHistories.physiologicalConstants')
                                                        ->with('clinicHistories.diagnoses')
                                                        ->with('clinicHistories.vaccines')
                                                        ->with('clinicHistories.dewormings')
                                                        ->with('clinicHistories.dewormings.product')
                                                        ->with('clinicHistories.dewormings.unity')
                                                        ->with('clinicHistories.products')
                                                        ->with('clinicHistories.services')  
                                                        ->with(['clinicHistories' => function ($query) use ($folios) {
                                                            $query->whereIn('number_folio', $folios);
                                                        }])
                                                        ->first(); 
        
        //return $clinicHistoriesFromPet->clinicHistories[0]->vaccines;
        if (!$clinicHistoriesFromPet->clinicHistories()->exists()) {
            return abort(422);
        }

        $company = Company::first();

        $pdf = \PDF::loadView('reports.clinic-history', compact('clinicHistoriesFromPet', 'company'));
        return $pdf->stream('invoice.pdf');
    }

    public function productsAndServicesApplied ($pet) {
        $services = ClinicHistoryService::whereHas('clinicHistory', function (Builder $query) use ($pet) {
                                        $query->where('pet_id', $pet);
                                    })
                                    ->with('service:id,name')
                                    ->with('clinicHistory:id,number_folio,created_at')
                                    ->with('clinicHistory.user:id,identification,username,names,surnames')
                                    ->whereHas('clinicHistory', function (Builder $query) {
                                        $query->whereBetween('created_at', [ request()->dateSince, \Carbon\Carbon::create(request()->dateUntil)->addHours(24)->subMillisecond(1) ]);
                                    })
                                    ->get(['id', 'clinic_history_id', 'service_id', 'quantity', 'observations']);

        $products = ClinicHistoryProduct::whereHas('clinicHistory', function (Builder $query) use ($pet) {
                                        $query->where('pet_id', $pet);
                                    })
                                    ->with('product:id,name')
                                    ->with('unity:id,description')
                                    ->with('clinicHistory:id,number_folio,created_at,user_id')
                                    ->with('clinicHistory.user:id,identification,username,names,surnames')
                                    ->whereHas('clinicHistory', function (Builder $query) {
                                        $query->whereBetween('created_at', [ request()->dateSince, \Carbon\Carbon::create(request()->dateUntil)->addHours(24)->subMillisecond(1) ]);
                                    })
                                    ->get(['id', 'clinic_history_id', 'product_id', 'quantity', 'unity_id', 'observations']);   
                                    
        return [
            'services' => $services,
            'products' => $products
        ];
    }

    public function getProductsAndServicesAppliedInPdf ($pet) {
        
        $pet = Pet::findOrFail($pet);

        $services = ClinicHistoryService::whereHas('clinicHistory', function (Builder $query) use ($pet) {
                                        $query->where('pet_id', $pet->id);
                                    })
                                    ->with('service:id,name')
                                    ->with('clinicHistory:id,number_folio,created_at')
                                    ->with('clinicHistory.user:id,identification,username,names,surnames')
                                    ->whereHas('clinicHistory', function (Builder $query) {
                                        $query->whereBetween('created_at', [ request()->dateSince, \Carbon\Carbon::create(request()->dateUntil)->addHours(24)->subMillisecond(1) ]);
                                    })
                                    ->get(['id', 'clinic_history_id', 'service_id', 'quantity', 'observations']);

        $products = ClinicHistoryProduct::whereHas('clinicHistory', function (Builder $query) use ($pet) {
                                        $query->where('pet_id', $pet->id);
                                    })
                                    ->with('product:id,name')
                                    ->with('unity:id,description')
                                    ->with('clinicHistory:id,number_folio,created_at,user_id')
                                    ->with('clinicHistory.user:id,identification,username,names,surnames')
                                    ->whereHas('clinicHistory', function (Builder $query) {
                                        $query->whereBetween('created_at', [ request()->dateSince, \Carbon\Carbon::create(request()->dateUntil)->addHours(24)->subMillisecond(1) ]);
                                    })
                                    ->get(['id', 'clinic_history_id', 'product_id', 'quantity', 'unity_id', 'observations']);   
                                    
        $company = Company::first();

        $pdf = \PDF::loadView('reports.products-and-services-applied', compact('company', 'pet', 'products', 'services'));
        return $pdf->stream('invoice.pdf');    
                                    
        
    }

    public function getConsultations (Request $request) {
        $request->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date',
        ]);

        $startDate = Carbon::createFromDate($request->startDate);
        $endDate = Carbon::createFromDate($request->endDate)->addHours(24)->subMillisecond(1);

        $clinicHistories = clinicHistory::whereBetween('created_at', [$startDate, $endDate])
                                            ->orderBy('created_at', 'asc')
                                            ->with('consultationPurposes:id,name')
                                            ->with('pet.owner:id,identification,names,surnames')
                                            ->with('pet:id,name,owner_id')
                                            ->with('user:id,identification,names,surnames')
                                            ->get(['id', 'number_folio', 'consultation_purposes_id', 'pet_id','user_id', 'created_at']);

        if (!count($clinicHistories)) {
            return response()->json(['message' => 'No hay registros'], 422);
        }                                            
        return $clinicHistories;
    }
    
    public function exportConsultationsInExcel (Request $request) {
        $request->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date',
        ]);
        
        $startDate = Carbon::createFromDate($request->startDate);
        $endDate = Carbon::createFromDate($request->endDate)->addHours(24)->subMillisecond(1);
        
        $clinicHistories = clinicHistory::whereBetween('created_at', [$startDate, $endDate])
        ->orderBy('created_at', 'asc')
        ->with('consultationPurposes:id,name')
        ->with('pet.owner:id,identification,names,surnames')
        ->with('pet:id,name,owner_id')
        ->with('user:id,identification,names,surnames')
        ->get(['id', 'number_folio', 'reason_consultation', 'consultation_purposes_id', 'pet_id','user_id', 'created_at']);

        return \Excel::download(new ConsultationsExport($clinicHistories), 'consultations.xlsx');
    }

    public function getServicesProvided (Request $request) {
        $request->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date',
        ]);
    
        $startDate = Carbon::createFromDate($request->startDate);
        $endDate = Carbon::createFromDate($request->endDate)->addHours(24)->subMillisecond(1);
    
        $services = ClinicHistoryService::whereBetween('created_at', [$startDate, $endDate])
                                            ->orderBy('created_at', 'asc')
                                            /*->with('consultationPurposes:id,name')*/
                                            ->with('clinicHistory.pet.owner:id,identification,names,surnames')
                                            ->with('clinicHistory.pet:id,name,owner_id')
                                            ->with('clinicHistory.user:id,identification,names,surnames')
                                            ->with('clinicHistory:id,number_folio,consultation_purposes_id,pet_id,user_id,created_at')
                                            ->with('service:id,name')
                                            ->get(['id', 'clinic_history_id', 'service_id', 'quantity', 'observations', 'created_at']);
            
        if (!count($services)) {
            return response()->json(['message' => 'No hay registros'], 422);
        }                       

        return $services;
    }

    public function exportServicesProvidedInExcel (Request $request) {
        $request->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date',
        ]);
    
        $startDate = Carbon::createFromDate($request->startDate);
        $endDate = Carbon::createFromDate($request->endDate)->addHours(24)->subMillisecond(1);
    
        $services = ClinicHistoryService::whereBetween('created_at', [$startDate, $endDate])
                                            ->orderBy('created_at', 'asc')
                                            ->with('clinicHistory.consultationPurposes:id,name')
                                            ->with('clinicHistory.pet.owner:id,identification,names,surnames')
                                            ->with('clinicHistory.pet:id,name,owner_id')
                                            ->with('clinicHistory.user:id,identification,names,surnames')
                                            ->with('clinicHistory:id,number_folio,consultation_purposes_id,pet_id,user_id,created_at')
                                            ->with('service:id,name')
                                            ->get(['id', 'clinic_history_id', 'service_id', 'quantity', 'observations', 'created_at']);
            
        return \Excel::download(new ServicesProvidedExport($services), 'services.xlsx');
    }
}
