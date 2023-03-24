<!DOCTYPE html>
<html lang="">
<head>
    <meta charset="utf-8">
    <title>{{ $devis->id }}.pdf</title>
    <style>
        /* Add your CSS styles here */
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
        }
        th {
            background-color: #eee;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<h1 class="title">Devis N° {{ $devis->id }}</h1>
<table>
    <thead>
    <tr>
        <th>Client</th>
        <th>Email Client</th>
        <th>Date de création</th>
        <th>Nombre d'opérations</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>{{ $devis->client }}</td>
        <td>{{ $devis->email_client }}</td>
        <td>{{ $devis->created_at->format('d/m/Y H:i:s') }}</td>
        <td>{{ $devis->operations->count() }}</td>
    </tr>
    </tbody>
</table>
<table>
    <thead>
    <tr>
        <th>Nature de l'opération</th>
        <th>Montant HT</th>
        <th>Montant avec TVA</th>
    </tr>
    </thead>
    <tbody>
    @foreach($devis->operations as $operation)
        <tr>
            <td>{{ $operation->nature }}</td>
            <td>{{ $operation->montant_ht }}</td>
            <td>{{ $operation->montant_ttc }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
