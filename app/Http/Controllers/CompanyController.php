<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Zip;

class CompanyController extends Controller
{
    public function __construct () {
        $this->middleware('has.permission:company.update')->only('index', 'update');
    }

    public function index () {
        return Company::first();
    }

    public function update (Request $request) {

        $data = $request->validate([
            'name' => 'required|max:100',
            'nit' => 'required|max:20',
            'address' => 'required|max:50',
            'slogan' => 'required|max:150',
            'logo' => 'nullable|mimes:jpg,jpeg,png|dimensions:max_with=500,max_height=500',

            'email' => 'nullable|email|max:90',
            'email_host' => 'nullable|max:50',
            'email_port' => 'nullable|max:50',
            'email_password' => 'nullable|max:50',
            'email_encryption' => 'nullable|max:50',
        ], [
            'logo.dimensions' => 'La imagen no debe ser superior a 500x500 pixeles.'
        ], [
            'name' => 'Nombre',
            'nit' => 'Nit',
            'address' => 'Dirección',
            'slogan' => 'Eslogan',
            'email' => 'Correo electrónico'
        ]);

        $file;
        if ($request->hasFile('logo')) {
            $file = Storage::putFile('company', $request->logo);
        }

        if ($company = Company::first()) {
            $company->name = $data['name'];
            $company->address = $data['address'];
            $company->slogan = $data['slogan'];
            $company->nit = $data['nit'];
            $company->email = $data['email'];
            $company->email_host = $data['email_host'];
            $company->email_port = $data['email_port'];
            $company->email_password = $data['email_password'];
            $company->email_encryption = $data['email_encryption'];

            if ($request->hasFile('logo') && $company->logo && Storage::exists($company->logo)) {
                Storage::delete($company->logo);
            }
            
            if ($request->hasFile('logo')) {
                $company->logo = $file;
            }

            return $company->save();
        }

        $company = (new Company)->fill($data);
        if ($request->hasFile('logo')) {
            $company->logo = $file;
        }
        $company->save();
    }

    public function downloadBackupFiles () {
        if (Storage::exists('backup-files.zip')) {
            Storage::delete('backup-files.zip');
        }

        $zip = Zip::create('backup-files.zip');
        
        $zip->add(public_path('images'));
        $zip->add(public_path('signatures'));
        $zip->add(public_path('company'));
        $zip->add(public_path('clinic-histories'));
        $zip->close();

        if (Storage::exists('backup-files.zip')) {
            return response()->download(public_path('backup-files.zip'));
        }

        return response()->json(['No hay datos para descargar'], 422);
    
    }
}
