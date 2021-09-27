<?php

namespace App\Http\Controllers;

use App\Models\Deworming;
use App\Models\Product;
use App\Models\Unity;
use Illuminate\Http\Request;

class DewormingController extends Controller
{
    public function __construct () {
        $this->middleware('has.permission:dewormings.store')->only('store');
        $this->middleware('has.permission:dewormings.update')->only('update');
        $this->middleware('has.permission:dewormings.destroy')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dewormings = Product::where('deworming', true)->orderBy('name')->get(['id', 'name']);
        $unities = Unity::orderBy('description')->get(['id', 'description']);
        return [
            'dewormings' => $dewormings,
            'unities' => $unities
        ];
    }

    public function getQuantityDewormingsCreated () {
        return Deworming::count();
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
            'dose' => 'required|numeric|min:0.0001|max:1000',
            'unity_id' => 'required|exists:unities,id',
            'next_date' => 'required|date|after_or_equal:today',
        ], [
            'next_date.after_or_equal' => 'El campo Próxima desparasitación debe ser igual o superior a la fecha actual'
        ], [
            'pet_id' => 'Mascota',
            'product_id' => 'Desparasitante',
            'dose' => 'Dosis',
            'unity_id' => 'Unidad',
            'next_date' => 'Próxima desparasitación'
        ]);

        auth()->user()->dewormings()->create($data);

        return response()->json(['message' => 'Deworming registered'], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Deworming  $deworming
     * @return \Illuminate\Http\Response
     */
    public function show(Deworming $deworming)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Deworming  $deworming
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Deworming $deworming)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'dose' => 'required|numeric|min:0.0001|max:1000',
            'unity_id' => 'required|exists:unities,id',
            'next_date' => 'required|date|after_or_equal:today',
        ], [
            'next_date.after_or_equal' => 'El campo Próxima vacuna debe ser igual o superior a la fecha actual'
        ], [
            'product_id' => 'Vacuna',
            'next_date' => 'Próxima vacuna',
            'dose' => 'Dosis',
            'unity_id' => 'Unidad'
        ]);

        //Para editar vacuna registrada desde HC
        if ($deworming->clinic_history_id) {
            if (auth()->user()->id != $deworming->clinicHistory->user_id) {
                return response()->json(['message' => 'Forbidden action'], 403);
            }
        } else {
            //Para editar vacuna registrada desde Vacunación y desparasitación
            if (auth()->user()->id != $deworming->user_id) {
                return response()->json(['message' => 'Forbidden action'], 403);
            }
        }

        $deworming->update($data);
        return response()->json(['message' => 'Deworming updated'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Deworming  $deworming
     * @return \Illuminate\Http\Response
     */
    public function destroy(Deworming $deworming)
    {
        //Para eliminar vacuna registrada desde HC
        if ($deworming->clinic_history_id) {
            if (auth()->user()->id != $deworming->clinicHistory->user_id) {
                return response()->json(['message' => 'Forbidden action'], 403);
            }
            $deworming->delete();
            return response()->json(['message' => 'Deworming deleted'], 200);
        }

        //Para eliminar vacuna registrada desde Vacunación y desparasitación
        if (auth()->user()->id != $deworming->user_id) {
            return response()->json(['message' => 'Forbidden action'], 403);
        }
        $deworming->delete();
        return response()->json(['message' => 'Deworming deleted'], 200);
    }
}
