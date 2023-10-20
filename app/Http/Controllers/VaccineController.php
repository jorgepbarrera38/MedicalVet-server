<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Vaccine;
use App\Models\Pet;
use App\Models\BillingPendingBilling;
use Illuminate\Http\Request;

class VaccineController extends Controller
{
    public function __construct () {
        $this->middleware('has.permission:vaccines.store')->only('store');
        $this->middleware('has.permission:vaccines.update')->only('update');
        $this->middleware('has.permission:vaccines.destroy')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vaccines = Product::where('vaccine', true)->orderBy('name')->get(['id', 'name']);
        return $vaccines;
    }

    public function getQuantityVaccinesCreated (Pet $pet) {
        return Vaccine::where('pet_id', $pet->id)->count();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'pet_id' => 'required|exists:pets,id',
            'product_id' => 'required|exists:products,id',
            'application_date' => 'required|date',
            'next_date' => 'required|date|after_or_equal:today',
        ], [
            'next_date.after_or_equal' => 'El campo Próxima vacuna debe ser igual o superior a la fecha actual'
        ], [
            'pet_id' => 'Mascota',
            'product_id' => 'Vacuna',
            'application_date' => 'Fecha de aplicación',
            'next_date' => 'Próxima vacuna'
        ]);

        $data['registration_date'] = now();

        \DB::transaction(function () use ($data) {

            $productDB = Product::find($data['product_id']);
            $petDB = Pet::find($data['pet_id']);

            BillingPendingBilling::create([
                'datetime' => now(),
                'code' => $productDB->code,
                'description' => $productDB->name,
                'product_id' => $productDB->id,
                'service_id' => null,
                'price' => $productDB->price,
                'quantity' => 1,
                'owner_id' => $petDB->owner_id,
                'pet_id' => $petDB->id,
            ]);

            auth()->user()->vaccines()->create($data);
        });


        return response()->json(['message' => 'Vaccine registered'], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Vaccine  $vaccine
     * @return \Illuminate\Http\Response
     */
    public function show(Vaccine $vaccine)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Vaccine  $vaccine
     * @return \Illuminate\Http\Response
     */
    public function edit(Vaccine $vaccine)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Vaccine  $vaccine
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vaccine $vaccine)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'next_date' => 'required|date|after_or_equal:today',
        ], [
            'next_date.after_or_equal' => 'El campo Próxima vacuna debe ser igual o superior a la fecha actual'
        ], [
            'product_id' => 'Vacuna',
            'next_date' => 'Próxima vacuna'
        ]);

        //Para editar vacuna registrada desde HC
        if ($vaccine->clinic_history_id) {
            if (auth()->user()->id != $vaccine->clinicHistory->user_id) {
                return response()->json(['message' => 'Forbidden action'], 403);
            }
        } else {
            //Para editar vacuna registrada desde Vacunación y desparasitación
            if (auth()->user()->id != $vaccine->user_id) {
                return response()->json(['message' => 'Forbidden action'], 403);
            }
        }


        $vaccine->update($data);
        return response()->json(['message' => 'Vaccine updated'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Vaccine  $vaccine
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vaccine $vaccine)
    {
        //Para eliminar vacuna registrada desde HC
        if ($vaccine->clinic_history_id) {
            if (auth()->user()->id != $vaccine->clinicHistory->user_id) {
                return response()->json(['message' => 'Forbidden action'], 403);
            }
            $vaccine->delete();
            return response()->json(['message' => 'Vaccine deleted'], 200);
        }

        //Para eliminar vacuna registrada desde Vacunación y desparasitación
        if (auth()->user()->id != $vaccine->user_id) {
            return response()->json(['message' => 'Forbidden action'], 403);
        }
        $vaccine->delete();
        return response()->json(['message' => 'Vaccine deleted'], 200);
    }
}
