<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $facture->id }}</title>
    <style>
        /* Add any custom CSS styles for the PDF here */
    </style>
</head>
<body>
<h1>Facture {{ $facture->id }}</h1>

<p>Client: {{ $facture->client }}</p>
<p>Email: {{ $facture->client_email }}</p>

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

<p>Total Montant HT: {{ $facture->total_montant_ht }}</p>
<p>Total Montant TTC: {{ $facture->total_montant_ttc }}</p>
<p>Total Montant Letters: {{ $facture->total_montant_letters }}</p>

<p>Date création: {{ $facture->date_creation }}</p>
</body>
</html>
