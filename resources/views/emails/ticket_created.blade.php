<!DOCTYPE html>
<html>
<head>
    <title>New Ticket Created</title>
</head>
<body>
<h1>New Ticket Created</h1>
<p>Dear Admin and Assigned Personnel,</p>
<p>A new ticket has been created for the project: <strong>{{ $project->projectname }}</strong></p>
<p>Ticket Details:</p>
<ul>
    <li>Object: {{ $ticket->object }}</li>
    <li>Description: {{ $ticket->description }}</li>
    <li>Closing Date: {{ $ticket->closing_date }}</li>
    <li>Status: {{ $ticket->status }}</li>
    <li>Priority: {{ $ticket->priority }}</li>

</ul>
<p>Please take necessary actions to resolve the ticket in a timely manner.</p>
<p>Thank you.</p>
</body>
</html>
