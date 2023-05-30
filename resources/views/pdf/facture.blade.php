<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $facture->formatted_id }}</title>
    <style>
        /* Add any custom CSS styles for the PDF here */
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .facture-header {
            background-color: #eee;
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
            font-size: 15px;
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
            background-color: #eee;
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
            color: #FF0000;
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
<div class="vertical-text">Facture N°{{ $facture->formatted_id }}</div>
<div class="header">
    <div class="facture-header">
        <div class="company-info">
            <h2 style="font-size: 30px; font-family: Bold ; color: #FF0000;">EBUILD</h2>
            <p style="font-size: 17px; font-family: Bold; display: inline-block;">De:EBUILD</p>
            <p>SARL immatriculée au registre national des entreprises</p>
            <p>sous l’identifiant unique 1751386/T.</p>
            <p>Relevé d'identité bancaire (RIB): 00120 00770036879 </p>
            <p><strong>N° de téléphone:</strong>98157896</p>
        </div>
        <div class="client-info" >
            <h1 style="text-align: right;"><strong>Facture N° </strong><small>{{ $facture->formatted_id }}</small></h1>
            <h1 style="margin-bottom: 22px;margin-left: 200px;"> <strong>Date:  </strong><small>{{ $facture->created_at->format('d/m/Y ') }}</small></h1>
            <p style="font-size: 17px; font-family: Bold; display: inline-block;" >À: {{ $facture->client }}</p>
            <p><strong>Email:</strong> {{ $facture->client_email }}</p>
            <p><strong>N° de téléphone:</strong> {{ $phone_number }}</p>

        </div>
        <div class="clear"></div>
    </div>
</div>

<div class="header" >
    <img src="{{ asset('resources/images/logo.svg') }}" alt="Logo">


</div>

<table style="margin-left: 20px;">
    <thead>
    <tr>
        <th><strong>Nature</strong></th>
        <th><strong>Quantité</strong></th>
        <th><strong>Montant HT</strong></th>
        @if (!is_null($facture->operationfactures->first()->montant_ttc))
            <th><strong>Montant TTC</strong></th>
        @endif
    </tr>
    </thead>
    <tbody>
    @foreach ($facture->operationfactures as $operation)
        <tr>
            <td>{{ $operation->nature }}</td>
            <td>{{ $operation->quantité }}</td>
            <td>{{ $operation->montant_ht }}</td>
            @if (!is_null($operation->montant_ttc))
                <td>{{ $operation->montant_ttc }}</td>
            @endif
        </tr>
    @endforeach
    @if (!is_null($facture->note))
    <tr>
        <td colspan="{{ !is_null($facture->operationfactures->first()->montant_ttc) ? 4 : 3}}" >
            <strong>Note:</strong> {{ $facture->note }}</td>
    </tr>
    @endif
    </tbody>
</table>

<div class="totals">
    <table style="width: 220px; margin-left: 400px;">
        <tr>
            <th><strong>Total Montant HT:</strong></th>
            <td>{{ $facture->total_montant_ht }}</td>
        </tr>
        @if (!is_null($facture->operationfactures->first()->montant_ttc))
            <tr>
                <th><strong>Taux TVA:</strong></th>
                <td>{{ $operation->taux_tva }}%</td>
            </tr>
        @endif
        <tr>
            <th><strong>Timbre:</strong></th>
            <td>1.00</td>
        </tr>
        @if (!is_null($facture->total_montant_ttc))
            <tr>
                <th><strong>Total Montant TTC:</strong></th>
                <td>{{ $facture->total_montant_ttc }}</td>
            </tr>
        @endif
    </table>

    <div style="margin-left: 50px;">
        <p><strong>Arrêter La Présente Facture A La Somme De:</strong></p>
        <p>{{ $facture->total_montant_letters }}</p>
    </div>
</div>
</body>
</html>
