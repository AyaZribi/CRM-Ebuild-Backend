<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $devis->formatted_id }}</title>
    <style>
        /* Add any custom CSS styles for the PDF here */
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .facture-header {
           // background-color: #eee;
            padding: 20px;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .header img {
            width: 100px;
            height: 100px;
        }
        .header h1 {
            font-size: 13px;
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid black;
            padding: 5px;
        }
        table th {
            background-color: #f2e3ea;
            font-weight: bold;
        }

        .totals {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            margin-top: 20px;
        }
        .totals p {
            margin: 0;
            margin-left: 20px;
            font-size: 14px;
        }
        .vertical-text {
            position: absolute;
            left: 0;
            top: 50%;
            transform: rotate(-90deg);
            transform-origin: 0 0;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-left: -30px;
            height: 100%;
            color: #a22b41;
            font-size: 23px;
            width: 1000px;
            font-family: Bold/* Adjust the width to fit your text */
        }
        .company-info {
            float: left;
            width: 50%;
        }
        .client-info {
            float: right;
            width: 50%;
            text-align: right;
        }
        .clear {
            clear: both;
        }

    </style>
</head>
<body>
<div class="vertical-text"><strong style="color: #000000;">DEVIS </strong>N°{{ $devis->formatted_id }}</div>
<div class="header">
    <div class="facture-header">
        <div class="company-info" >
            <h2 style="font-size: 30px; font-family: Bold,serif ; color: #a22b41;">EBUILD</h2>
            <p><strong>MF: EBUILD, SARL immatriculée au registre national </strong></p>
            <p><strong>des entreprises sous l’identiant unique 1751386/T .</strong></p>

            <p><strong>N° de téléphone:</strong>98157896</p>



            <div style="margin-right: 140px;">

                <p style="font-size: 20px; font-family: Bold,serif; display: inline-block;color: #a22b41;">De</p>
                <hr style="border: 1px solid #a22b41;">
                <p style="font-size: 16px;background-color: #eee;" > EBUILD</p>
                <p><strong>Matricule Fiscal:</strong></p>
                <p>EBUILD, SARL immatriculée au</p>
                <p>registre national des entreprises </p>
                <p>sous l’identiant unique 1751386/T.</p>
            </div>
        </div>
        <div class="client-info">
            <h1 style="margin-bottom: 135px;margin-left: -80px;"></h1>
            <h1 style="text-align: right;"></h1>
            <h1 style="margin-left: -80px;margin-bottom:-550px;"> </h1>

            <div style="margin-right: 230px;text-align: left;margin-left: -100px;">
                <p style="font-size: 20px; font-family: Bold,serif; display: inline-block;color: #a22b41;">À</p>
                <hr style="border: 1px solid #a22b41;">
                <p style="font-size: 16px;background-color: #eee;"> {{ $devis->client }}</p>
            <p><strong>Email:</strong> {{ $devis->client_email }}</p>
            <p><strong>N° de téléphone:</strong> {{ $phone_number }}</p>
            </div>
        </div>
        <div>
            <h1 style="text-align: right;"><strong>Numéro </strong><small>{{ $devis->formatted_id }}</small></h1>
            <h1 style="margin-right: 0px;text-align: right;"><strong>Date </strong><small>{{ $devis->created_at->format('d/m/Y ') }}</small></h1>
            <h1 style="margin-top: 0px"></h1>
        </div>
        <div class="clear"></div>
    </div>
</div>




<table style="margin-left: 20px;">
    <thead>
    <tr>
        <th>Nature de l'opération</th>
        <th>Montant HT</th>
        <th>Taux de TVA</th>
        @if (!is_null($devis->operations->first()->montant_ttc))

        <th>Montant TTC</th>
        @endif
    </tr>
    </thead>
    <tbody>
    @foreach($devis->operations as $operation)
        <tr>
            <td>{{ $operation->nature }}</td>
            <td>{{ $operation->montant_ht }}</td>
            <td>{{ $operation->taux_tva }}</td>
            @if (!is_null($operation->montant_ttc))
            <td>{{ $operation->montant_ttc }}</td>
            @endif
        </tr>
    @endforeach
    @if (!is_null($devis->note))

        <tr>
        <td colspan="{{ !is_null($devis->operations->first()->montant_ttc) ? 4 : 3}}" >
            <strong>Note:</strong> {{ $devis->note }}</td>
    </tr>
    @endif
    </tbody>
</table>
</body>
</html>

