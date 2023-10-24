<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consultingroom;
use App\Models\Activity;
use App\Models\Pet;
use App\Models\Company;
use App\Models\Appointment;
use App\Models\ReasonCancellation;
use App\Models\BillingPendingBilling;
use \Carbon\Carbon;

class AppointmentController extends Controller
{

    public function __construct () {
        $this->middleware('has.permission:appointments.store')->only('store');
        $this->middleware('has.permission:appointments.fulfill')->only('fulfillAppointment');
        $this->middleware('has.permission:appointments.unfulfill')->only('unFulfillAppointment');
        $this->middleware('has.permission:appointments.cancel')->only('cancelAppointment');
        $this->middleware('has.permission:appointments.report-appointments')->only('reportGetAppointments');
        $this->middleware('has.permission:appointments.report-appointments-export')->only('fulfillAppointment');
    }

    public function index () {

        return Appointment::with(['pet:id,name,owner_id', 'consultingroom:id,name', 'activity:id,name'])
                                ->with('pet.owner:id,identification,names,surnames')
                                ->orderBy('date_time_start', 'ASC')
                                ->whereBetween('date_time_start',
                                                [ request()->date,
                                                  Carbon::createFromDate(request()->date)->addHours(23)->addMinutes(59)->addSeconds(59)
                                                ]
                                )
                                ->where('state', 'PENDIENTE')
                                ->get(['id', 'date_time_start', 'date_time_finish', 'observations', 'pet_id', 'consultingroom_id', 'activity_id', 'state']);
    }

    public function getDataToCreateAppointment () {
        $consultingrooms = Consultingroom::orderBy('name')->get(['id', 'name']);
        $activities = Activity::orderBy('name')->get(['id', 'name', 'duration']);

        return [
            'consultingrooms' => $consultingrooms,
            'activities' => $activities
        ];
    }

    public function getAppointments (Pet $pet) {
        
        return $pet->appointments()->with(['consultingroom:id,name', 'activity:id,name'])->orderBy('date_time_start', 'desc')->get();
    }

    public function store (Request $request) {
        //Validando disponibilidad de agenda
        $data = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'date_time_start' => 'required|date|after_or_equal:' . Carbon::now()->format('Y-m-d H:i'),
            'consultingroom_id' => 'required|exists:consultingrooms,id',
            'activity_id' => 'required|exists:activities,id',
            'duration' => 'required|numeric|min:1',
            'observations' => 'nullable|max:300',
        ], [
            'date_time_start.after_or_equal' => 'El campo fecha debe ser mayor o igual a la fecha actual'
        ], [
            'pet_id' => 'Mascota',
            'consultingroom_id' => 'Consultorio',
            'activity_id' => 'Actividad',
            'date_time_start' => 'Fecha',
            'duration' => 'Duración',
            'observations' => 'Observaciónes'
        ]);

        $thereIsAppointmentCrossing = Appointment::where('consultingroom_id', $data['consultingroom_id'])
                            ->where(function ($query) use ($data) {

                                $query->where(function ($query) use ($data) {

                                        $query->where(function ($query) use ($data) {
                                            $query->where('date_time_start', '<=', Carbon::createFromDate($data['date_time_start'])->subMinutes(1))
                                                ->where('date_time_finish', '>=', Carbon::createFromDate($data['date_time_start'])->addMinutes(1));
                                        })
                                        ->orWhere(function ($query) use ($data) {
                                            $query->where('date_time_start', '<=', Carbon::createFromDate($data['date_time_start'])->addMinutes($data['duration'] - 1))
                                                ->where('date_time_finish', '>=', Carbon::createFromDate($data['date_time_start'])->addMinutes($data['duration'] + 1));
                                        });

                                })->orWhere(function ($query) use ($data) {

                                    $query->where('date_time_start', '>=', Carbon::createFromDate($data['date_time_start'])->subMinutes(1))
                                        ->where('date_time_finish', '<=', Carbon::createFromDate($data['date_time_start'])->addMinutes($data['duration'] + 1));

                                });

                            })
                        ->where('state', 'PENDIENTE')
                        ->exists();

        if ($thereIsAppointmentCrossing) {
            return response()->json(['errors' => ['thereIsAppointmentCrossing' => ['Hay citas asignadas dentro del rango de fechas seleccionado para este consultorio']]], 422);
        }

        $data['date_time_finish'] = \Carbon\Carbon::createFromDate($data['date_time_start'])->addMinutes($data['duration']);
        $data['user_id_creates'] = auth()->user()->id;

        Appointment::create($data);

        return $request->all();
    }

    //Cumplir cita
    public function fulfillAppointment (Appointment $appointment) {
        if ($appointment->state != 'PENDIENTE') {
            return response()->json(['errors' => ['message' => ['El estado actual de la cita es ' . $appointment->state]]], 422);
        }

        //Validar que la fecha de la cita sea menor a la fecha y hora actual

        $actualDateTime = Carbon::createFromDate();

        if (Carbon::createFromDate($appointment->date_time_start) >= $actualDateTime->subSecond($actualDateTime->second)) {
            return response()->json(['errors' => ['message' => ['La cita aún no ha iniciado']]], 422);
        }

        //Validando que la actividad tenga un servicio relacionado para agregarlo a pendiente por facturar
        if (!$appointment->activity->service) {
            return response()->json(['errors' => ['message' => ['La actividad no tiene servicio relacionado']]], 422);
        }

        \DB::transaction(function () use ($appointment) {

            /*Generando servicio pendiente*/
            $billingPendingBilling = BillingPendingBilling::create([
                'datetime' => now(),
                'code' => $appointment->activity->service->code,
                'description' => $appointment->activity->service->name,
                'product_id' => null,
                'service_id' => $appointment->activity->service->id,
                'price' => $appointment->activity->service->price,
                'quantity' => 1,
                'owner_id' => $appointment->pet->owner_id,
                'pet_id' => $appointment->pet_id,
            ]);
            
            $appointment->update(['state' => 'CUMPLIDA', 'user_id_fulfill' => auth()->user()->id, 'billing_pending_billing_id' => $billingPendingBilling->id]);
        });
    }

    //Incumplir cita
    public function unFulfillAppointment (Appointment $appointment) {
        //Validar que la cita esté pendiente
        if ($appointment->state != 'PENDIENTE') {
            return response()->json(['errors' => ['message' => ['El estado actual de la cita es ' . $appointment->state]]], 422);
        }

        //Validar que la fecha de la cita sea menor a la fecha y hora actual

        $actualDateTime = Carbon::createFromDate();
        
        if (Carbon::createFromDate($appointment->date_time_start) >= $actualDateTime->subSecond($actualDateTime->second)) {
            return response()->json(['errors' => ['message' => ['La cita aún no ha iniciado']]], 422);
        }

        $appointment->update(['state' => 'INCUMPLIDA', 'user_id_unfulfill' => auth()->user()->id]);
    }

    //Cancelar cita
    public function cancelAppointment (Request $request, Appointment $appointment) {
        if ($appointment->state != 'PENDIENTE') {
            return response()->json(['errors' => ['message' => ['El estado actual de la cita es ' . $appointment->state]]], 422);
        }

        $request->validate([
            'reason_cancellation_id' => 'required|exists:reason_cancellations,id'
        ], [], [
            'reason_cancellation_id' => 'Motivo de cancelación'
        ]);

        $appointment->update(['state' => 'CANCELADA', 'reason_cancellation_id' => $request->reason_cancellation_id, 'user_id_cancel' => auth()->user()->id]);
    }

    public function getReasonCancellations () {
        return ReasonCancellation::orderBy('name')->get(['id', 'name']);
    }

    public function reportGetAppointments (Request $request) {

        return Appointment::whereBetween('date_time_start',
                                            [
                                                $request->from,
                                                Carbon::createFromDate($request->until)->addHours(23)->addMinutes(59)->addSeconds(59)
                                            ])
                            ->when($request->pending == 'false', function ($query) {
                                return $query->where('state', '<>', 'PENDIENTE');
                            })
                            ->when($request->fulfilled == 'false', function ($query) {
                                return $query->where('state', '<>', 'CUMPLIDA');
                            })
                            ->when($request->unFulfilled == 'false', function ($query) {
                                return $query->where('state', '<>', 'INCUMPLIDA');
                            })
                            ->when($request->cancelled == 'false', function ($query) {
                                return $query->where('state', '<>', 'CANCELADA');
                            })
                            ->with(['pet:id,name,owner_id', 'activity:id,name', 'consultingroom:id,name', 'reasonCancellation:id,name'])
                            ->with('pet.owner:id,identification,names,surnames')
                            ->orderBy('date_time_start', 'ASC')
                            ->get(['id', 'date_time_start', 'duration', 'state', 'pet_id', 'activity_id', 'consultingroom_id', 'user_id_creates', 'user_id_fulfill', 'user_id_unfulfill', 'user_id_cancel', 'reason_cancellation_id', 'observations']);
    }

    public function reportGetAppointmentsToPdf (Request $request) {

        $appointments = Appointment::whereBetween('date_time_start',
                                            [
                                                $request->from,
                                                Carbon::createFromDate($request->until)->addHours(23)->addMinutes(59)->addSeconds(59)
                                            ])
                            ->when($request->pending == 'false', function ($query) {
                                return $query->where('state', '<>', 'PENDIENTE');
                            })
                            ->when($request->fulfilled == 'false', function ($query) {
                                return $query->where('state', '<>', 'CUMPLIDA');
                            })
                            ->when($request->unFulfilled == 'false', function ($query) {
                                return $query->where('state', '<>', 'INCUMPLIDA');
                            })
                            ->when($request->cancelled == 'false', function ($query) {
                                return $query->where('state', '<>', 'CANCELADA');
                            })
                            ->with(['pet:id,name,owner_id', 'activity:id,name', 'consultingroom:id,name', 'reasonCancellation:id,name'])
                            ->with('pet.owner:id,identification,names,surnames')
                            ->orderBy('date_time_start', 'ASC')
                            ->get(['id', 'clinic_history_id', 'date_time_start', 'duration', 'state', 'pet_id', 'activity_id', 'consultingroom_id', 'user_id_creates', 'user_id_fulfill', 'user_id_unfulfill', 'user_id_cancel', 'reason_cancellation_id', 'observations']);

        $company = Company::first();

        $pdf = \PDF::loadView('reports.appointments.appointments', compact('appointments', 'company'))
                        ->setPaper('letter', 'landscape');
        return $pdf->stream('invoice.pdf');
    }
}

