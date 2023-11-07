<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BillBillingAuthorization;
use App\Models\BillBillingAuthorizationDetail;

class BillBillingAuthorizationController extends Controller
{
    public function index () {
    
        $billBillingAuthorization = BillBillingAuthorization::orderBy('code', 'asc')->paginate(10);
        return $billBillingAuthorization;
    }

    public function edit ($billBillingAuthorization) {
    
        return BillBillingAuthorization::with('billBillingAuthorizationDetails')->whereId($billBillingAuthorization)->firstOrFail();

    }

    public function store (Request $request) {
        $data = $request->validate([
            'code' => 'required|unique:bill_billing_authorizations,code',
            'name' => 'required|max:100',
            'handles_prefix' => 'required|boolean',
        ], [], [
            'code' => 'Código',
            'name' => 'Nombre',
            'handles_prefix' => 'Maneja prefijo',
        ]);

        BillBillingAuthorization::create($data);
    }

    public function addDetail (Request $request, BillBillingAuthorization $billingAuthorization) {

        $data = $request->validate([
            'resolution_number' => 'required|string|max:100',
            'resolution_date' => 'required|date',
            'initial_bill' => 'required|numeric|min:1',
            'final_bill' => 'required|numeric|min:1|gte:initial_bill',
            'initial_date' => 'required|date',
            'final_date' => 'required|date|after_or_equal:initial_date',
            'active' => 'required|boolean',
        ], [], [
            'resolution_number' => 'Número de resolución',
            'resolution_date' => 'Fecha de resolución',
            'initial_bill' => 'Factura inicial',
            'final_bill' => 'Factura final',
            'initial_date' => 'Fecha inicial',
            'final_date' => 'Fecha final',
            'active' => 'Activo',
        ]);

        \DB::transaction(function () use ($billingAuthorization, $data) {

            if ($data['active'] == true) {
                BillBillingAuthorizationDetail::get()->each->update(['active' => false]);
            }

            $billingAuthorization->billBillingAuthorizationDetails()->create($data);
        });

    }
    
    public function updateDetail (Request $request, BillBillingAuthorizationDetail $billBillingAuthorizationDetail) {

        $data = $request->validate([
            'resolution_number' => 'required|string|max:100',
            'resolution_date' => 'required|date',
            'initial_bill' => 'required|numeric|min:1',
            'final_bill' => 'required|numeric|min:1|gte:initial_bill',
            'initial_date' => 'required|date',
            'final_date' => 'required|date|after_or_equal:initial_date',
            'active' => 'required|boolean',
        ], [], [
            'resolution_number' => 'Número de resolución',
            'resolution_date' => 'Fecha de resolución',
            'initial_bill' => 'Factura inicial',
            'final_bill' => 'Factura final',
            'initial_date' => 'Fecha inicial',
            'final_date' => 'Fecha final',
            'active' => 'Activo',
        ]);

        \DB::transaction(function () use ($billBillingAuthorizationDetail, $data) {

            if ($data['active'] == true) {
                BillBillingAuthorizationDetail::get()->each->update(['active' => false]);
            }

            $billBillingAuthorizationDetail->update($data);
        });

    }

    public function deleteDetail (BillBillingAuthorizationDetail $billBillingAuthorizationDetail) {

        $billBillingAuthorizationDetail->delete();

    }
}
