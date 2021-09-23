<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        .page-break {
            page-break-after: always;
        }
        .header {
            vertical-align: top;
            text-align: right;
        }
        th {
            vertical-align: top;
            text-align: left;
        }
        * {
            font-family: sans-serif;
        }
    </style>
</head>
<body>
    <table border="0" width="100%">
        <tr>
            <td>
                @if($company && $company->logo)
                    <img src="{{ $company->logo }}" alt="" width="140px">
                @endif
            </td>
            <td class="header">
                @if($company && $company->slogan)
                    {{ $company->slogan }} <br>
                @endif
                @if($company && $company->nit)
                    NIT - {{ $company->nit }}
                @endif
            </td>
        </tr>
    </table>
    <center>
        @if($company && $company->name)
            <h3 style="padding-bottom:0;margin-bottom:0">{{ $company->name }}</h3>
        @endif
        <h4 style="padding:0;margin:0">REPORTE DE CITAS</h4>
    </center>
        <p style="font-size:12px">
        Desde <strong>{{ request()->from }}</strong> hasta <strong>{{ request()->until }}</strong> |
        {{ request()->pending == 'true' ? '- Pendientes' : '' }}
        {{ request()->fulfilled == 'true' ? '- Cumplidas' : '' }}
        {{ request()->unFulfilled == 'true' ? '- Incumplidas' : '' }}
        {{ request()->cancelled == 'true' ? '- Canceladas' : '' }}
        </p>
    <br>
    <table border="1" width="100%" style="font-size:10px;border-collapse: collapse">
        <tr>
            <th>Fecha cita</th>
            <th>Propietario</th>
            <th>Mascota</th>
            <th>Consultorio</th>
            <th>Actividad</th>
            <th>Duración</th>
            <th>Estado</th>
            <th>Usuario creó</th>
            <th>Usuario que cumplió</th>
            <th>Folio que cumplió</th>
            <th>Usuario incumplió</th>
            <th>Usuario canceló</th>
            <th>Motivo de cancelación</th>
            <th>Observaciónes</th>
        </tr>
        <tbody>
            @foreach($appointments as $appointment)
                <tr>
                    <td>{{ $appointment->date_time_start }}</td>
                    <td>{{ $appointment->pet->owner->identification . ' - ' . $appointment->pet->owner->names . ' ' . $appointment->pet->owner->surnames }}</td>
                    <td>{{ $appointment->pet->name }}</td>
                    <td>{{ $appointment->consultingroom->name }}</td>
                    <td>{{ $appointment->activity->name }}</td>
                    <td>{{ $appointment->duration }}</td>
                    <td>{{ $appointment->state }}</td>                    
                    <td>
                    {{ 
                        $appointment->userThatCreates->identification . ' - ' .
                        $appointment->userThatCreates->names . ' ' .
                        $appointment->userThatCreates->surnames
                    
                    }}
                    </td>
                    <td>
                        @if ($appointment->state == 'CUMPLIDA')
                            {{ 
                                $appointment->userThatFulFill->identification . ' - ' .
                                $appointment->userThatFulFill->names . ' ' .
                                $appointment->userThatFulFill->surnames
                            
                            }}
                        @endif
                    </td>
                    <td>
                        @if ($appointment->clinic_history_id)
                            {{ $appointment->clinicHistory->number_folio }}
                        @endif
                    </td>
                    <td>
                        @if ($appointment->state == 'INCUMPLIDA')
                            {{ 
                                $appointment->userThatUnfulfill->identification . ' - ' .
                                $appointment->userThatUnfulfill->names . ' ' .
                                $appointment->userThatUnfulfill->surnames
                            
                            }}
                        @endif
                    </td>
                    <td>
                        @if ($appointment->state == 'CANCELADA')
                            {{ 
                                $appointment->userThatCancel->identification . ' - ' .
                                $appointment->userThatCancel->names . ' ' .
                                $appointment->userThatCancel->surnames
                            
                            }}
                        @endif
                    </td>
                    <td>
                        @if ($appointment->state == 'CANCELADA')
                            {{ $appointment->reasonCancellation->name }}
                        @endif
                    </td>
                    <td>{{ $appointment->observations }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>