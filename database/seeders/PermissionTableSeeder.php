<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Mascotas
        Permission::create([
            'name' => 'pets.index',
            'description' => 'Listar mascotas'
        ]);
        Permission::create([
            'name' => 'pets.store',
            'description' => 'Registrar mascotas'
        ]);
        Permission::create([
            'name' => 'pets.update',
            'description' => 'Editar mascotas'
        ]);
        Permission::create([
            'name' => 'pets.destroy',
            'description' => 'Eliminar mascotas'
        ]);

        //Propietarios
        Permission::create([
            'name' => 'owners.index',
            'description' => 'Listar propietarios'
        ]);
        Permission::create([
            'name' => 'owners.store',
            'description' => 'Registrar propietarios'
        ]);
        Permission::create([
            'name' => 'owners.update',
            'description' => 'Editar propietarios'
        ]);
        Permission::create([
            'name' => 'owners.destroy',
            'description' => 'Eliminar propietarios'
        ]);

        //Especies
        Permission::create([
            'name' => 'species.index',
            'description' => 'Listar especies'
        ]);
        Permission::create([
            'name' => 'species.store',
            'description' => 'Registrar especies'
        ]);
        Permission::create([
            'name' => 'species.update',
            'description' => 'Editar especies'
        ]);
        Permission::create([
            'name' => 'species.destroy',
            'description' => 'Eliminar especies'
        ]);

        //Razas
        Permission::create([
            'name' => 'races.index',
            'description' => 'Listar razas'
        ]);
        Permission::create([
            'name' => 'races.store',
            'description' => 'Registrar razas'
        ]);
        Permission::create([
            'name' => 'races.update',
            'description' => 'Editar razas'
        ]);
        Permission::create([
            'name' => 'races.destroy',
            'description' => 'Eliminar razas'
        ]);

        //Departamento
        Permission::create([
            'name' => 'states.index',
            'description' => 'Listar departamentos'
        ]);
        Permission::create([
            'name' => 'states.store',
            'description' => 'Registrar departamentos'
        ]);
        Permission::create([
            'name' => 'states.update',
            'description' => 'Editar departamentos'
        ]);
        Permission::create([
            'name' => 'states.destroy',
            'description' => 'Eliminar departamentos'
        ]);

        //Ciudades
        Permission::create([
            'name' => 'cities.index',
            'description' => 'Listar ciudades'
        ]);
        Permission::create([
            'name' => 'cities.store',
            'description' => 'Registrar ciudades'
        ]);
        Permission::create([
            'name' => 'cities.update',
            'description' => 'Editar ciudades'
        ]);
        Permission::create([
            'name' => 'cities.destroy',
            'description' => 'Eliminar ciudades'
        ]);

        //Citas
        Permission::create([
            'name' => 'appointments.store',
            'description' => 'Agendar citas'
        ]);
        Permission::create([
            'name' => 'appointments.fulfill',
            'description' => 'Cumplir citas'
        ]);
        Permission::create([
            'name' => 'appointments.unfulfill',
            'description' => 'Incumplir citas'
        ]);
        Permission::create([
            'name' => 'appointments.cancel',
            'description' => 'Cancelar citas'
        ]);

        //Consultorios
        Permission::create([
            'name' => 'consultingrooms.index',
            'description' => 'Listar consultorios'
        ]);
        Permission::create([
            'name' => 'consultingrooms.store',
            'description' => 'Registrar consultorios'
        ]);
        Permission::create([
            'name' => 'consultingrooms.update',
            'description' => 'Editar consultorios'
        ]);
        Permission::create([
            'name' => 'consultingrooms.destroy',
            'description' => 'Eliminar consultorios'
        ]);

        //Actividades
        Permission::create([
            'name' => 'activities.index',
            'description' => 'Listar actividades'
        ]);
        Permission::create([
            'name' => 'activities.store',
            'description' => 'Registrar actividades'
        ]);
        Permission::create([
            'name' => 'activities.update',
            'description' => 'Editar actividades'
        ]);
        Permission::create([
            'name' => 'activities.destroy',
            'description' => 'Eliminar actividades'
        ]);

        //Motivos de cancelación
        Permission::create([
            'name' => 'reason-cancellations.index',
            'description' => 'Listar motivos de cancelación'
        ]);
        Permission::create([
            'name' => 'reason-cancellations.store',
            'description' => 'Registrar motivos de cancelación'
        ]);
        Permission::create([
            'name' => 'reason-cancellations.update',
            'description' => 'Editar motivos de cancelación'
        ]);
        Permission::create([
            'name' => 'reason-cancellations.destroy',
            'description' => 'Eliminar motivos de cancelación'
        ]);

        //Reporte de citas
        Permission::create([
            'name' => 'appointments.report-appointments',
            'description' => 'Ver reporte de citas'
        ]);
        Permission::create([
            'name' => 'appointments.report-appointments-export',
            'description' => 'Exportar reporte de citas'
        ]);

        //Historias clínicas
        Permission::create([
            'name' => 'clinic-histories.store',
            'description' => 'Registrar historias clínicas'
        ]);
        Permission::create([
            'name' => 'clinic-histories.show',
            'description' => 'Consultar historias clínicas'
        ]);
        Permission::create([
            'name' => 'clinic-histories.show-vaccine',
            'description' => 'Consultar informe de vacunación'
        ]);
        Permission::create([
            'name' => 'clinic-histories.show-product-and-services-applied',
            'description' => 'Consultar productos y servicios aplicados'
        ]);
        Permission::create([
            'name' => 'clinic-histories.edit-quantity-products-applied',
            'description' => 'Editar cantidades de productos aplicados'
        ]);
        Permission::create([
            'name' => 'clinic-histories.delete-products-applied',
            'description' => 'Eliminar productos aplicados'
        ]);
        Permission::create([
            'name' => 'clinic-histories.edit-quantity-services-applied',
            'description' => 'Editar cantidades de servicios aplicados'
        ]);
        Permission::create([
            'name' => 'clinic-histories.delete-services-applied',
            'description' => 'Eliminar servicios aplicados'
        ]);

        //Finalidades de consulta
        Permission::create([
            'name' => 'consultation-purposes.index',
            'description' => 'Listar finalidades de consulta'
        ]);
        Permission::create([
            'name' => 'consultation-purposes.store',
            'description' => 'Registrar finalidades de consulta'
        ]);
        Permission::create([
            'name' => 'consultation-purposes.update',
            'description' => 'Editar finalidades de consulta'
        ]);
        Permission::create([
            'name' => 'consultation-purposes.destroy',
            'description' => 'Eliminar finalidades de consulta'
        ]);

        //Diagnósticos
        Permission::create([
            'name' => 'diagnoses.index',
            'description' => 'Listar diagnósticos'
        ]);
        Permission::create([
            'name' => 'diagnoses.store',
            'description' => 'Registrar diagnósticos'
        ]);
        Permission::create([
            'name' => 'diagnoses.update',
            'description' => 'Editar diagnósticos'
        ]);
        Permission::create([
            'name' => 'diagnoses.destroy',
            'description' => 'Eliminar diagnósticos'
        ]);

        //Productos
        Permission::create([
            'name' => 'products.index',
            'description' => 'Listar productos'
        ]);
        Permission::create([
            'name' => 'products.store',
            'description' => 'Registrar productos'
        ]);
        Permission::create([
            'name' => 'products.update',
            'description' => 'Editar productos'
        ]);
        Permission::create([
            'name' => 'products.destroy',
            'description' => 'Eliminar productos'
        ]);

        //Servicios
        Permission::create([
            'name' => 'services.index',
            'description' => 'Listar servicios'
        ]);
        Permission::create([
            'name' => 'services.store',
            'description' => 'Registrar servicios'
        ]);
        Permission::create([
            'name' => 'services.update',
            'description' => 'Editar servicios'
        ]);
        Permission::create([
            'name' => 'services.destroy',
            'description' => 'Eliminar servicios'
        ]);

        //Unidades
        Permission::create([
            'name' => 'unities.index',
            'description' => 'Listar unidades'
        ]);
        Permission::create([
            'name' => 'unities.store',
            'description' => 'Registrar unidades'
        ]);
        Permission::create([
            'name' => 'unities.update',
            'description' => 'Editar unidades'
        ]);
        Permission::create([
            'name' => 'unities.destroy',
            'description' => 'Eliminar unidades'
        ]);

        //Usuarios
        Permission::create([
            'name' => 'users.index',
            'description' => 'Listar usuarios'
        ]);
        Permission::create([
            'name' => 'users.store',
            'description' => 'Registrar usuarios'
        ]);
        Permission::create([
            'name' => 'users.update',
            'description' => 'Editar usuarios'
        ]);
        Permission::create([
            'name' => 'users.destroy',
            'description' => 'Eliminar usuarios'
        ]);
        Permission::create([
            'name' => 'users.permission',
            'description' => 'Editar permisos de usuario'
        ]);

        //Empresa
        Permission::create([
            'name' => 'company.update',
            'description' => 'Actualizar datos de empresa'
        ]);

        //Parámetros
        Permission::create([
            'name' => 'parameters.update',
            'description' => 'Actualizar parámetros del sistema'
        ]);

        //Tipos de archivos adjuntos
        Permission::create([
            'name' => 'attachment-types.index',
            'description' => 'Listar tipos de archivos adjuntos'
        ]);
        Permission::create([
            'name' => 'attachment-types.store',
            'description' => 'Registrar tipos de archivos adjuntos'
        ]);
        Permission::create([
            'name' => 'attachment-types.update',
            'description' => 'Editar tipos de archivos adjuntos'
        ]);
        Permission::create([
            'name' => 'attachment-types.destroy',
            'description' => 'Eliminar tipos de archivos adjuntos'
        ]);
        //Ver recetas
        Permission::create([
            'name' => 'clinic-histories.show-prescriptions',
            'description' => 'Consultar recetas médicas'
        ]);
        //Consultar informe de consultas
        Permission::create([
            'name' => 'reports.consultations',
            'description' => 'Consultar informe de atenciónes'
        ]);
        Permission::create([
            'name' => 'reports.services-provided',
            'description' => 'Consultar informe de servicios prestados'
        ]);
        Permission::create([
            'name' => 'informed-consent-templates.index',
            'description' => 'Listar plantillas de consentimiento informado'
        ]);
        Permission::create([
            'name' => 'informed-consent-templates.store',
            'description' => 'Registrar plantillas de consentimiento informado'
        ]);
        Permission::create([
            'name' => 'informed-consent-templates.update',
            'description' => 'Editar plantillas de consentimiento informado'
        ]);
        Permission::create([
            'name' => 'informed-consent-templates.destroy',
            'description' => 'Eliminar plantillas de consentimiento informado'
        ]);
        Permission::create([
            'name' => 'informed-consent-templates.customize',
            'description' => 'Personalizar plantillas de consentimiento informado'
        ]);
    }
}
