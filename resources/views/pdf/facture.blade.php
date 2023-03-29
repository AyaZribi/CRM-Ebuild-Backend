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
            font-size: 28px;
            margin: 0;
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
            background-color: #ccc;
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
    </style>
</head>
<body>
<div class="header">
    <img src="{{ url('images/logo.svg') }}" alt="Logo">
    <h1>{{ $facture->formatted_id }}</h1>
</div>

<p>Client: {{ $facture->client }}</p>
<p>Email: {{ $facture->client_email }}</p>
<p>Phone Number: {{ $phone_number }}</p>
<p>RNE: {{ $RNE }}</p>
<p>Date création: {{ $facture->created_at->format('d/m/Y H:i:s') }}</p>

<table>
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
    <p>Total Montant HT: {{ $facture->total_montant_ht }}</p>
    <p>Total Montant TTC: {{ $facture->total_montant_ttc }}</p>
    <p>Total Montant Letters: {{ $facture->total_montant_letters }}</p>
</div>
</body>
</html>
