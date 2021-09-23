<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;

class ActivityController extends Controller
{
    public function __construct () {
        $this->middleware('has.permission:activities.index')->only('index');
        $this->middleware('has.permission:activities.store')->only('store');
        $this->middleware('has.permission:activities.update')->only('update');
        $this->middleware('has.permission:activities.destroy')->only('destroy');
    }

    public function index()
    {
        return Activity::where('name', 'like', '%' . request()->search . '%')->paginate(7);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:80|unique:activities,name',
            'duration' => 'required|numeric|min:1'
        ], [], [
            'name' => 'Nombre',
            'duration' => 'Duración'
        ]);
        //return $data;
        return Activity::create($data);
    }

    public function show(Activity $activity)
    {
        return $activity;
    }

    public function update(Request $request, Activity $activity)
    {
        $data = $request->validate([
            'name' => 'required|max:80|unique:activities,name,' . $activity->id,
            'duration' => 'required|numeric|min:1'
        ], [], [
            'name' => 'Nombre',
            'duration' => 'Duración'
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
