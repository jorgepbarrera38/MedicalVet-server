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
            <h4 style="padding:0;margin:0">INFORME DE PRODUCTOS Y SERVICIOS APLICADOS <br></h4>
        </center>

        <br>
        <table border="0" width="100%">
            <tr>
                <th>Propietario:</th>
                <td colspan="3">
                {{ $pet->owner->identification }} -
                    {{ $pet->owner->names . ' ' .  $pet->owner->surnames}}
                </td>
            </tr>
            <tr>
                <th>Mascota:</th>
                <td>{{ $pet->name }}</td>
                <th style="text-align:right">Especie:</th>
                <td>{{ $pet->species->name }}</td>
            </tr>
            <tr>
                <th>Raza:</th>
                <td>{{ $pet->race->name }}</td>
                <th style="text-align:right">Fecha de nacimiento:</th>
                <td>{{ $pet->date_born }}</td>
            </tr>
            <tr>
                <th>Sexo:</th>
                <td>{{ $pet->sex }}</td>
                <th style="text-align:right">Color:</th>
                <td>{{ $pet->color }}</td>
            </tr>
            <tr>  
                <th>Tamaño:</th>
                <td colspan="3">{{ $pet->size }}</td>
            </tr>
            <tr>
                <th>Observaciónes:</th>
                <td colspan="3" align="justify">{{ $pet->observations }}</td>
            </tr>
        </table>

        <!--Vacunas-->
        <table border="1" style="border-collapse:collapse;margin-top:20px" width="100%">
                <tr>
                    <td colspan="4" nowrap>
                        <center><strong>PRODUCTOS<strong></center>
                    </td>
                </tr>

                <tr>
                    <td nowrap><strong>Producto<strong></td>
                    <td nowrap><strong>Cantidad<strong></td>
                    <td nowrap><strong>Fecha de aplicación<strong></td>
                    <td nowrap><strong>Observaciónes<strong></td>
                </tr>
                @foreach($products as $product)
                <tr>
                    <td>{{ $product->product->name }}</td>
                    <td>{{ $product->quantity }}</td>
                    <td nowrap>{{ $product->application_date }}</td>
                    <td>{{ $product->observations }}</td>
                </tr>
                @endforeach
        </table>
        <!--Fin Vacunas-->
        <!--Vacunas-->
        <table border="1" style="border-collapse:collapse;margin-top:20px" width="100%">

                <tr>
                    <td colspan="4" nowrap>
                        <center><strong>SERVICIOS<strong></center>
                    </td>
                </tr>

                <tr>
                    <td nowrap><strong>Servicio<strong></td>
                    <td nowrap><strong>Cantidad<strong></td>
                    <td nowrap><strong>Fecha de aplicación<strong></td>
                    <td nowrap><strong>Observaciónes<strong></td>
                </tr>
            
                @foreach($services as $service)
                <tr>
                    <td>{{ $service->service->name }}</td>
                    <td>{{ $service->quantity }}</td>
                    <td nowrap>{{ $service->application_date }}</td>
                    <td>{{ $service->observations }}</td>
                </tr>
                @endforeach
            
        </table>
        <!--Fin Vacunas-->
</body>
</html>