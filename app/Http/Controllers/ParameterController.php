<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parameter;

class ParameterController extends Controller
{
    public function __construct () {
        $this->middleware('has.permission:parameters.update')->only('index', 'update');
    }

    public function index () {
        return Parameter::first();
    }

    public function update (Request $request) {
        //return 'hey';
        $data = $request->validate([
            'hours_products_and_services_applied' => 'required|numeric|min:0|max:100000',
            'days_alert_vaccines' => 'required|numeric|min:1|max:365',
        ], [], [
            'hours_products_and_services_applied' => 'Horas para actualización de productos y servicios aplicados',
            'days_alert_vaccines' => 'Días para alertar vacunas',
        ]);

        if ($parameter = Parameter::first()) {
            
        } else {
            $parameter = new Parameter;
        }

        $parameter->hours_products_and_services_applied = $data['hours_products_and_services_applied'];       
        $parameter->days_alert_vaccines = $data['days_alert_vaccines'];       
        $parameter->save();
    }
}
