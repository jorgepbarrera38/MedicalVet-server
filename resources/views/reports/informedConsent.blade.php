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
        </center>

        {!! $template !!}
</body>
</html>