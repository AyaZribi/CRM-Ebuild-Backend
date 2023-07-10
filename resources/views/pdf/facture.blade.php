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
         background-color: #f2e3ea;
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
            margin-top: 15px;
        }
        table th, table td {
            border: #ffffff;
            padding: 5px;
            width: 200px;
        }


        table th {
            background-color: #ffffff;
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
<div class="vertical-text"><strong style="color: #000000;">FACTURE  </strong>N°{{ $facture->formatted_id }}</div>
    <table class="facture-header">
        <tr>

            <th>
            <h2 style="font-size: 30px;width: 400px; font-family: Bold,serif ; color: #a22b41;">EBUILD</h2>
            <p><strong>MF: EBUILD, SARL immatriculée au registre national </strong></p>
            <p><strong>des entreprises sous l’identiant unique 1751386/T .</strong></p>

            <p><strong>N° de téléphone:</strong>98157896</p>

            </th>
        </tr>
        </table>
        <table>
            <tbody>
            <td>

                <strong style="font-size: 20px; font-family: Bold,serif; color: #a22b41;">De</strong>
                <hr style="border: 1px solid #a22b41;">
                <p style="font-size: 16px;background-color: #eee;" > EBUILD</p>
                <p><strong>Matricule Fiscal:</strong></p>
                <p>EBUILD, SARL immatriculée au</p>
                <p>registre national des entreprises </p>
                <p>sous l’identiant unique 1751386/T.</p>
            </td>
        <td >


                <strong style=" font-size: 20px;font-family: Bold,serif;color: #a22b41;">À</strong>
                <hr style="border: 1px solid #a22b41;">
                <p style="font-size: 16px;background-color: #eee;"> {{ $facture->client }}</p>
                <p><strong>Email:</strong> {{ $facture->client_email }}</p>
                <p><strong>N° de téléphone:</strong> {{ $phone_number }}</p>

        </td>
        <td style="font-size: 7px;">
            <h1><strong>Numéro </strong><small>{{ $facture->formatted_id }}</small></h1>
            <h1><strong>Date </strong><small>{{ $facture->created_at->formatLocalized('%a. %d %B %Y') }}</small></h1>

        </td>

        </tbody>

    </table>

</div>

<div class="header" >
{{--<img src="{{ asset('resources/images/logo.svg') }}" alt="Logo">--}}

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
  <table style="width: 220px; margin-left: 400px; text-align: right;">
      <h1 style="margin-left: 60px;"><small>TOTAUX</small></h1>
      <tr>
          <th><strong>Total Montant HT</strong></th>
          <td>{{ $facture->total_montant_ht }}<strong>DT</strong></td>
      </tr>
      @if (!is_null($facture->operationfactures->first()->montant_ttc))
          <tr>
              <th><strong>Taux TVA</strong></th>
              <td>{{ $operation->taux_tva }}%</td>
          </tr>
      @endif
      <tr>
          <th><strong>TIMBRE</strong></th>
          <td>1.00<strong>DT</strong></td>
      </tr>
      <tr>
          <th><strong>TOTAL À PAYER</strong></th>
          <td>{{ $facture->total_montant_ttc }}<strong>DT</strong></td>
      </tr>

  </table>

  <div style="margin-left: 50px;">
      <p><strong>Arrêter La Présente Facture A La Somme De:</strong></p>
      <p>{{ $facture->total_montant_letters }}</p>
  </div>
</div>
</body>
</html>

