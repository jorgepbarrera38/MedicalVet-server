<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\Service;

class ActivityController extends Controller
{
    public function __construct () {
        $this->middleware('has.permission:activities.index')->only('index');
        $this->middleware('has.permission:activities.store')->only(['store', 'getServices']);
        $this->middleware('has.permission:activities.update')->only('update');
        $this->middleware('has.permission:activities.destroy')->only('destroy');
    }

    public function index()
    {
        return Activity::with(['service:id,code,name'])->where('name', 'like', '%' . request()->search . '%')->paginate(7);
    }

    public function getServices()
    {
        return Service::orderBy('name', 'asc')->get(['id', 'code', 'name']);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:80|unique:activities,name',
            'duration' => 'required|numeric|min:1',
            'service_id' => 'required|exists:services,id',
        ], [], [
            'name' => 'Nombre',
            'duration' => 'Duración',
            'service_id' => 'Servicio',
        ]);
        //return $data;
        return Activity::create($data);
    }

    public function show($activity)
    {
        $activity = Activity::with(['service:id,code,name'])->where('id', $activity)->firstOrFail();
        $services = Service::orderBy('name', 'asc')->get(['id', 'code', 'name']);
        
        return [
            'services' => $services,
            'activity' => $activity,
        ];
    }

    public function update(Request $request, Activity $activity)
    {
        $data = $request->validate([
            'name' => 'required|max:80|unique:activities,name,' . $activity->id,
            'duration' => 'required|numeric|min:1',
            'service_id' => 'required|exists:services,id',
        ], [], [
            'name' => 'Nombre',
            'duration' => 'Duración',
            'service_id' => 'Servicio',
        ]);

        $activity->update($data);
    }

    public function destroy(Activity $activity)
    {
        if ($activity->appointments()->exists()) {
            abort(422);
        }
        $activity->delete();
    }
}
