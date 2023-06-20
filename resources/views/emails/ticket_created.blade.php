<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Ticket Created</title>
    <style>
        @media only screen and (max-width: 600px) {
            .container {
                width: 100% !important;
                padding: 20px !important;
            }
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 40px;
            background-color: #ffffff;
            box-shadow: 0px 2px 5px 0px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #3d4852;
            font-size: 18px;
            font-weight: bold;
            margin: 0;
            margin-bottom: 20px;
            text-align: left;
        }

        .logo {
            color: #ff0000;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: left;
            height: 75px;
            max-height: 75px;
            width: 75px;
        }

        p {
            color: #555555;
            font-size: 16px;
            line-height: 1.5em;
            margin: 0;
            margin-bottom: 10px;
            text-align: left;
        }

        ul {
            color: #555555;
            font-size: 16px;
            line-height: 1.5em;
            margin: 0;
            margin-bottom: 10px;
            text-align: left;
            padding-left: 20px;
        }

        .footer {
            margin-bottom: 10px;
            line-height: 1.5em;
            margin-top: 20px;
            color: #777777;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="logo">EBUILD</h1>
        <h1>New Ticket Created</h1>
        <p>Dear Admin and Assigned Personnel,</p>
        <p>A new ticket has been created for the project: <strong>{{ $project->projectname }}</strong></p>
        <p>Ticket Details:</p>
        <ul>
            <li>Object: {{ $ticket->object }}</li>
            <li>Description: {{ $ticket->description }}</li>
            <li>Closing Date: {{ $ticket->closing_date }}</li>
        </ul>
        <p>Please take necessary actions to resolve the ticket in a timely manner.</p>
        <p class="footer">Thank you.</p>
    </div>
</body>
</html>
