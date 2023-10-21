<?php

namespace App\Http\Controllers;

use App\Models\ClinicHistory;
use App\Models\Owner;
use App\Models\ConsultationPurposes;
use App\Models\AttachmentType;
use App\Models\Pet;
use App\Models\Product;
use App\Models\Service;
use App\Models\Diagnosis;
use App\Models\Deworming;
use App\Models\Vaccine;
use App\Models\Company;
use App\Models\Parameter;
use App\Models\Record;
use App\Models\Appointment;
use App\Models\ClinicHistoryDiagnosis;
use App\Models\ClinicHistoryProduct;
use App\Models\ClinicHistoryService;
use App\Models\ClinicHistoryAttachment;
use App\Models\ClinicHistoryPrescription;
use App\Models\BillingPendingBilling;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\SendClinicHistory;
use Illuminate\Database\Eloquent\Builder;

class ClinicHistoryController extends Controller
{
    public function __construct () {
        $this->middleware('has.permission:clinic-histories.store')->only('store');
        $this->middleware('has.permission:clinic-histories.edit-quantity-products-applied')->only('updateProductApplication');
        $this->middleware('has.permission:clinic-histories.delete-products-applied')->only('deleteProductApplication');
        $this->middleware('has.permission:clinic-histories.edit-quantity-services-applied')->only('updateServiceApplication');
        $this->middleware('has.permission:clinic-histories.delete-services-applied')->only('deleteServiceApplication');
    }

    public function getOwners()
    {
        //return Owner::orderBy('names')->has('pets')->get();
        return Owner::where('identification', 'like', '%' . request()->q . '%')
                        ->orWhere('names', 'like', '%' . request()->q . '%')
                        ->orWhere('surnames', 'like', '%' . request()->q . '%')
                        ->with('pets')
                        ->withCount('pets')->orderBy('names')->paginate(9);
    }

    public function getOwner()
    {
        //return Owner::orderBy('names')->has('pets')->get();
        return Owner::where('identification', request()->identification)
                            ->withCount('pets')
                            ->with('pets')
                            ->firstOrFail();
    }

    public function getConsultationPurposesAndAttachmentTypes()
    {

        $consultationPurposes = ConsultationPurposes::orderBy('name')->get();
        $attachmentTypes = AttachmentType::orderBy('name', 'asc')->get();

        return [
            'consultationPurposes' => $consultationPurposes,
            'attachmentTypes' => $attachmentTypes,
        ];
    }

    public function getPetFromOwner (Owner $owner) {
        //$petsFromOwner = $o
        return $owner->pets()->orderBy('name', 'asc')->get();
    }

    public function getDataForCreateFolio ($pet) {
        if (!auth()->user()->isVet()) {
            return abort(402);
        }
        //Datos del paciente
        $infoFromPetAndOwner = Pet::with('owner:id,identification,names,surnames,address,phonenumber,email')
                                    ->with('species:id,name')
                                    ->with('race:id,name')
                                    ->select('id', 'name', 'photo', 'owner_id', 'species_id', 'race_id', 'date_born', 'sex', 'color', 'size', 'observations')->findOrFail($pet);

        $diagnosesFromPet = ClinicHistoryDiagnosis::whereHas('clinicHistory', function (Builder $query) use ($pet) {
                                                                $query->where('pet_id', $pet);
                                                            })
                                                    ->with('clinicHistory:id,number_folio,created_at')
                                                    ->with('diagnosis:id,name')
                                                    ->latest()->take(10)->get();

        //$recordsFromPet = ClinicHistory::where('pet_id', $infoFromPetAndOwner->id)->has('records')->with('records')->latest()->take(5)->get();
        $recordsFromPet = Record::whereHas('clinicHistory', function (Builder $query) use ($pet) {
                                                        $query->where('pet_id', $pet);
                                                    })
                                            ->with('clinicHistory:id,number_folio,created_at')
                                            ->latest()->take(10)->get();

        //Datos para diligenciar la historia clínica
        $diagnoses = Diagnosis::orderBy('name')->get(['id', 'name']);
        $vaccines = Product::where('vaccine', true)->orderBy('name')->get(['id', 'name']);
        $products = Product::where('vaccine', false)->where('deworming', false)->orderBy('name')->get(['id', 'name']);
        $services = Service::orderBy('name')->get(['id', 'name']);
        $dewormings = Product::where('deworming', true)->orderBy('name')->get(['id', 'name']);


        $dewormingsCountTotalFromPet = Deworming::where('pet_id', $pet)->count();
        $vaccinesCountTotalFromPet = Vaccine::where('pet_id', $pet)->count();

        return [
            'infoFromPetAndOwner' => $infoFromPetAndOwner,
            'diagnosesFromPet' => $diagnosesFromPet,
            'recordsFromPet' => $recordsFromPet,
            'diagnoses' => $diagnoses,
            'vaccines' => $vaccines,
            'products' => $products,
            'services' => $services,
            'dewormings' => $dewormings,
            'dewormingsCountTotalFromPet' => $dewormingsCountTotalFromPet,
            'vaccinesCountTotalFromPet' => $vaccinesCountTotalFromPet,
        ];
    }

    public function getAppointmentsFromPet ($pet) {
        return Appointment::with(['consultingroom:id,name', 'activity:id,name'])->where('pet_id', $pet)->where('state', 'PENDIENTE')->get();
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //return $request->all();
        if (!auth()->user()->isVet()) {
            return abort(403);
        }

        $appointment = '';

        if ($request->appointment_id) {
            $appointment = Appointment::where('id', $request->appointment_id)->where('pet_id', $request->pet_id)->first();
            if (!$appointment) {
                return response()->json(['errors' => ['appointment_not_exists' => ['La cita no existe o no pertenece a la mascota']]], 422);
            } else if ($appointment->state != 'PENDIENTE') {
                return response()->json(['errors' => ['appointment_is_not_pending' => ['La cita se encuentra en estado: ' . $appointment->state]]], 422);
            } else {

            }
        }

        $consultationPurposes = ConsultationPurposes::where('id', $request->consultation_purposes_id)->firstOrFail();

        $data = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'consultation_purposes_id'=> 'required|exists:consultation_purposes,id',
            'reason_consultation' => ['max:800', Rule::requiredIf($consultationPurposes->reason_consultation)],
            'symptoms' => ['max:800', Rule::requiredIf($consultationPurposes->symptoms)],
            'anamnesis' => ['max:800', Rule::requiredIf($consultationPurposes->anamnesis)],
            'records' => 'array',
            'records.*.description' => 'required|max:100',
            'physiologicalConstants.weight' => ['nullable', 'numeric', 'min:0.1', 'max:500', Rule::requiredIf($consultationPurposes->physiological_constants)],
            'physiologicalConstants.temperature' => ['nullable', 'numeric', 'min:1', 'max:200', Rule::requiredIf($consultationPurposes->physiological_constants)],
            'physiologicalConstants.heart_rate' => ['nullable', 'numeric', 'min:1', 'max:500', Rule::requiredIf($consultationPurposes->physiological_constants)],
            'physiologicalConstants.breathing_frequency' => ['nullable', 'numeric', 'min:1', 'max:500', Rule::requiredIf($consultationPurposes->physiological_constants)],


            'reviewBySystems.respiratorySystem.state' => ['nullable', Rule::in(['Normal', 'Anormal']), Rule::requiredIf($consultationPurposes->review_by_systems)],
            'reviewBySystems.respiratorySystem.observations' => 'nullable|max:1000|required_if:reviewBySystems.respiratorySystem.state,Anormal',

            'reviewBySystems.tegumentarySystem.state' => ['nullable', Rule::in(['Normal', 'Anormal']), Rule::requiredIf($consultationPurposes->review_by_systems)],
            'reviewBySystems.tegumentarySystem.observations' => 'nullable|max:1000|required_if:reviewBySystems.tegumentarySystem.state,Anormal',

            'reviewBySystems.nervousSystem.state' => ['nullable', Rule::in(['Normal', 'Anormal']), Rule::requiredIf($consultationPurposes->review_by_systems)],
            'reviewBySystems.nervousSystem.observations' => 'nullable|max:1000|required_if:reviewBySystems.nervousSystem.state,Anormal',

            'reviewBySystems.genitourinarySystem.state' => ['nullable', Rule::in(['Normal', 'Anormal']), Rule::requiredIf($consultationPurposes->review_by_systems)],
            'reviewBySystems.genitourinarySystem.observations' => 'nullable|max:1000|required_if:reviewBySystems.genitourinarySystem.state,Anormal',

            'reviewBySystems.lymphNodes.state' => ['nullable', Rule::in(['Normal', 'Anormal']), Rule::requiredIf($consultationPurposes->review_by_systems)],
            'reviewBySystems.lymphNodes.observations' => 'nullable|max:1000|required_if:reviewBySystems.lymphNodes.state,Anormal',

            'reviewBySystems.musculoskeletalSystem.state' => ['nullable', Rule::in(['Normal', 'Anormal']), Rule::requiredIf($consultationPurposes->review_by_systems)],
            'reviewBySystems.musculoskeletalSystem.observations' => 'nullable|max:1000|required_if:reviewBySystems.musculoskeletalSystem.state,Anormal',

            'reviewBySystems.reproductiveSystem.state' => ['nullable', Rule::in(['Normal', 'Anormal']), Rule::requiredIf($consultationPurposes->review_by_systems)],
            'reviewBySystems.reproductiveSystem.observations' => 'nullable|max:1000|required_if:reviewBySystems.reproductiveSystem.state,Anormal',

            'reviewBySystems.digestiveSystem.state' => ['nullable', Rule::in(['Normal', 'Anormal']), Rule::requiredIf($consultationPurposes->review_by_systems)],
            'reviewBySystems.digestiveSystem.observations' => 'nullable|max:1000|required_if:reviewBySystems.digestiveSystem.state,Anormal',

            'reviewBySystems.oralCavity.state' => ['nullable', Rule::in(['Normal', 'Anormal']), Rule::requiredIf($consultationPurposes->review_by_systems)],
            'reviewBySystems.oralCavity.observations' => 'nullable|max:1000|required_if:reviewBySystems.oralCavity.state,Anormal',



            'diagnoses' => 'array',
            'diagnoses.*.diagnosis_id' => 'required|exists:diagnoses,id',
            'diagnoses.*.type' => ['required', Rule::in(['Presuntivo', 'Definitivo'])],
            'vaccines' => 'array',
            'vaccines.*.product_id' => 'required|exists:products,id',
            'vaccines.*.application_date' => 'required|date',
            'vaccines.*.next_date' => 'required|date|after_or_equal:today',
            'dewormings' => 'array',
            'dewormings.*.product_id' => 'required|exists:products,id',
            'dewormings.*.application_date' => 'required|date',
            'dewormings.*.next_date' => 'required|date|after_or_equal:today',
            'products' => 'array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:0.0001|max:1000',
            'products.*.observations' => 'nullable|max:100',
            'services' => 'array',
            'services.*.service_id' => 'required|exists:services,id',
            'services.*.quantity' => 'required|integer|min:1|max:1000',
            'services.*.observations' => 'nullable|max:1500',
            'attachments' => 'array',
            'attachments.*.attachment_type_id' => 'required|exists:attachment_types,id',
            'attachments.*.file' => 'required|mimes:pdf|max:10000',
            'products_from_prescription' => 'array',
            'products_from_prescription.*.product_id' => 'required|exists:products,id',
            'products_from_prescription.*.quantity' => 'required|numeric|min:0.0001|max:1000',
            'products_from_prescription.*.posology' => 'required|max:500',
            'observations_from_prescription' => 'nullable|max:800',
            'indications' => ['max:800', Rule::requiredIf($consultationPurposes->indications)],
        ], [
            'pet_id.required' => 'Debe seleccionar una mascota',
            'pet_id.exists' => 'La mascota seleccionada no existe',

            'reviewBySystems.respiratorySystem.state.required' => 'Seleccione una opción',
            'reviewBySystems.respiratorySystem.observations.required_if' => 'Ingrese las observaciónes',
            'reviewBySystems.respiratorySystem.observations.max' => 'Las observaciónes no debe superar los 1000 caracteres',

            'reviewBySystems.tegumentarySystem.state.required' => 'Seleccione una opción',
            'reviewBySystems.tegumentarySystem.observations.required_if' => 'Ingrese las observaciónes',
            'reviewBySystems.tegumentarySystem.observations.max' => 'Las observaciónes no debe superar los 1000 caracteres',

            'reviewBySystems.nervousSystem.state.required' => 'Seleccione una opción',
            'reviewBySystems.nervousSystem.observations.required_if' => 'Ingrese las observaciónes',
            'reviewBySystems.nervousSystem.observations.max' => 'Las observaciónes no debe superar los 1000 caracteres',

            'reviewBySystems.genitourinarySystem.state.required' => 'Seleccione una opción',
            'reviewBySystems.genitourinarySystem.observations.required_if' => 'Ingrese las observaciónes',
            'reviewBySystems.genitourinarySystem.observations.max' => 'Las observaciónes no debe superar los 1000 caracteres',

            'reviewBySystems.lymphNodes.state.required' => 'Seleccione una opción',
            'reviewBySystems.lymphNodes.observations.required_if' => 'Ingrese las observaciónes',
            'reviewBySystems.lymphNodes.observations.max' => 'Las observaciónes no debe superar los 1000 caracteres',

            'reviewBySystems.musculoskeletalSystem.state.required' => 'Seleccione una opción',
            'reviewBySystems.musculoskeletalSystem.observations.required_if' => 'Ingrese las observaciónes',
            'reviewBySystems.musculoskeletalSystem.observations.max' => 'Las observaciónes no debe superar los 1000 caracteres',

            'reviewBySystems.reproductiveSystem.state.required' => 'Seleccione una opción',
            'reviewBySystems.reproductiveSystem.observations.required_if' => 'Ingrese las observaciónes',
            'reviewBySystems.reproductiveSystem.observations.max' => 'Las observaciónes no debe superar los 1000 caracteres',

            'reviewBySystems.digestiveSystem.state.required' => 'Seleccione una opción',
            'reviewBySystems.digestiveSystem.observations.required_if' => 'Ingrese las observaciónes',
            'reviewBySystems.digestiveSystem.observations.max' => 'Las observaciónes no debe superar los 1000 caracteres',

            'reviewBySystems.oralCavity.state.required' => 'Seleccione una opción',
            'reviewBySystems.oralCavity.observations.required_if' => 'Ingrese las observaciónes',
            'reviewBySystems.oralCavity.observations.max' => 'Las observaciónes no debe superar los 1000 caracteres',



            'records.*.description.max' => 'La descripción del antecedente no debe ser mayor a 100 caracteres',
            'vaccines.*.next_date.after_or_equal' => 'La fecha de la próxima vacuna debe ser posterior o igual a la fecha actual',
            'dewormings.*.next_date.after_or_equal' => 'La fecha de la próxima desparasitación debe ser posterior o igual a la fecha actual',
            'attachments.*.file.mimes' => 'Los archivos adjuntos deben ser de tipo PDF',
            'attachments.*.file.max' => 'Los archivos adjuntos no deben pesar más de 10.000 KB',
        ], [
            'consultation_purposes_id' => 'Finalidad de la consulta',
            'reason_consultation' => 'Motivo',
            'symptoms' => 'Síntomas',
            'anamnesis' => 'Anamnesis',
            'indications' => 'Indicaciones',
            'diagnoses' => 'Diagnósticos',
            'physiologicalConstants.weight' => 'Peso',
            'physiologicalConstants.temperature' => 'Temperatura',
            'physiologicalConstants.heart_rate' => 'Frecuencia cardíaca',
            'physiologicalConstants.breathing_frequency' => 'Frecuencia respiratoria',
            'observations_from_prescription' => 'Observaciónes',
        ]);


        $data['user_id'] = auth()->user()->id;

        \DB::transaction(function () use ($data, $request, $consultationPurposes, $appointment) {

            //Registro de historia clínica
            $clinicHistory = (new ClinicHistory)->fill($data);

            $clinicHistoryFromPet = ClinicHistory::where('pet_id', $request->pet_id)->latest()->first();

            if ($clinicHistoryFromPet) {
                $clinicHistory->number_folio = $clinicHistoryFromPet->number_folio + 1;
            } else {
                $clinicHistory->number_folio = 1;
            }

            $clinicHistory->save();

            //Registro de revisión por sistemas
            if ($consultationPurposes->review_by_systems) {
                $clinicHistory->clinicHistoryReviewBySystem()->create([
                    'respiratory_system_state' => $data['reviewBySystems']['respiratorySystem']['state'],
                    'respiratory_system_observations' => $data['reviewBySystems']['respiratorySystem']['observations'],
                    'tegumentary_system_state' => $data['reviewBySystems']['tegumentarySystem']['state'],
                    'tegumentary_system_observations' => $data['reviewBySystems']['tegumentarySystem']['observations'],
                    'nervous_system_state' => $data['reviewBySystems']['nervousSystem']['state'],
                    'nervous_system_observations' => $data['reviewBySystems']['nervousSystem']['observations'],
                    'genitourinary_system_state' => $data['reviewBySystems']['genitourinarySystem']['state'],
                    'genitourinary_system_observations' => $data['reviewBySystems']['genitourinarySystem']['observations'],
                    'lymph_nodes_state' => $data['reviewBySystems']['lymphNodes']['state'],
                    'lymph_nodes_observations' => $data['reviewBySystems']['lymphNodes']['observations'],
                    'musculoskeletal_system_state' => $data['reviewBySystems']['musculoskeletalSystem']['state'],
                    'musculoskeletal_system_observations' => $data['reviewBySystems']['musculoskeletalSystem']['observations'],
                    'reproductive_system_state' => $data['reviewBySystems']['reproductiveSystem']['state'],
                    'reproductive_system_observations' => $data['reviewBySystems']['reproductiveSystem']['observations'],
                    'digestive_system_state' => $data['reviewBySystems']['digestiveSystem']['state'],
                    'digestive_system_observations' => $data['reviewBySystems']['digestiveSystem']['observations'],
                    'oral_cavity_state' => $data['reviewBySystems']['oralCavity']['state'],
                    'oral_cavity_observations' => $data['reviewBySystems']['oralCavity']['observations'],
                ]);
            }

            //Registro de antecedentes
            if ($consultationPurposes->records && $request->records) {
                $clinicHistory->records()->createMany($data['records']);
            }


            //Registro de constantes fisiológicas
            if ($consultationPurposes->physiological_constants) {
                $clinicHistory->physiologicalConstants()->create($data['physiologicalConstants']);
            }

            //Registro de diagnósticos
            if ($consultationPurposes->diagnoses) {
                $diagnoses = collect($request->diagnoses)->map(function ($item) {
                    return ['diagnosis_id' => $item['diagnosis_id'], 'type' => $item['type']];
                })->toArray();

                $clinicHistory->diagnoses()->createMany($diagnoses);
            }

            //Registro de vacunas
            if ($consultationPurposes->vaccines) {
                $vaccines = collect($request->vaccines)->map(function ($item) use($clinicHistory) {
                    return [
                            'pet_id' => $clinicHistory->pet_id,
                            'product_id' => $item['product_id'],
                            'registration_date' => $clinicHistory->created_at,
                            'application_date' => $item['application_date'],
                            'next_date' => $item['next_date'],
                            'user_id' => auth()->user()->id,
                        ];
                })->toArray();

                /*Registrando las vacunas en "pendientes por facturar"*/
                foreach ($vaccines as $key => $vaccine) {
                    $productDB = Product::find($vaccine['product_id']);

                    $billingPendingBilling = BillingPendingBilling::create([
                    'datetime' => now(),
                    'code' => $productDB->code,
                    'description' => $productDB->name,
                    'product_id' => $productDB->id,
                    'service_id' => null,
                    'price' => $productDB->price,
                    'quantity' => 1,
                    'owner_id' => $clinicHistory->pet->owner_id,
                    'pet_id' => $clinicHistory->pet_id,
                    ]);

                    $vaccines[$key]['billing_pending_billing_id'] = $billingPendingBilling->id;
                }

                //Registrando las vacunas en la tabla de vacunas realizadas
                $clinicHistory->vaccines()->createMany($vaccines);
            }

            //Registro de desparacitasiónes
            if ($consultationPurposes->dewormings) {
                $dewormings = collect($request->dewormings)->map(function ($item) use($clinicHistory) {
                    return [
                            'pet_id' => $clinicHistory->pet_id,
                            'product_id' => $item['product_id'],
                            'registration_date' => $clinicHistory->created_at,
                            'application_date' => $item['application_date'],
                            'next_date' => $item['next_date'],
                            'user_id' => auth()->user()->id,
                        ];
                })->toArray();

                /*Agregando los desparasitantes a "pendientes por facturar"*/
                foreach ($dewormings as $key => $deworming) {
                    $productDB = Product::find($deworming['product_id']);

                    $billingPendingBilling = BillingPendingBilling::create([
                    'datetime' => now(),
                    'code' => $productDB->code,
                    'description' => $productDB->name,
                    'product_id' => $productDB->id,
                    'service_id' => null,
                    'price' => $productDB->price,
                    'quantity' => 1,
                    'owner_id' => $clinicHistory->pet->owner_id,
                    'pet_id' => $clinicHistory->pet_id,
                    ]);

                    $dewormings[$key]['billing_pending_billing_id'] = $billingPendingBilling->id;
                }

                $clinicHistory->dewormings()->createMany($dewormings);
            }

            //Registro de productos y servicios aplicados
            if ($consultationPurposes->products_and_services_applied) {
                $productsApplied = collect($request->products)->map(function ($item) {
                    return [
                            'product_id' => $item['product_id'],
                            'quantity' => $item['quantity'],
                            'observations' => $item['observations']
                        ];
                })->toArray();

                $clinicHistory->products()->createMany($productsApplied);

                /*Agregando los productos a "pendientes por facturar"*/
                foreach ($productsApplied as $productApplied) {
                    $productDB = Product::find($productApplied['product_id']);

                    BillingPendingBilling::create([
                    'datetime' => now(),
                    'code' => $productDB->code,
                    'description' => $productDB->name,
                    'product_id' => $productDB->id,
                    'service_id' => null,
                    'price' => $productDB->price,
                    'quantity' => $productApplied['quantity'],
                    'owner_id' => $clinicHistory->pet->owner_id,
                    'pet_id' => $clinicHistory->pet_id,
                    ]);

                }

                $servicesApplied = collect($request->services)->map(function ($item) {
                    return [
                            'service_id' => $item['service_id'],
                            'observations' => $item['observations'],
                            'quantity' => $item['quantity']
                        ];
                })->toArray();


                $clinicHistory->services()->createMany($servicesApplied);

                /*Agregando los servicios a pendientes por facturar*/

                foreach ($servicesApplied as $serviceApplied) {
                    $serviceDB = Service::find($serviceApplied['service_id']);

                    BillingPendingBilling::create([
                    'datetime' => now(),
                    'code' => $serviceDB->code,
                    'description' => $serviceDB->name,
                    'product_id' => null,
                    'service_id' => $serviceDB->id,
                    'price' => $serviceDB->price,
                    'quantity' => $serviceApplied['quantity'],
                    'owner_id' => $clinicHistory->pet->owner_id,
                    'pet_id' => $clinicHistory->pet_id,
                    ]);

                }
            }

            //Archivos adjuntos
            if ($consultationPurposes->attachments) {
                if ($request->attachments) {
                    foreach ($request->attachments as $attachment) {
                        $path = Storage::putFile('clinic-histories/attachments', $attachment['file']);
                        $clinicHistoryAttachment = (new ClinicHistoryAttachment)->fill([
                            'clinic_history_id' => $clinicHistory->id,
                                                                                    'attachment_type_id' => $attachment['attachment_type_id']
                                                                                    ]);
                                                                                    $clinicHistoryAttachment->file = $path;
                        $clinicHistoryAttachment->save();
                    }
                }
            }

            //Registro de receta veterinaria
            if ($consultationPurposes->prescription && ($request->observations_from_prescription || collect($request->products_from_prescription)->count())) {

                $clinicHistoryPrescription = $clinicHistory->clinicHistoryPrescription()->create(['observations' => $request->observations_from_prescription]);

                $productsFromPrescription = collect($request->products_from_prescription)->map(function ($item) {
                    return [
                            'product_id' => $item['product_id'],
                            'quantity' => $item['quantity'],
                            'posology' => $item['posology'],

                        ];
                })->toArray();

                $clinicHistoryPrescription->clinicHistoryPrescriptionDetails()->createMany($productsFromPrescription);
            }

            //Cumpliendo la cita si la escogió
            if ($request->appointment_id) {
                $appointment->update(['state' => 'CUMPLIDA', 'user_id_fulfill' => auth()->user()->id, 'clinic_history_id' => $clinicHistory->id]);
            }

        });

        return 'bien';
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ClinicHistory  $clinicHistory
     * @return \Illuminate\Http\Response
     */
    public function deleteProductApplication(ClinicHistoryProduct $clinicHistoryProduct)
    {
        if ($parameters = Parameter::first()) {

            $diffInHoursFromFolioAndNow = $clinicHistoryProduct->clinicHistory->created_at->diffInHours(\Carbon\Carbon::now());

            if ($diffInHoursFromFolioAndNow >= $parameters->hours_products_and_services_applied) {
                $text = $parameters->hours_products_and_services_applied == 1 ? 'hora' : 'horas';

                return response()
                    ->json(['message' => 'El tiempo límite para actualizar es de '
                                            . $parameters->hours_products_and_services_applied
                                            . ' ' . $text ],422);
            }

        }
        //Verifcar que el usuario que intenta eliminar sea el mismo que creó el folio
        if (auth()->user()->id == $clinicHistoryProduct->clinicHistory->user_id) {
            $clinicHistoryProduct->delete();
        } else {
            return response()->json(['message' => 'Solo el usuario que registró la aplicación puede eliminarla'],422);
        }
    }

    public function updateProductApplication(ClinicHistoryProduct $clinicHistoryProduct, Request $request)
    {
        if ($parameters = Parameter::first()) {

            $diffInHoursFromFolioAndNow = $clinicHistoryProduct->clinicHistory->created_at->diffInHours(\Carbon\Carbon::now());

            if ($diffInHoursFromFolioAndNow >= $parameters->hours_products_and_services_applied) {
                $text = $parameters->hours_products_and_services_applied == 1 ? 'hora' : 'horas';

                return response()
                    ->json(['message' => 'El tiempo límite para actualizar es de '
                                            . $parameters->hours_products_and_services_applied
                                            . ' ' . $text ],422);
            }

        }
        //Verifcar que el usuario que intenta actualizar sea el mismo que creó el folio
        $request->validate([
            'quantity' => 'required|numeric|min:1'
        ]);

        //Verifcar que sea del mismo autenticado
        if (auth()->user()->id == $clinicHistoryProduct->clinicHistory->user_id) {
            $clinicHistoryProduct->quantity = $request->quantity;
            $clinicHistoryProduct->save();
        } else {
            return response()->json(['message' => 'Solo el usuario que registró la aplicación puede actualizar'],422);
        }
    }

    public function deleteServiceApplication(ClinicHistoryService $clinicHistoryService)
    {
        if ($parameters = Parameter::first()) {

            $diffInHoursFromFolioAndNow = $clinicHistoryService->clinicHistory->created_at->diffInHours(\Carbon\Carbon::now());

            if ($diffInHoursFromFolioAndNow >= $parameters->hours_products_and_services_applied) {
                $text = $parameters->hours_products_and_services_applied == 1 ? 'hora' : 'horas';

                return response()
                    ->json(['message' => 'El tiempo límite para actualizar es de '
                                            . $parameters->hours_products_and_services_applied
                                            . ' ' . $text ],422);
            }

        }
        //Verifcar que el usuario que intenta eliminar sea el mismo que creó el folio
        if (auth()->user()->id == $clinicHistoryService->clinicHistory->user_id) {
            $clinicHistoryService->delete();
        } else {
            return response()->json(['message' => 'Solo el usuario que registró la aplicación puede eliminarla'],422);
        }
    }

    public function updateServiceApplication(ClinicHistoryService $clinicHistoryService, Request $request)
    {
        if ($parameters = Parameter::first()) {

            $diffInHoursFromFolioAndNow = $clinicHistoryService->clinicHistory->created_at->diffInHours(\Carbon\Carbon::now());

            if ($diffInHoursFromFolioAndNow >= $parameters->hours_products_and_services_applied) {
                $text = $parameters->hours_products_and_services_applied == 1 ? 'hora' : 'horas';

                return response()
                    ->json(['message' => 'El tiempo límite para actualizar es de '
                                            . $parameters->hours_products_and_services_applied
                                            . ' ' . $text ],422);
            }

        }

        $request->validate([
            'quantity' => 'required|numeric|min:1'
        ]);

        //Verifcar que el usuario que intenta actualizar sea el mismo que creó el folio
        if (auth()->user()->id == $clinicHistoryService->clinicHistory->user_id) {
            $clinicHistoryService->quantity = $request->quantity;
            $clinicHistoryService->save();
        } else {
            return response()->json(['message' => 'Solo el usuario que registró la aplicación puede actualizar'],422);
        }
    }

    public function sendClinicHistoryForEmail (Request $request, Pet $pet) {

        if ($company = Company::first()) {

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

            //Buscando los folios y guardandolos como temporal
            $request->validate([
                'folios' => 'required|array'
            ]);

            $folios = [];

            if ($request->folios[0] == 'latest') {
                $folios[0] = ClinicHistory::where('pet_id', $pet)->latest()->first()->number_folio;
            } else {
                $folios = $request->folios;
            }

            $clinicHistoriesFromPet = Pet::where('id', $pet->id)
                                                            ->with('species')
                                                            ->with('owner')
                                                            ->with('clinicHistories.consultationPurposes')
                                                            ->with('clinicHistories.records')
                                                            ->with('clinicHistories.physiologicalConstants')
                                                            ->with('clinicHistories.diagnoses')
                                                            ->with('clinicHistories.vaccines')
                                                            ->with('clinicHistories.dewormings')
                                                            ->with('clinicHistories.dewormings.product')
                                                            ->with('clinicHistories.products')
                                                            ->with('clinicHistories.services')
                                                            ->with(['clinicHistories' => function ($query) use ($folios) {
                                                                $query->whereIn('number_folio', $folios);
                                                            }])
                                                            ->first();

            if (!$clinicHistoriesFromPet->clinicHistories()->exists()) {
                return response()->json(['message' => 'No hay folios de historia clínica'], 422);
            }

            $pdf = \PDF::loadView('reports.clinic-history', compact('clinicHistoriesFromPet', 'company'));
            Storage::put('temp.pdf', $pdf->output());
            //Fin buscando folios y guardandolos como temporal

            Mail::to($pet->owner->email)->send(new SendClinicHistory($company, $pet->name, $pet->owner, 'historia clínica'));

            return "Enviado";

        } else {
            return response()->json(['message' => 'No ha digitado los datos de la empresa'], 422);
        }
    }
}
