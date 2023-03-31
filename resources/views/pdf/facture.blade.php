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
            font-size: 18px;
            font-weight: bold;
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
            background-color: #FF0000;
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
            color: #000080;
            font-size: 20px;
            width: 1000px; /* Adjust the width to fit your text */
        }
    </style>
</head>
<body>
<!-- Add company info here -->
<div style=" margin-bottom: 5px;">
    <h2 style="font-size: 30px; font-family: Bold ; color: #000080;">E BUILD</h2>
    <p>SARL immatriculée au registre national des entreprises</p>
    <p>sous l’identifiant unique 1751386/T.</p>
    <p>Relevé d'identité bancaire (RIB): 00120 00770036879 </p>
    <p>N° de téléphone:98157896</p>
</div>
<div class="header">
    <img src="{{ asset('resources/images/logo.svg') }}" alt="Logo">


    <h1>{{ $facture->formatted_id }}</h1>
</div>

<div class="vertical-text">{{ $facture->formatted_id }}</div>
<table style="margin-left: 20px;">
    <thead>
    <tr>
        <th>Client</th>
        <th>Email du Client</th>
        <th>N° de téléphone</th>
        <th>RNE</th>
        <th>Date de création</th>
        <th>Nombre d'opérations</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>{{ $facture->client }}</td>
        <td>{{ $facture->client_email }}</td>
        <td>{{ $phone_number }}</td>
        <td>{{ $RNE }}</td>
        <td>{{ $facture->created_at->format('d/m/Y H:i:s') }}</td>
        <td>{{ $facture->nombre_operations }}</td>
    </tr>
    </tbody>
</table>

<table style="margin-left: 20px;">
    <thead>
    <tr>
        <th>Nature</th>
        <th>Quantité</th>
        <th>Montant HT</th>
        <th>Taux TVA</th>
        <th>Montant TTC</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($facture->operationfactures as $operation)
        <tr>
            <td>{{ $operation->nature }}</td>
            <td>{{ $operation->quantité }}</td>
            <td>{{ $operation->montant_ht }}</td>
            <td>{{ $operation->taux_tva }}%</td>
            <td>{{ $operation->montant_ttc }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="totals">
    <p style="margin-left: 500px;">Total Montant HT: {{ $facture->total_montant_ht }}</p>
    <p style="margin-left: 500px;">Total Montant TTC: {{ $facture->total_montant_ttc }}</p>
    <p style="margin-left: 50px;">Arrêter Le Présent Facture A La Somme De:
        {{ $facture->total_montant_letters }}</p>
</div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Facture</title>
    <style>
        /* Define your CSS styles here */
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            margin: 0 auto;
            max-width: 600px;
        }

        .logo {
            max-width: 200px;
            height: auto;
        }

        .facture-header {
            background-color: #eee;
            padding: 20px;
        }

        .facture-header h1 {
            margin: 0;
        }

        .facture-header p {
            margin: 0;
        }

        .facture-body {
            padding: 20px;
        }

        .facture-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .facture-table th {
            background-color: #eee;
            text-align: left;
            padding: 10px;
        }

        .facture-table td {
            border: 1px solid #ddd;
            text-align: left;
            padding: 10px;
        }

        .facture-total {
            text-align: right;
            margin-top: 20px;
            font-weight: bold;
        }

        .facture-total p {
            margin: 0;
        }




    </style>
</head>
<body>
<div class="container">
    <div class="facture-header">
        <img class="logo" src="{{ $logo }}" alt="Logo">
        <h1>Facture n° {{ $facture->id }}</h1>
        <p>Client : {{ $facture->client }}</p>
        <p>Email : {{ $facture->client_email }}</p>
    </div>

    <div class="facture-body">
        <table class="facture-table">
            <thead>
            <tr>
                <th>Nature</th>
                <th>Quantité</th>
                <th>Montant HT</th>
                <th>Taux TVA</th>
                <th>Montant TTC</th>
            </tr>
            </thead>
            <tbody>
            @foreach($facture->operationfactures as $operation)
                <tr>
                    <td>{{ $operation->nature }}</td>
                    <td>{{ $operation->quantité }}</td>
                    <td>{{ $operation->montant_ht }} Dhs</td>
                    <td>{{ $operation->taux_tva }}%</td>
                    <td>{{ $operation->montant_ttc }} Dhs</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="facture-total">
            <p>Total HT : {{ $facture->total_montant_ht }} Dhs</p>
            <p>Total TTC : {{ $facture->total_montant_ttc }} Dhs</p>
            <p>Total en lettres : {{ $facture->total_montant_letters }} Dhs</p>
        </div>


    </div>
</div>
</body>
</html>







