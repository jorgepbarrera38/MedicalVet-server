<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function __construct () {
        $this->middleware('has.permission:services.index')->only('index');
        $this->middleware('has.permission:services.store')->only('store');
        $this->middleware('has.permission:services.update')->only('update');
        $this->middleware('has.permission:services.destroy')->only('destroy');
    }

    public function index()
    {
        return Service::where('name', 'like', '%' . request()->search . '%')
                            ->paginate(7);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|max:20|unique:services,code',
            'name' => 'required|max:100|unique:services,name',
            'price' => 'required|numeric|min:0|max:50000000'
        ], [], [
            'code' => 'Código',
            'name' => 'Nombre',
            'price' => 'Precio'
        ]);

        $data['user_id'] = auth()->user()->id;
        //return $data;
        return Service::create($data);
    }

    public function show(Service $service) {
        return $service;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'code' => 'required|max:20|unique:services,code,' . $service->id,
            'name' => 'required|max:100|unique:services,name,' . $service->id,
            'price' => 'required|numeric|min:0|max:50000000'
        ], [], [
            'code' => 'Código',
            'name' => 'Nombre',
            'price' => 'Precio'
        ]);

        $service->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(Service $service)
    {
        if ($service->clinicHistoryServices()->exists()) {
            abort(422);
        }
        $service->delete();
    }
}
