<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Pet;
use App\Models\ClinicHistoryPrescription;

class PrescriptionController extends Controller
{
    public function getProducts () {
        return Product::where('vaccine', false)->where('deworming', false)->orderBy('name')->get(['id', 'name']);
    }

    public function store (Request $request, Pet $pet) {
        
        $data = $request->validate([
            'products' => 'array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1|max:1000',
            'products.*.posology' => 'required|max:500',
            'observations' => 'required|max:800',
        ], [], [
            'observations' => 'Observaciones',
        ]);

        if (!count($data['products'])) {
            return response()->json(['errors' => ['products' => ['Debe agregar productos a la prescripción']]], 422);
        }

        \DB::transaction(function () use ($data, $pet) {

            $clinicHistoryPrescription = ClinicHistoryPrescription::create(['pet_id' => $pet->id, 'observations' => $data['observations'], 'user_id' => auth()->user()->id,]);

            $dataProducts = [];

            foreach ($data['products'] as $product) {
                $dataProducts[] = ['product_id' => $product['product_id'], 'quantity' => $product['quantity'], 'posology' => $product['posology']];
            }

            $clinicHistoryPrescription->clinicHistoryPrescriptionDetails()->createMany($dataProducts);

        });
    }
}
