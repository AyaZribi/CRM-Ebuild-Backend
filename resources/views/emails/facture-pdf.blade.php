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

        .facture-signature {
            margin-top: 50px;
            text-align: right;
        }

        .facture-signature p {
            margin: 0;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="facture-header">
        <img class="logo" src="{{ $logo }}" alt="Logo">
        <h1>Facture n° {{ $facture->id }}</h1>
        <p>Date : {{ $facture->date_creation->format('d/m/Y') }}</p>
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
