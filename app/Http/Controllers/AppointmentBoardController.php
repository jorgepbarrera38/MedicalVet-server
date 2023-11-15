<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentBoardController extends Controller
{
    public function __construct () {
        $this->middleware('has.permission:appointments.board')->only('getAllAppointments');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllAppointments ()
    {
        $initialDate = Carbon::createFromDate(now()->toDateString());
        $finalDate = Carbon::createFromDate(now()->toDateString())->addDay()->subMillisecond();
        $appointments = Appointment::with(['pet.owner:id,identification,names,surnames', 'pet:id,name,owner_id', 'activity:id,name', 'consultingroom:id,name'])
                                ->where('state','PENDIENTE')
                                ->whereBetween('date_time_start', [$initialDate, $finalDate])
                                ->select('id', 'pet_id', 'consultingroom_id', 'activity_id', 'date_time_start', 'duration', 'date_time_finish', 'state')
                                ->orderBy('date_time_start', 'asc')
                            ->get();

        return $appointments;
    }

    public function getAppointmentsByDate (Request $request)
    {
        if (empty($request->initialDate) || empty($request->finalDate)) {
            $initialDate = Carbon::createFromDate(now()->toDateString());
            $finalDate = Carbon::createFromDate(now()->toDateString())->addDay()->subMillisecond();
            $appointments = Appointment::with(['pet.owner:id,identification,names,surnames', 'pet:id,name,owner_id', 'activity:id,name', 'consultingroom:id,name'])
                                    ->where('state','PENDIENTE')
                                    ->whereBetween('date_time_start', [$initialDate, $finalDate])
                                    ->select('id', 'pet_id', 'consultingroom_id', 'activity_id', 'date_time_start', 'duration', 'date_time_finish', 'state')
                                    ->orderBy('date_time_start', 'asc')
                                ->get();
        } else {
            $initialDate = Carbon::createFromDate($request->initialDate);
            $finalDate = Carbon::createFromDate($request->finalDate)->addDay()->subMillisecond();
            $appointments = Appointment::with(['pet.owner:id,identification,names,surnames', 'pet:id,name,owner_id', 'activity:id,name', 'consultingroom:id,name'])
                                    ->where('state','PENDIENTE')
                                    ->whereBetween('date_time_start', [$initialDate, $finalDate])
                                    ->select('id', 'pet_id', 'consultingroom_id', 'activity_id', 'date_time_start', 'duration', 'date_time_finish', 'state')
                                    ->orderBy('date_time_start', 'asc')
                                ->get();
        }

        return $appointments;
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
