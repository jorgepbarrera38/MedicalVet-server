<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\Owner;
use App\Models\Species;
use App\Models\Race;
use App\Models\ClinicHistoryAttachment;
use App\Models\ClinicHistory;
use App\Rules\PetIsUniqueFromOwner;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

class PetController extends Controller
{

    public function __construct () {
        $this->middleware('has.permission:pets.index')->only('index');
        $this->middleware('has.permission:pets.store')->only('store');
        $this->middleware('has.permission:pets.update')->only('update');
        $this->middleware('has.permission:pets.destroy')->only('destroy');
    }

    public function index()
    {

        return Pet::whereHas('owner', function (Builder $query) {
                        $query->where('identification', 'like', '%' . request()->ownerIdOrName . '%')
                                ->orWhere('names', 'like', '%' . request()->ownerIdOrName . '%')
                                    ->orWhere('surnames', 'like', '%' . request()->ownerIdOrName . '%');
                    })
                    ->where('name', 'like', '%' . request()->petName . '%')
                    ->withCount('clinicHistories')
                    ->with(['owner:id,identification,names,surnames', 'species:id,name', 'race:id,name'])
                    ->orderBy('name', 'asc')
                    ->paginate(7);
    }

    public function getQuantityPetsCreated () {
        return Pet::count();
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
            'owner_id' => 'required|exists:owners,id',
            'photo' => 'nullable|image',
            'name' => ['required', 'max:40', new PetIsUniqueFromOwner($request->owner_id)],
            'date_born' => 'required|date|before_or_equal:today',
            'sex' => [Rule::in(['MACHO', 'HEMBRA'])],
            'species_id' => 'required|exists:species,id',
            'race_id' => 'required|exists:races,id',
            'color' => 'required|max:100',
            'size' => [Rule::in(['PEQUEÑO', 'MEDIANO', 'GRANDE'])],
            'observations' => 'nullable|max:200',
        ], [
            'date_born.before_or_equal' => 'La fecha de nacimiento debe ser igual o inferior a la fecha actual'
        ], [
            'name' => 'Nombre',
            'species_id' => 'Especie',
            'race_id' => 'Raza',
            'owner_id' => 'Propietario',
            'date_born' => 'Fecha de nacimiento',
            'sex' => 'Sexo',
            'color' => 'Color',
            'size' => 'Tamaño',
            'observations' => 'Observaciónes',
        ]);

        $data['user_id'] = auth()->user()->id;

        $pet = (new Pet)->fill($data);

        if ($request->hasFile('photo')) {
            $pet->photo = Storage::putFile('images/photos-pet', $request->file('photo'));
        }

        $pet->save();
    }

    public function getDataToCreatePet () {
        $owners = Owner::orderBy('names')->get(['id', 'identification', 'names', 'surnames']);
        $species = Species::orderBy('name')->get(['id', 'name']);
        $races = Race::orderBy('name')->get(['id', 'name']);

        return [
            'owners' => $owners,
            'species' => $species,
            'races' => $races
        ];
    }

    public function show($pet) {
        $pet = Pet::with(['owner', 'species', 'race', 'appointments.consultingroom', 'appointments.activity'])
                    ->with(['appointments' => function ($query) {
                        $query->orderBy('date_time_start', 'desc');
                    }])
                    ->with('prescriptions.clinicHistoryPrescriptionDetails.product')
                    ->with(['prescriptions' => function ($query) {
                        $query->orderBy('created_at', 'desc');
                    }])
                    ->with('vaccines.product')
                    ->with(['vaccines' => function ($query) {
                        $query->orderBy('application_date', 'desc');
                    }])
                    ->with('dewormings.product')
                    ->with(['dewormings' => function ($query) {
                        $query->orderBy('application_date', 'desc');
                    }])
                    ->with('productsApplied.product')
                    ->with(['productsApplied' => function ($query) {
                        $query->orderBy('created_at', 'desc');
                    }])
                    ->with('servicesApplied.service')
                    ->with(['servicesApplied' => function ($query) {
                        $query->orderBy('application_date', 'desc');
                    }])
                    ->with(['records' => function ($query) {
                        $query->orderBy('created_at', 'desc');
                    }])
                    ->where('id', $pet)->firstOrFail();

        $clinicHistoryAttachments = ClinicHistoryAttachment::whereHas('clinicHistory', function (Builder $query) use ($pet) {
                                            $query->where('pet_id', $pet->id);
                                        })->with('attachmentType')->get();
        $clinicHistories = ClinicHistory::where('pet_id', $pet->id)
                                            ->with('consultationPurposes:id,name')
                                            ->orderBy('number_folio', 'desc')
                                            ->get(['id', 'number_folio', 'consultation_purposes_id', 'created_at']);
                    
        $pet->clinicHistoryAttachments = $clinicHistoryAttachments;
        $pet->clinicHistories = $clinicHistories;
                    
        return $pet;                    
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pet  $pet
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pet $pet)
    {
        $data = $request->validate([
            'owner_id' => 'required|exists:owners,id',
            'photo' => 'nullable|image',
            'name' => ['required', 'max:40', new PetIsUniqueFromOwner($request->owner_id, $pet->id)],
            'date_born' => 'required|date|before_or_equal:today',
            'sex' => [Rule::in(['MACHO', 'HEMBRA'])],
            'species_id' => 'required|exists:species,id',
            'race_id' => 'required|exists:races,id',
            'color' => 'required|max:100',
            'size' => [Rule::in(['PEQUEÑO', 'MEDIANO', 'GRANDE'])],
            'observations' => 'nullable|max:200',
        ], [
            'date_born.before_or_equal' => 'La fecha de nacimiento debe ser igual o inferior a la fecha actual'
        ], [
            'name' => 'Nombre',
            'species_id' => 'Especie',
            'race_id' => 'Raza',
            'owner_id' => 'Propietario',
            'date_born' => 'Fecha de nacimiento',
            'sex' => 'Sexo',
            'color' => 'Color',
            'size' => 'Tamaño',
            'observations' => 'Observaciónes',
        ]);

        if ($request->hasFile('photo')) {
            //Verificando si ya tiene foto para eliminarla
            if ($pet->photo && Storage::exists($pet->photo)) {
                Storage::delete($pet->photo);
            }
            $data['photo'] = Storage::putFile('images/photos-pet', $request->file('photo'));
        }

        $pet->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pet  $pet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pet $pet)
    {
        if ($pet->clinicHistories()->exists()) {
            return response()->json(['message' => 'La mascota no se puede eliminar ya que tiene historias clínicas relacionadas'], 422);
        }

        if ($pet->appointments()->exists()) {
            return response()->json(['message' => 'La mascota no se puede eliminar ya que tiene citas relacionadas'], 422);
        }

        //Verificando si ya tiene foto para eliminarla
        if ($pet->photo && Storage::exists($pet->photo)) {
            Storage::delete($pet->photo);
        }

        $pet->delete();
    }
}
