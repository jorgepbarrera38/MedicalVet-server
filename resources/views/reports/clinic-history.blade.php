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
    @foreach($clinicHistoriesFromPet->clinicHistories as $clinicHistory)
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
            <h4 style="padding:0;margin:0">HISTORIA CLÍNICA VETERINARIA <br> {{ $clinicHistory->consultationPurposes->name }}</h4>
        </center>
        <br>
        <table border="0">
            <tr>
                <th>Folio:</th>
                <td>{{ $clinicHistory->number_folio }}</td>
            </tr>
            <tr>
                <th>Fecha del folio:</th>
                <td nowrap>{{ $clinicHistory->created_at }}</td>
            </tr>
        </table>
        <br>
        <table border="0" width="100%">
            <tr>
                <th>Propietario:</th>
                <td colspan="3">
                {{ $clinicHistoriesFromPet->owner->identification }} -
                    {{ $clinicHistoriesFromPet->owner->names . ' ' .  $clinicHistoriesFromPet->owner->surnames}}
                </td>
            </tr>
            <tr>
                <th>Mascota:</th>
                <td>{{ $clinicHistoriesFromPet->name }}</td>
                <th style="text-align:right">Especie:</th>
                <td>{{ $clinicHistoriesFromPet->species->name }}</td>
            </tr>
            <tr>
                <th>Raza:</th>
                <td>{{ $clinicHistoriesFromPet->race->name }}</td>
                <th style="text-align:right">Edad:</th>
                <td>
                    {{ str_replace("antes", "", Carbon\Carbon::createFromDate($clinicHistoriesFromPet->date_born)->diffForHumans($clinicHistory->created_at)) }}
                </td>
            </tr>
            <tr>
                <th>Sexo:</th>
                <td>{{ $clinicHistoriesFromPet->sex }}</td>
                <th style="text-align:right">Color:</th>
                <td>{{ $clinicHistoriesFromPet->color }}</td>
            </tr>
            <tr>  
                <th>Tamaño:</th>
                <td colspan="3">{{ $clinicHistoriesFromPet->size }}</td>
            </tr>
            <tr>
                <th>Observaciónes:</th>
                <td colspan="3" align="justify">{{ $clinicHistoriesFromPet->observations }}</td>
            </tr>
        </table>

        <hr>

        <center><h4>MOTIVO</h4></center>
        <div align="justify">{{ $clinicHistory->reason_consultation }}</div>

        <!--Síntomas-->
        @if($clinicHistory->symptoms)
            <div>
                <center><h4>SÍNTOMAS</h4></center>
                <div align="justify">{{ $clinicHistory->symptoms }}</div>
            </div>
        @endif
        <!--Fin síntomas-->
        
        <!--Anamnesis-->
        @if($clinicHistory->anamnesis)
            <div>
                <center><h4>ANAMNESIS</h4></center>
                <div align="justify">{{ $clinicHistory->anamnesis }}</div>
            </div>
        @endif
        <!--Fin Anamnesis-->

        <!--Revisión por sistemas-->
        @if($clinicHistory->clinicHistoryReviewBySystem)
            <div>
                <center><h4>REVISIÓN POR SISTEMAS</h4></center>
                <div align="justify">
                    <strong>Sistema respiratorio</strong> <br><br>
                    {{ $clinicHistory->clinicHistoryReviewBySystem->respiratory_system_state }} <br><br>
                    @if ($clinicHistory->clinicHistoryReviewBySystem->respiratory_system_observations)
                        {{ $clinicHistory->clinicHistoryReviewBySystem->respiratory_system_observations }}<br><br>
                    @endif
                </div>
                <div align="justify">
                    <strong>Sistema tegumentario</strong> <br><br>
                    {{ $clinicHistory->clinicHistoryReviewBySystem->tegumentary_system_state }} <br><br>
                    @if ($clinicHistory->clinicHistoryReviewBySystem->tegumentary_system_observations)
                        {{ $clinicHistory->clinicHistoryReviewBySystem->tegumentary_system_observations }}<br><br>
                    @endif
                </div>
                <div align="justify">
                    <strong>Sistema nervioso</strong> <br><br>
                    {{ $clinicHistory->clinicHistoryReviewBySystem->nervous_system_state }} <br><br>
                    @if ($clinicHistory->clinicHistoryReviewBySystem->nervous_system_observations)
                        {{ $clinicHistory->clinicHistoryReviewBySystem->nervous_system_observations }}<br><br>
                    @endif
                </div>
                <div align="justify">
                    <strong>Sistema genitourinario</strong> <br><br>
                    {{ $clinicHistory->clinicHistoryReviewBySystem->genitourinary_system_state }} <br><br>
                    @if ($clinicHistory->clinicHistoryReviewBySystem->genitourinary_system_observations)
                        {{ $clinicHistory->clinicHistoryReviewBySystem->genitourinary_system_observations }}<br><br>                        
                    @endif
                </div>
                <div align="justify">
                    <strong>Ganglios linfáticos</strong> <br><br>
                    {{ $clinicHistory->clinicHistoryReviewBySystem->lymph_nodes_state }} <br><br>
                    @if ($clinicHistory->clinicHistoryReviewBySystem->lymph_nodes_observations)
                        {{ $clinicHistory->clinicHistoryReviewBySystem->lymph_nodes_observations }}<br><br>
                    @endif
                </div>
                <div align="justify">
                    <strong>Sistema musculoesquelético</strong> <br><br>
                    {{ $clinicHistory->clinicHistoryReviewBySystem->musculoskeletal_system_state }} <br><br>
                    @if ($clinicHistory->clinicHistoryReviewBySystem->musculoskeletal_system_observations)
                        {{ $clinicHistory->clinicHistoryReviewBySystem->musculoskeletal_system_observations }}<br><br>
                    @endif
                </div>
                <div align="justify">
                    <strong>Sistema reproductivo</strong> <br><br>
                    {{ $clinicHistory->clinicHistoryReviewBySystem->reproductive_system_state }} <br><br>
                    @if ($clinicHistory->clinicHistoryReviewBySystem->reproductive_system_observations)
                        {{ $clinicHistory->clinicHistoryReviewBySystem->reproductive_system_observations }}<br><br>
                    @endif
                </div>
                <div align="justify">
                    <strong>Cavidad oral</strong> <br><br>
                    {{ $clinicHistory->clinicHistoryReviewBySystem->oral_cavity_state }} <br><br>
                    @if ($clinicHistory->clinicHistoryReviewBySystem->oral_cavity_observations)
                        {{ $clinicHistory->clinicHistoryReviewBySystem->oral_cavity_observations }}<br><br>
                    @endif
                </div>
            </div>
        @endif
        <!--Fin Revisión por sistemas-->

        <!--Antecedentes-->
        @if(count($clinicHistory->records))
            <center><h4>ANTECEDENTES</h4></center>
            @foreach($clinicHistory->records as $record)
                <li>{{ $record->description }}</li>
            @endforeach
        @endif
        <!--Fin Antecedentes-->

        <!--Constantes fisiológicas-->
        @if($clinicHistory->physiologicalConstants)
            <div>
                <center><h4>CONSTANTES FISIOLÓGICAS</h4></center>
                <table>
                    <tr>
                        <th>Peso:</th>
                        <td>
                        {{ $clinicHistory->physiologicalConstants->weight }}KG
                        </td>
                    </tr>
                    <tr>
                        <th>Temperatura:</th>
                        <td>
                        {{ $clinicHistory->physiologicalConstants->temperature }}°C
                        </td>
                    </tr>
                    <tr>
                        <th>Frecuencia cardíaca:</th>
                        <td>
                        {{ $clinicHistory->physiologicalConstants->heart_rate }}
                        </td>
                    </tr>
                    <tr>
                        <th>Frecuencia respiratoria:</th>
                        <td>
                        {{ $clinicHistory->physiologicalConstants->breathing_frequency }}
                        </td>
                    </tr>
                </table>
            </div>
        @endif
        <!--Fin Constantes fisiológicas-->

        <!--Diagnósticos-->
        @if(count($clinicHistory->diagnoses))
            <center><h4>DIAGNÓSTICOS</h4></center>
            @foreach($clinicHistory->diagnoses as $diagnosis)
                <li>{{ $diagnosis->diagnosis->name . ' - '  . $diagnosis->type }}</li>
            @endforeach
        @endif
        <!--Fin Diagnósticos-->

        <!--Vacunas-->
        @if(count($clinicHistory->vaccines))
            <center><h4>VACUNACIÓN</h4></center>
            <table border="1" style="border-collapse:collapse" width="100%">
                <thead>
                    <tr>
                    <th nowrap>Vacuna</th>
                    <th nowrap>Fecha siguiente vacuna</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clinicHistory->vaccines as $vaccine)
                    <tr>
                        <td>{{ $vaccine->product->name }}</td>
                        <td nowrap>{{ $vaccine->next_date }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        <!--Fin Vacunas-->

        <!--Desparasitación-->
        @if(count($clinicHistory->dewormings))
            <center><h4>DESPARASITACIÓN</h4></center>
            <table border="1" style="border-collapse:collapse" width="100%">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Dosis</th>
                        <th>Unidad</th>
                        <th nowrap>Fecha siguiente desparasitación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clinicHistory->dewormings as $deworming)
                    <tr>
                        <td>{{ $deworming->product->name }}</td>
                        <td nowrap>{{ $deworming->dose }}</td>
                        <td nowrap>{{ $deworming->unity->description }}</td>
                        <td nowrap>{{ $deworming->next_date }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>           
        @endif
        <!--Fin Desparasitación-->

        <!--Productos y servicios aplicados-->
        @if(count($clinicHistory->products))
            <center><h4>PRODUCTOS APLICADOS</h4></center>
            <table border="1" style="border-collapse:collapse" width="100%">
                <thead>
                    <tr>
                    <th nowrap>Producto</th>
                    <th nowrap>Cantidad aplicada</th>
                    <th nowrap>Unidad</th>
                    <th nowrap>Observaciónes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clinicHistory->products as $product)
                    <tr>
                        <td>{{ $product->product->name }}</td>
                        <td>{{ $product->quantity }}</td>
                        <td>{{ $product->unity ? $product->unity->description : '' }}</td>
                        <td align="justify">{{ $product->observations }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if(count($clinicHistory->services))
            <center><h4>SERVICIOS APLICADOS</h4></center>
            <table border="1" style="border-collapse: collapse" width="100%">
                <thead>
                    <tr>
                    <th nowrap>Servicio</th>
                    <th nowrap>Cantidad</th>
                    <th nowrap>Observaciónes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clinicHistory->services as $service)
                    <tr>
                        <td>{{ $service->service->name }}</td>
                        <td>{{ $service->quantity }}</td>
                        <td align="justify">
                            {{ $service->observations }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        <!--Fin Productos y servicios aplicados-->

        <!--Inicio receta veterinaria -->
        @if($clinicHistory->clinicHistoryPrescription)
            <center><h4>RECETA VETERINARIA</h4></center>
            @if (count($clinicHistory->clinicHistoryPrescription->clinicHistoryPrescriptionDetails))
                <table border="1" style="border-collapse: collapse" width="100%">
                    <thead>
                        <tr>
                        <th nowrap>Producto</th>
                        <th nowrap>Cantidad</th>
                        <th nowrap>Posología</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clinicHistory->clinicHistoryPrescription->clinicHistoryPrescriptionDetails as $product)
                        <tr>
                            <td>{{ $product->product->name }}</td>
                            <td>{{ $product->quantity }}</td>
                            <td>{{ $product->posology }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @if ($clinicHistory->clinicHistoryPrescription->observations)
                <div align="justify">
                    <strong>Observaciónes: </strong>{{ $clinicHistory->clinicHistoryPrescription->observations }}
                </div>
            @endif
        @endif
        <!--Fin receta veterinaria -->

        <!--Indicaciones-->
        @if($clinicHistory->indications)
                <center><h4>INDICACIÓNES</h4></center>
            
                <div align="justify">{{ $clinicHistory->indications }}</div>
            
        @endif
        <!--Fin indicaciones-->
        <br>
        <br>
        <br>
        <br>
        <div>
            @if ($clinicHistory->user->signature)
                <img src="{{ $clinicHistory->user->signature }}" width="160" alt=""><br>
            @endif
            {{ $clinicHistory->user->identification . ' - ' . $clinicHistory->user->names . ' ' . $clinicHistory->user->surnames }} <br>
            @if ($clinicHistory->user->professional_register)
                <strong>Matrícula profesional:</strong> {{ $clinicHistory->user->professional_register }}
            @endif
        </div>

        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>