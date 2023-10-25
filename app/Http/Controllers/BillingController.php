<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BillingPendingBilling;
use App\Models\BillingBill;
use App\Models\BillingBillDetail;
use App\Models\Pet;
use Illuminate\Support\Str;


class BillingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPendingBilling(Request $request, $petSelected)
    {
        $productsAndServices = BillingPendingBilling::where('pet_id', $petSelected)->where('billed', false)->orderBy('id', 'DESC')->get();

        return [
            'productsAndServices' => $productsAndServices,
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function bill(Request $request, $petSelected)
    {
        $request->validate([
            'productsAndServicesChecked' => 'required|array',
            'productsAndServicesChecked.*.id' => 'required|exists:billing_pending_billings,id',
            'moneyGiven' => 'required|numeric|min:0'
        ]);        

        \DB::transaction(function () use ($request, $petSelected) {

           $pet = Pet::findOrFail($petSelected);

           $billingBill = BillingBill::create([
                'date' => now(),
                'number_bill' => Str::random(40),
                'total' => 0,
                'money_given' => 0,
                'owner_id' => $pet->owner_id,
                'pet_id' => $pet->id
            ]);

            $totalBill = 0;
            
            foreach ($request->productsAndServicesChecked as $productAndServiceChecked) {
                $billingPendingBilling = BillingPendingBilling::find($productAndServiceChecked["id"]);

                //Validando que el pendiente por facturar si pertenezca a la mascota
                if ($billingPendingBilling->pet_id != $petSelected) {
                    abort(422, 'El pendiente (Id: ' . $billingPendingBilling->id . ' - Descripción: ' . $billingPendingBilling->description . ') no pertenece a la mascota seleccionada');
                }

                if ($billingPendingBilling->billed) {
                    abort(422, 'El pendiente (Id: ' . $billingPendingBilling->id . ' - Descripción: ' . $billingPendingBilling->description . ') ya se encuentra facturado');
                }

                BillingBillDetail::create([
                    'billing_bill_id' => $billingBill->id,
                    'code' => $billingPendingBilling->code,
                    'description' => $billingPendingBilling->description,
                    'product_id' => $billingPendingBilling->product_id ? $billingPendingBilling->product_id : null,
                    'service_id' => $billingPendingBilling->service_id ? $billingPendingBilling->service_id : null,
                    'price' => $billingPendingBilling->price,
                    'quantity' => $billingPendingBilling->quantity,
                    'billing_pending_billing_id' => $billingPendingBilling->id,
                ]);

                $billingPendingBilling->update(['billed' => true]);

                /*Calculando total de la factura*/
                $totalBill += $billingPendingBilling->price * $billingPendingBilling->quantity;

            }

            if ($totalBill > $request->moneyGiven) {
                abort(422, 'El valor entregado ($' . $request->moneyGiven . ') es inferior al total de la factura ($' . $totalBill . ')');
            }

            $billingBill->update(['total' => $totalBill, 'money_given' => $request->moneyGiven]);
    

        });

        return 'ok';

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
