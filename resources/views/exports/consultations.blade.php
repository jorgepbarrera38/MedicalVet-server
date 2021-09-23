<table>
    <thead>
    <tr>
        <th>Fecha</th>
        <th>Finalidad de consulta</th>
        <th>Usuario</th>
        <th>Propietario</th>
        <th>Mascota</th>
        <th>Folio</th>
        <th>Motivo</th>
    </tr>
    </thead>
    <tbody>
        @foreach ($clinicHistories as $clinicHistory)
            <tr>
                <td>{{ $clinicHistory->created_at }}</td>
                <td>{{ $clinicHistory->consultationPurposes->name }}</td>
                <td>{{ $clinicHistory->user->identification . ' - ' . $clinicHistory->user->names . ' - ' . $clinicHistory->user->surnames }}</td>
                <td>{{ $clinicHistory->pet->owner->identification . ' - ' . $clinicHistory->pet->owner->names . ' - ' . $clinicHistory->pet->owner->surnames }}</td>
                <td>{{ $clinicHistory->pet->name }}</td>
                <td>{{ $clinicHistory->number_folio }}</td>
                <td>{{ $clinicHistory->reason_consultation }}</td>
            </tr>
        @endforeach    
    </tbody>
</table>