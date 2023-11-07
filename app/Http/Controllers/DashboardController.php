<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClinicHistory;
use App\Models\Pet;
use App\Models\Owner;
use App\Models\Appointment;
use App\Models\Vaccine;
use App\Models\Parameter;
use App\Models\BillingBill;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function getData () {
        $petsQuantity = Pet::count();
        $ownersQuantity = Owner::count();
        $clinicHistoriesQuantity = ClinicHistory::count();
        
        $appointmentsQuantity = Appointment::with(['pet:id,name,owner_id', 'consultingroom:id,name', 'activity:id,name'])
                                    ->with('pet.owner:id,identification,names,surnames')
                                    ->orderBy('date_time_start', 'ASC')
                                    ->whereBetween('date_time_start', 
                                                    [ Carbon::now()->format('Y-m-d'), 
                                                    Carbon::createFromDate(Carbon::now()->format('Y-m-d'))->addHours(23)->addMinutes(59)->addSeconds(59) 
                                                    ]
                                    )
                                    ->where('state', 'PENDIENTE')
                                    ->count();

        $dateNow = Carbon::now()->format('Y-m-d');
        $daysAlertVaccines = 0;

        $parameters = Parameter::first();
        
        if ($parameters && $parameters->days_alert_vaccines) {
            $dateFinish = Carbon::now()->addDays($parameters->days_alert_vaccines - 1)->format('Y-m-d');
            $daysAlertVaccines = $parameters->days_alert_vaccines;
        } else {
            $dateFinish = Carbon::now()->addDays(6)->format('Y-m-d');
        }

        $vaccines = Vaccine::select('pet_id', \DB::raw('max(next_date) as nextDate'))
                                ->groupBy('pet_id')
                                ->havingRaw('max(next_date) >= ?', [$dateNow])
                                ->havingRaw('max(next_date) <= ?', [$dateFinish])
                                ->with('pet.owner:id,identification,names,surnames,phonenumber')
                                ->with('pet:id,name,owner_id')
                                //->orderBy('nextDate', 'asc')*/
                                ->get();

        $totalBillCurrentMonth =  BillingBill::where('cancelled', false)->sum('total');
                                    
        return [
            'petsQuantity' => $petsQuantity,
            'totalBillCurrentMonth' => $totalBillCurrentMonth,
            'ownersQuantity' => $ownersQuantity,
            'clinicHistoriesQuantity' => $clinicHistoriesQuantity,
            'vaccines' => $vaccines,
            'appointmentsQuantity' => $appointmentsQuantity,
            'daysAlertVaccines' => $daysAlertVaccines,
        ];
    }
}
