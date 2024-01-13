<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BillingPendingBilling;
use App\Models\BillingBill;
use App\Models\BillingBillDetail;
use App\Models\BillBillingAuthorizationDetail;
use App\Models\Pet;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\PDF;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Exports\BillsReportExport;

class BillingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPendingBilling(Request $request, $petSelected)
    {
        $productsAndServices = BillingPendingBilling::where('pet_id', $petSelected)->where('billed', false)->orderBy('datetime', 'DESC')->get();

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

            'productsAndServicesChecked.*.id' => 'required_without_all:productsAndServicesChecked.*.product_id,productsAndServicesChecked.*.service_id|nullable|exists:billing_pending_billings,id',
            'productsAndServicesChecked.*.product_id' => 'required_without_all:productsAndServicesChecked.*.id,productsAndServicesChecked.*.service_id|nullable|exists:products,id',
            'productsAndServicesChecked.*.service_id' => 'required_without_all:productsAndServicesChecked.*.id,productsAndServicesChecked.*.product_id|nullable|exists:services,id',
            'productsAndServicesChecked.*.quantity' => 'required_without_all:productsAndServicesChecked.*.id|nullable|numeric|min:1',

            'moneyGiven' => 'required|numeric|min:0'
        ]);

        //\DB::transaction(function () use ($request, $petSelected) {
        try {
        
        \DB::beginTransaction();
            /*Creando encabezado de la factura*/
           $billBillingAuthorizationDetail = BillBillingAuthorizationDetail::where('active', true)
                                                            ->where('initial_date', '<=', now())
                                                            ->where('final_date', '>=', now())
                                                            ->first();

           if (!$billBillingAuthorizationDetail) {

               abort(422, 'No tiene resolución de facturación activa');
           }

           /*Elaborando número de factura*/
           $numberBill;
            $lastBill = BillingBill::whereHas('billBillingAuthorizationDetail.billBillingAuthorization', function (Builder $query) use ($billBillingAuthorizationDetail) {
                                        $query->where('id', $billBillingAuthorizationDetail->billBillingAuthorization->id);
                                    })->orderBy('id', 'desc')->first();
            
            if ($lastBill) {
                $numberBill = $lastBill->number_bill + 1;                
            } else {
                $numberBill = 1;
            }
            //abort(422, $numberBill);

           /*Elaborando número de factura*/
           $pet = Pet::findOrFail($petSelected);


           $billingBill = BillingBill::create([
                'date' => now(),
                'prefix' => $billBillingAuthorizationDetail->billBillingAuthorization->handles_prefix ? $billBillingAuthorizationDetail->billBillingAuthorization->code : '',
                'number_bill' => $numberBill,
                'prefix_and_number_bill' => ($billBillingAuthorizationDetail->billBillingAuthorization->handles_prefix ? $billBillingAuthorizationDetail->billBillingAuthorization->code : '') . $numberBill,
                'total' => 0,
                'money_given' => 0,
                'owner_id' => $pet->owner_id,
                'pet_id' => $pet->id,
                'bill_billing_authorization_detail_id' => $billBillingAuthorizationDetail->id,
            ]);

            $totalBill = 0;
            
            foreach ($request->productsAndServicesChecked as $productAndServiceChecked) {

                /* 1. El producto-servicio es de un pendiente que ya existía*/
                if ($productAndServiceChecked["id"]) {
                    $billingPendingBilling = BillingPendingBilling::find($productAndServiceChecked["id"]);
    
                    //Validando que el pendiente por facturar si pertenezca a la mascota
                    if ($billingPendingBilling->pet_id != $petSelected) {
                        abort(422, 'El pendiente (Id: ' . $billingPendingBilling->id . ' - Descripción: ' . $billingPendingBilling->description . ') no pertenece a la mascota seleccionada');
                    }
    
                    //Validando que el pendiente por facturar no se encuentre ya facturado
                    if ($billingPendingBilling->billed) {
                        abort(422, 'El pendiente (Id: ' . $billingPendingBilling->id . ' - Descripción: ' . $billingPendingBilling->description . ') ya se encuentra facturado');
                    }
                    
                    //Creando detalle de la factura
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
    
                    //Actualizando el pendiente por facturar como facturado
                    $billingPendingBilling->update(['billed' => true]);
                    /*Calculando total de la factura*/
                    $totalBill += $billingPendingBilling->price * $billingPendingBilling->quantity;
                }

                /* 2. El producto-servicio no es de un pendiente que ya existía (se agregó manualmente)*/
                if (!$productAndServiceChecked["id"] && ($productAndServiceChecked["product_id"] || $productAndServiceChecked["service_id"])) {
                    
                    $productOrService = null;

                    if ($productAndServiceChecked["product_id"]) {
                        $productOrService = Product::findOrFail($productAndServiceChecked["product_id"]);
                    }

                    if ($productAndServiceChecked["service_id"]) {
                        $productOrService = Service::findOrFail($productAndServiceChecked["service_id"]);
                    }

                    //Creando detalle de la factura
                    BillingBillDetail::create([
                        'billing_bill_id' => $billingBill->id,
                        'code' => $productOrService->code,
                        'description' => $productOrService->name,
                        'product_id' => $productAndServiceChecked["product_id"] ? $productOrService['id'] : null,
                        'service_id' => $productAndServiceChecked["service_id"] ? $productOrService['id'] : null,
                        'price' => $productOrService->price,
                        'quantity' => $productAndServiceChecked['quantity'],
                        'billing_pending_billing_id' => null,
                    ]);

                    //abort(422, );

                    /*Calculando total de la factura*/
                    $totalBill += $productOrService->price * $productAndServiceChecked['quantity'];

                }                

            }

            if ($totalBill > $request->moneyGiven) {
                abort(422, 'El valor entregado ($' . $request->moneyGiven . ') es inferior al total de la factura ($' . $totalBill . ')');
            }

            $billingBill->update(['total' => $totalBill, 'money_given' => $request->moneyGiven]);
    
        //});
        \DB::commit();
        } catch (\Exception $e) {
            abort(422, $e->getMessage());
            \DB::rollBack();
        }


        $billingBill = BillingBill::with('billingBillDetails')->where('id', $billingBill->id)->firstOrFail();

        $pdf = \PDF::loadView('bills.bill', compact('billingBill'));
        $pdf->setPaper('b7');
        return $pdf->stream('invoice.pdf');


    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProductsAndServices()
    {
        $products = Product::select(['id', 'code', 'name', 'price', \DB::raw('"Product" as type')])
                                ->where('name', 'like', '%' . request()->q . '%');
        $services = Service::select(['id', 'code', 'name', 'price', \DB::raw('"Service" as type')])
                                ->where('name', 'like', '%' . request()->q . '%')
                                ->union($products)->paginate(9);

        return $services;

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllBills()
    {
        return BillingBill::numberBill(request()->number_bill)
                ->whereHas('owner', function (Builder $query) {
                    $query->where('identification', 'like', '%' . request()->identification_or_names_from_owner . '%');
                })
                ->with(['billingBillDetails', 'owner'])->orderBy('date', 'desc')->paginate(12);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function cancelBill(BillingBill $billingBill)
    {
        if ($billingBill->cancelled) {
            return response()->json(['message' => 'La factura ya se encuentra anulada'], 422);
        }
        
        try {
            \DB::beginTransaction();
            
            //Cancelando factura
            $billingBill->update(['cancelled' => true]);

            //Buscando si el detalle de la factura tiene relación con un registro de pendiente por facturar para pasarlo a (no facturado) y eliminar la relación
            foreach ($billingBill->billingBillDetails as $billingBillDetail) {
                if ($billingBillDetail->billingPendingBilling) {
                    $billingBillDetail->billingPendingBilling()->update(['billed' => false]);
                    $billingBillDetail->update(['billing_pending_billing_id' => null]);
                }
            }
            
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function billReportGetAllBills(Request $request)
    {
        return BillingBill::with(['billingBillDetails', 'owner'])
                        ->whereBetween('date', [$request->from, Carbon::createFromDate($request->until)->addHour(24)->subMillisecond()])
                        ->orderBy('date', 'desc')->paginate(12);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function billReportGetAllBillsToExcel(Request $request)
    {
        $bills = BillingBill::with(['billingBillDetails', 'owner'])
                        ->whereBetween('date', [$request->from, Carbon::createFromDate($request->until)->addHour(24)->subMillisecond()])
                        ->orderBy('date', 'desc')->get();
        
            
        return \Excel::download(new BillsReportExport($bills), 'bills.xlsx');                    
    }
}
