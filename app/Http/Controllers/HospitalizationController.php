<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Pet;
use App\Models\Hospitalization;
use App\Models\BillingPendingBilling;
use App\Models\ClinicHistoryService;
use Carbon\Carbon;

class HospitalizationController extends Controller
{
    public function getDataToHospitalizePet () {
        $services = Service::orderBy('name', 'asc')->get();

        return [
            'services' => $services,
        ];
    }

    public function hospitalizePet (Request $request, Pet $pet) {
        /*Inicio verificando si la mascota ya se encuentra hospitalizada*/
        $petIsHospitalized = Hospitalization::where('pet_id', $pet->id)->where('status', 'active')->first();

        if ($petIsHospitalized) {
            return response()->json(['errors' => ['message' => ['La mascota ya se encuentra hospitalizada']]], 422);
        }
        /*Fin verificando si la mascota ya se encuentra hospitalizada*/

        $data = $request->validate([
            'initial_date' => 'required|date|before_or_equal:now',
            'service_id' => 'required|exists:services,id',
        ], [
            'initial_date.before_or_equal' => 'El campo Fecha y hora de hospitalización debe ser una fecha anterior o igual a la fecha actual'
        ], [
            'initial_date' => 'Fecha y hora de hospitalización',
            'service_id' => 'Servicio',
        ]);

        /*Inicio verificando que la fecha y hora de hospitalización no se cruce con otras estancias anteriores de la mascota*/
        $hospitalizationIntersected = Hospitalization::where('pet_id', $pet->id)->where('status', 'finished')->whereBetween('initial_date', [$data['initial_date'], now()])->first();
        
        if ($hospitalizationIntersected) {
            return response()->json(['errors' => ['message' => ['El campo Fecha y hora de hospitalización se cruza con una hospitalización anterior']]], 422);   
        }

        $hospitalizationIntersected = Hospitalization::where('pet_id', $pet->id)->where('status', 'finished')->whereBetween('final_date', [$data['initial_date'], now()])->first();
        if ($hospitalizationIntersected) {
            return response()->json(['errors' => ['message' => ['El campo Fecha y hora de hospitalización se cruza con una hospitalización anterior']]], 422);   
        }
        /*Fin verificando que la fecha y hora de hospitalización no se cruce con otras estancias anteriores de la mascota*/

        Hospitalization::create([
            'pet_id' => $pet->id,
            'initial_date' => $data['initial_date'],
            'service_id' => $data['service_id'],
            'quantity_hours' => 0,
            'user_id' => auth()->user()->id,
        ]);
    }
    
    public function updateHospitalizationFromPet (Request $request, Pet $pet) {
        /*Inicio verificando si la mascota ya se encuentra hospitalizada*/
        $petIsHospitalized = Hospitalization::where('pet_id', $pet->id)->where('status', 'active')->first();

        if (!$petIsHospitalized) {
            return response()->json(['errors' => ['message' => ['La mascota no se encuentra hospitalizada']]], 422);
        }
        /*Fin verificando si la mascota ya se encuentra hospitalizada*/

        $data = $request->validate([
            'initial_date' => 'required|date|before_or_equal:now',
            'service_id' => 'required|exists:services,id',
        ], [
            'initial_date.before_or_equal' => 'El campo Fecha y hora de hospitalización debe ser una fecha anterior o igual a la fecha actual'
        ], [
            'initial_date' => 'Fecha y hora de hospitalización',
            'service_id' => 'Servicio',
        ]);

        /*Inicio verificando que la fecha y hora de hospitalización no se cruce con otras estancias anteriores de la mascota*/
        $hospitalizationIntersected = Hospitalization::where('pet_id', $pet->id)->where('status', 'finished')->whereBetween('initial_date', [$data['initial_date'], now()])->first();
        
        if ($hospitalizationIntersected) {
            return response()->json(['errors' => ['message' => ['El campo Fecha y hora de hospitalización se cruza con una hospitalización anterior']]], 422);   
        }

        $hospitalizationIntersected = Hospitalization::where('pet_id', $pet->id)->where('status', 'finished')->whereBetween('final_date', [$data['initial_date'], now()])->first();
        if ($hospitalizationIntersected) {
            return response()->json(['errors' => ['message' => ['El campo Fecha y hora de hospitalización se cruza con una hospitalización anterior']]], 422);   
        }
        /*Fin verificando que la fecha y hora de hospitalización no se cruce con otras estancias anteriores de la mascota*/

        $petIsHospitalized->update([
            'initial_date' => $data['initial_date'],
            'service_id' => $data['service_id'],
            'user_id' => auth()->user()->id,
        ]);
    }

    public function finalizeHospitalization (Request $request, Pet $pet) {
        /*Inicio verificando si la mascota se encuentra hospitalizada*/
        $hospitalizationFromPet = Hospitalization::where('pet_id', $pet->id)->where('status', 'active')->first();

        if (!$hospitalizationFromPet) {
            return response()->json(['errors' => ['message' => ['La mascota no se encuentra hospitalizada']]], 422);
        }
        /*Fin verificando si la mascota ya se encuentra hospitalizada*/

        $data = $request->validate([
            'final_date' => 'required|date|after_or_equal:'. $hospitalizationFromPet->initial_date .'|before_or_equal:now',
            //'quantity_hours' => 'required|numeric|min:0|max:10000',
        ], [
            'final_date.after_or_equal' => 'El campo Fecha y hora de egreso debe ser una fecha posterior o igual a la fecha y hora de hospitalización',
            'final_date.before_or_equal' => 'El campo Fecha y hora de egreso debe ser una fecha anterior o igual a la fecha actual',
        ], [
            'final_date' => 'Fecha y hora de egreso',
            //'quantity_hours' => 'Estancia total',
        ]);

        /*Calculando total horas*/
        \Log::info(Carbon::createFromDate($data['final_date']));
        $differenceMinutesBetweenDates = Carbon::createFromDate($data['final_date'])->diffInMinutes($hospitalizationFromPet->initial_date);
        $quantity_hours = round($differenceMinutesBetweenDates / 60);
        /*Calculando total horas*/

        \DB::transaction(function () use ($hospitalizationFromPet, $data, $quantity_hours) {
            
            /*Generando servicio pendiente*/
            $billingPendingBilling = BillingPendingBilling::create([
                'datetime' => $data['final_date'],
                'code' => $hospitalizationFromPet->service->code,
                'description' => $hospitalizationFromPet->service->name,
                'product_id' => null,
                'service_id' => $hospitalizationFromPet->service->id,
                'price' => $hospitalizationFromPet->service->price,
                'quantity' => $quantity_hours,
                'owner_id' => $hospitalizationFromPet->pet->owner_id,
                'pet_id' => $hospitalizationFromPet->pet_id,
            ]);
            /*Generando servicio pendiente*/

            /*Registrando el servicio prestado de la cita en productos y servicios aplicados*/
            $clinicHistoryService = ClinicHistoryService::create([
                'pet_id' => $hospitalizationFromPet->pet_id,
                'service_id' => $hospitalizationFromPet->service->id,
                'quantity' => $quantity_hours,
                'application_date' => $data['final_date'],
                'billing_pending_billing_id' => $billingPendingBilling->id,
                'user_id' => auth()->user()->id,
            ]);
            /*Registrando el servicio prestado de la cita en productos y servicios aplicado*/

            /*Actualizando datos de la hospitalización*/
            $hospitalizationFromPet->update([
                'final_date' => $data['final_date'],
                'status' => 'Finished',
                'quantity_hours' => $quantity_hours,
                'user_finalized_id' => auth()->user()->id,
                'clinic_history_service_id' => $clinicHistoryService->id,
            ]);
            /*Actualizando datos de la hospitalización*/
        });
    }

    public function destroy (Hospitalization $hospitalization) {
        //Validando si el pendiente por facturar está facturado
        $billingPendingBillingId = null;

        //Verificando que el producto no esté facturado
        if ($hospitalization->clinicHistoryService->billingPendingBilling->billed) {

            $number_bill = $hospitalization->clinicHistoryService->billingPendingBilling->billingBillDetail->billingBill->number_bill;
            return response()->json(['El registro de hospitalización no se puede eliminar ya que se encuentra facturado en la factura No.: ' . $number_bill], 422);

        }

        //Si el producto NO se encuentra facturado y se guarda su id para su eliminación al final por integridad referencial
        $clinicHistoryServiceId = $hospitalization->clinicHistoryService->id;
        $billingPendingBillingId = $hospitalization->clinicHistoryService->billingPendingBilling->id;

        \DB::transaction(function () use ($hospitalization, $clinicHistoryServiceId, $billingPendingBillingId) {

            //Eliminando hospitalización
            $hospitalization->delete();

            //Eliminando servicio aplicado
            ClinicHistoryService::findOrFail($clinicHistoryServiceId)->delete();

            //Eliminando el pendiente por facturar
            BillingPendingBilling::findOrFail($billingPendingBillingId)->delete();


        });

        return response()->json(['message' => 'Hospitalization deleted'], 200);
    }
}
