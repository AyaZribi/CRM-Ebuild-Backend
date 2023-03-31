
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
            background-color: #FF0000;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
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
<h1 class="title"> {{ $devis->formatted_id }}</h1>

<div class="vertical-text">{{ $devis->formatted_id }}</div>


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
        <td>{{ $devis->client }}</td>
        <td>{{ $devis->client_email }}</td>
        <td>{{ $phone_number }}</td>
        <td>{{ $RNE }}</td>
        <td>{{ $devis->created_at->format('d/m/Y H:i:s') }}</td>
        <td>{{ $devis->operations->count() }}</td>
    </tr>
    </tbody>
</table>

<table style="margin-left: 20px;">
    <thead>
    <tr>
        <th>Nature de l'opération</th>
        <th>Montant HT</th>
        <th>Taux de TVA</th>
        <th>Montant avec TVA</th>
    </tr>
    </thead>
    <tbody>
    @foreach($devis->operations as $operation)
        <tr>
            <td>{{ $operation->nature }}</td>
            <td>{{ $operation->montant_ht }}</td>
            <td>{{ $operation->taux_tva }}</td>
            <td>{{ $operation->montant_ttc }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>

