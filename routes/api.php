<?php

Route::middleware('auth')->group(function () {

    Route::get('dashboard/get-data', 'DashboardController@getData');
    Route::get('owners/get-cities', 'OwnerController@getCities');
    Route::get('owners/{owner}/get-pets-from-owner', 'OwnerController@getPetsFromOwner');
    Route::apiResource('owners', 'OwnerController');
    Route::apiResource('species', 'SpeciesController');
    Route::apiResource('races', 'RaceController');
    Route::apiResource('states', 'StateController');
    Route::get('cities/get-states', 'CityController@getStates');
    Route::apiResource('cities', 'CityController');
    Route::apiResource('unities', 'UnityController');
    
    Route::get('pets/get-quantity-pets-created', 'PetController@getQuantityPetsCreated');
    Route::get('pets/get-data-to-create-pet', 'PetController@getDataToCreatePet');
    Route::apiResource('pets', 'PetController');
    
    Route::apiResource('diagnoses', 'DiagnosisController');
    Route::apiResource('products', 'ProductController');
    Route::apiResource('services', 'ServiceController');
    Route::apiResource('consultation-purposes', 'ConsultationPurposesController');
    
    Route::get('clinic-histories/get-owner', 'ClinicHistoryController@getOwner');
    Route::get('clinic-histories/{pet}/get-appointments-from-pet', 'ClinicHistoryController@getAppointmentsFromPet');
    Route::put('clinic-histories/{clinicHistoryProduct}/update-product-application', 'ClinicHistoryController@updateProductApplication');
    Route::delete('clinic-histories/{clinicHistoryProduct}/delete-product-application', 'ClinicHistoryController@deleteProductApplication');
    Route::put('clinic-histories/{clinicHistoryService}/update-service-application', 'ClinicHistoryController@updateServiceApplication');
    Route::delete('clinic-histories/{clinicHistoryService}/delete-service-application', 'ClinicHistoryController@deleteServiceApplication');
    Route::get('clinic-histories/{owner}/get-pet-from-owner', 'ClinicHistoryController@getPetFromOwner');
    Route::get('clinic-histories/get-owners', 'ClinicHistoryController@getOwners');
    Route::get('clinic-histories/get-consultation-purposes-and-attachment-types', 'ClinicHistoryController@getConsultationPurposesAndAttachmentTypes');
    Route::get('clinic-histories/get-data-for-create-folio/{pet}', 'ClinicHistoryController@getDataForCreateFolio');
    Route::get('reports/clinic-history-folios/{payload}', 'ReportController@clinicHistoryFolios');
    Route::apiResource('clinic-histories', 'ClinicHistoryController')->only('store');
    
    Route::get('reports/prescriptions/{pet}/get-prescriptions-from-pet', 'ReportController@getPrescriptionsFromPet');
    Route::get('reports/prescriptions/{prescription}/get-prescription-from-pet', 'ReportController@getPrescriptionFromPet');
    Route::get('reports/prescriptions/{prescription}/export-prescription-in-pdf', 'ReportController@exportPrescriptionInPdf');
    Route::get('reports/prescriptions/{prescription}/send-prescription-from-pet-for-email', 'ReportController@sendPrescriptionFromPetForEmail');
    Route::get('reports/vaccine/{pet}', 'ReportController@getVaccine');
    Route::get('reports/vaccine/{pet}/export-pdf', 'ReportController@getVaccineInPdf');
    Route::get('reports/vaccine/{pet}/send-vaccines-and-dewormings-for-email', 'ReportController@sendVaccinesAndDewormingsForEmail');
    Route::get('reports/products-and-services-applied/{pet}', 'ReportController@productsAndServicesApplied');
    Route::get('reports/products-and-services-applied/{pet}/export-pdf', 'ReportController@getProductsAndServicesAppliedInPdf');
    Route::get('reports/clinic-history/{pet}', 'ReportController@getClinicHistory');
    
    Route::get('clinic-histories/{pet}/send-for-email', 'ClinicHistoryController@sendClinicHistoryForEmail');

    Route::get('users/get-quantity-users-created', 'UserController@getQuantityUsersCreated');
    Route::put('users/{user}/update-permissions', 'UserController@updatePermissions');    
    Route::get('users/{user}/get-permissions', 'UserController@getPermissions');    
    Route::get('company', 'CompanyController@index');    
    Route::put('company', 'CompanyController@update');
    Route::get('company/download-backup-files', 'CompanyController@downloadBackupFiles');
    Route::get('parameters', 'ParameterController@index');
    Route::put('parameters', 'ParameterController@update');
    
    Route::apiResource('users', 'UserController');

    Route::get('consultingrooms/get-quantity-consultingrooms-created', 'ConsultingroomController@getQuantityConsultingroomsCreated');
    Route::apiResource('consultingrooms', 'ConsultingroomController');

    Route::get('activities/get-services', 'ActivityController@getServices');
    Route::apiResource('activities', 'ActivityController');

    Route::apiResource('reason-cancellations', 'ReasonCancellationController');

    Route::get('appointments', 'AppointmentController@index');
    Route::post('appointments', 'AppointmentController@store');
    Route::get('appointments/reports/get-appointments', 'AppointmentController@reportGetAppointments');
    Route::get('appointments/reports/get-appointments-to-pdf', 'AppointmentController@reportGetAppointmentsToPdf');
    Route::get('appointments/{pet}/get-appointments', 'AppointmentController@getAppointments');
    Route::put('appointments/{appointment}/fulfill-appointment', 'AppointmentController@fulfillAppointment');
    Route::put('appointments/{appointment}/unfulfill-appointment', 'AppointmentController@unFulfillAppointment');
    Route::put('appointments/{appointment}/cancel-appointment', 'AppointmentController@cancelAppointment');
    Route::get('appointments/get-data-to-create-appointment', 'AppointmentController@getDataToCreateAppointment');
    Route::get('appointments/get-reason-cancellations', 'AppointmentController@getReasonCancellations');

    Route::get('appointments/get-all-appointments', 'AppointmentBoardController@getAllAppointments');
    
    Route::apiResource('attachment-types', 'AttachmentTypeController');

    Route::get('reports/get-consultations', 'ReportController@getConsultations');
    Route::get('reports/export-consultations-in-excel', 'ReportController@exportConsultationsInExcel');
    
    Route::get('reports/get-services-provided', 'ReportController@getServicesProvided');
    Route::get('reports/export-services-in-excel', 'ReportController@exportServicesProvidedInExcel');

    Route::post('informed-consent-templates/{informedConsentTemplate}/customize', 'InformedConsentTemplateController@customize');
    Route::apiResource('informed-consent-templates', 'InformedConsentTemplateController');
    
    Route::get('informed-consents/get-informed-consents-templates', 'InformedConsentController@getInformedConsentTemplates');
    Route::get('informed-consents/{pet}/{informedConsentTemplate}', 'InformedConsentController@getInformedConsent');

    //Registrar y eliminar vacunas por fuera de historia clínica
    Route::get('vaccines/{pet}/get-quantity-vaccines-created', 'VaccineController@getQuantityVaccinesCreated');
    Route::get('vaccines', 'VaccineController@index');    
    Route::post('vaccines', 'VaccineController@store');
    Route::put('vaccines/{vaccine}', 'VaccineController@update');
    Route::delete('vaccines/{vaccine}', 'VaccineController@destroy');

    //Registrar y eliminar desparasitaciónes por fuera de historia clínica
    Route::get('dewormings/{pet}/get-quantity-dewormings-created', 'DewormingController@getQuantityDewormingsCreated');
    Route::get('dewormings', 'DewormingController@index');    
    Route::post('dewormings', 'DewormingController@store');
    Route::put('dewormings/{deworming}', 'DewormingController@update');
    Route::delete('dewormings/{deworming}', 'DewormingController@destroy');

    //Facturación
    Route::get('billings/{pet}/get-pending-billing', 'BillingController@getPendingBilling');
    Route::post('billings/{pet}/bill', 'BillingController@bill');
});

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');

});
