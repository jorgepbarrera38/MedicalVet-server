<table>
    <thead>
    <tr>
        <th>Fecha</th>
        <th>Servicio</th>
        <th>Cantidad</th>
        <th>Propietario</th>
        <th>Mascota</th>
        <th>Folio</th>
        <th>Observaciónes</th>
        <th>Finalidad de la consulta</th>
        <th>Usuario</th>
    </tr>
    </thead>
    <tbody>
        @foreach ($services as $service)
            <tr>
                <td>{{ $service->application_date }}</td>
                <td>{{ $service->service->name }}</td>
                <td>{{ $service->quantity }}</td>
                <td>{{ $service->clinicHistory->pet->owner->identification . ' - ' . $service->clinicHistory->pet->owner->names . ' - ' . $service->clinicHistory->pet->owner->surnames }}</td>
                <td>{{ $service->clinicHistory->pet->name }}</td>
                <td>{{ $service->clinicHistory->number_folio }}</td>
                <td>{{ $service->observations }}</td>
                <td>{{ $service->clinicHistory->consultationPurposes->name }}</td>
                <td>{{ $service->clinicHistory->user->identification . ' - ' . $service->clinicHistory->user->names . ' - ' . $service->clinicHistory->user->surnames }}</td>
            </tr>
        @endforeach
    </tbody>
</table>