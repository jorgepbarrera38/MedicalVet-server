<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function __construct () {
        $this->middleware('has.permission:users.index')->only('index');
        $this->middleware('has.permission:users.store')->only('store');
        $this->middleware('has.permission:users.update')->only('update');
        $this->middleware('has.permission:users.destroy')->only('destroy');
        $this->middleware('has.permission:users.permission')->only('getPermissions', 'updatePermissions');
    }

    public function index () {
        return User::where('username', 'like', '%' . request()->search . '%')
                        ->paginate(7);
    }

    public function store (Request $request) {
        $data = $request->validate([
            'username' => 'required|max:30|unique:users,username',
            'identification' => 'required|max:15',
            'names' => 'required|max:50',
            'surnames' => 'required|max:50',
            'password' => 'required|max:15',
            'is_vet' => 'required|boolean',
            'professional_register' => 'nullable|max:40',
            'signature' => 'nullable|mimes:jpg,jpeg,png|dimensions:max_width=500,max-height=500'
        ], [
            'signature.dimensions' => 'La firma no debe ser mayor a 500px-500px'
        ], [
            'username' => 'Nombre de usuario',
            'identification' => 'Identificación',
            'names' => 'Nombres',
            'surnames' => 'Apellidos',
            'password' => 'Contraseña',
            'signature' => 'Firma',
            'is_vet' => 'Usuario es veterinario',
            'professional_register' => 'Matrícula profesional',
        ]);

        $user = (new User)->fill($data);
        $user->username = strtolower($request->username);

        if ($request->hasFile('signature')) {
            $signature = Storage::putFile('signatures', $request->signature);
            $user->signature = $signature;
        }

        $user->password = bcrypt($data['password']);
        $user->save();
    }

    public function show (User $user) {
        return $user;
    }

    public function update (Request $request, User $user) {
        $data = $request->validate([
            'username' => 'required|max:30|unique:users,username,' . $user->id,
            'identification' => 'required|max:15',
            'names' => 'required|max:50',
            'surnames' => 'required|max:50',
            'password' => 'nullable|max:15',
            'is_vet' => 'required|boolean',
            'professional_register' => 'nullable|max:40',
            'signature' => 'nullable|mimes:jpg,jpeg,png|dimensions:max_width=500,max-height=500'
        ], [
            'signature.dimensions' => 'La firma no debe ser mayor a 500px-500px'
        ], [
            'username' => 'Nombre de usuario',
            'identification' => 'Identificación',
            'names' => 'Nombres',
            'surnames' => 'Apellidos',
            'password' => 'Contraseña',
            'signature' => 'Firma',
            'is_vet' => 'Usuario es veterinario',
            'professional_register' => 'Matrícula profesional',
        ]);

        if ($request->hasFile('signature')) {
            if (Storage::exists($user->signature)) {
                Storage::delete($user->signature);
            }
            $signature = Storage::putFile('signatures', $request->signature);
            $user->signature = $signature;
        }

        if ($request->password) {
            $data['password'] = bcrypt($data['password']);
        }

        $user->username = strtolower($request->username);

        $user->update($data);
    }

    public function destroy (User $user) {
        try {
            $user->delete();
        } catch (\Throwable $th) {
            abort(402);
        }
    }

    public function getPermissions ($user) {
        $user = User::where('id', $user)
                ->with('permissions')
                ->firstOrFail(['id', 'names', 'surnames']);

        $permissions = Permission::orderBy('id', 'asc')->get();
        return [
            'user' => $user,
            'permissions' => $permissions
        ];
    }

    public function updatePermissions (Request $request, User $user) {
        $idsFromPermissions = collect($request->all())->pluck('id');    
        $user->permissions()->sync($idsFromPermissions);
    }
}

