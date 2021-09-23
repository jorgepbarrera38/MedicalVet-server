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
                        {{ $company->slogan }}<br>
                    @endif
                    @if($company && $company->nit)
                        NIT - {{ $company->nit }}<br>
                    @endif
                    @if($company && $company->address)
                        {{ $company->address }}
                    @endif
                </td>
            </tr>
        </table>
        <center>
            @if($company && $company->name)
                <h3 style="padding-bottom:0;margin-bottom:0">{{ $company->name }}</h3>
            @endif
            <h4 style="padding:0;margin:0">RECETA VETERINARIA <br></h4>
        </center>

        <br>
        <table border="0" width="100%">
            <tr>
                <th>Propietario:</th>
                <td colspan="3">
                {{ $prescription->clinicHistory->pet->owner->identification }} -
                    {{ $prescription->clinicHistory->pet->owner->names . ' ' .  $prescription->clinicHistory->pet->owner->surnames}}
                </td>
            </tr>
            <tr>
                <th>Mascota:</th>
                <td>{{ $prescription->clinicHistory->pet->name }}</td>
                <th style="text-align:right">Especie:</th>
                <td>{{ $prescription->clinicHistory->pet->species->name }}</td>
            </tr>
            <tr>
                <th>Raza:</th>
                <td>{{ $prescription->clinicHistory->pet->race->name }}</td>
                <th style="text-align:right">Fecha de nacimiento:</th>
                <td>{{ $prescription->clinicHistory->pet->date_born }}</td>
            </tr>
            <tr>
                <th>Sexo:</th>
                <td>{{ $prescription->clinicHistory->pet->sex }}</td>
                <th style="text-align:right">Color:</th>
                <td>{{ $prescription->clinicHistory->pet->color }}</td>
            </tr>
            <tr>  
                <th>Tamaño:</th>
                <td colspan="3">{{ $prescription->clinicHistory->pet->size }}</td>
            </tr>
            <tr>
                <th>Observaciónes:</th>
                <td colspan="3" align="justify">{{ $prescription->clinicHistory->pet->observations }}</td>
            </tr>
        </table>
        

        <!--Productos-->
        @if(count($prescription->clinicHistoryPrescriptionDetails))
            <table border="1" style="border-collapse:collapse;margin-top:20px" width="100%">
                    <tr>
                        <td nowrap><strong>Producto<strong></td>
                        <td nowrap><strong>Cantidad<strong></td>
                        <td nowrap><strong>Posología<strong></td>
                    </tr>
                    @foreach($prescription->clinicHistoryPrescriptionDetails as $prescriptionDetail)
                    <tr>
                        <td>{{ $prescriptionDetail->product->name }}</td>
                        <td>{{ $prescriptionDetail->quantity }}</td>
                        <td>{{ $prescriptionDetail->posology }}</td>
                    </tr>
                    @endforeach
            </table>
        @endif
        <!--Fin Productos-->
        @if ($prescription->observations)
            <p class="text-justify">
                <strong>Observaciónes: </strong>{{ $prescription->observations }}
            </p>
        @endif

        <div style="margin-top:60px">
            @if ($prescription->clinicHistory->user->signature)
                <img src="{{ $prescription->clinicHistory->user->signature }}" width="160" alt="">
            @endif
            <p>{{ $prescription->clinicHistory->user->identification . ' - ' . $prescription->clinicHistory->user->names . ' ' . $prescription->clinicHistory->user->surnames }}</p>
        </div>
</body>
</html>