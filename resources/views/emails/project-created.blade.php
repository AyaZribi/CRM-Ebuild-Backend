<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Project Created</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0px 2px 5px 0px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333333;
            font-size: 24px;
            margin: 0;
            margin-bottom: 20px;
        }

        .logo {
            color: #ff0000;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        p {
            color: #555555;
            font-size: 16px;
            line-height: 1.5;
            margin: 0;
            margin-bottom: 10px;
        }

        ul {
            margin: 0;
            padding: 0;
            list-style-type: none;
            margin-bottom: 10px;
        }

        ul li {
            margin-bottom: 5px;
        }

        .footer {
            margin-top: 20px;
            color: #777777;
            font-size: 14px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="logo">EBUILD</h1>
    <h1>New Project Created</h1>

    <p>A new project has been created:</p>

    <ul>
        <li><strong>Project Name:</strong> {{ $project->projectname }}</li>
        <li><strong>Type of Project:</strong> {{ $project->typeofproject->name }}</li>
        <li><strong>Frameworks:</strong>
            @foreach ($project->frameworks as $framework)
                {{ $framework->name }},
            @endforeach
        </li>
        <li><strong>Database:</strong> {{ $project->project_database }}</li>
        <li><strong>Description:</strong> {{ $project->description }}</li>
        <li><strong>Date Created:</strong> {{ $project->datecreation }}</li>
        <li><strong>Deadline:</strong> {{ $project->deadline }}</li>
        <li><strong>Status:</strong> {{ $project->etat }}</li>
    </ul>

    <p>Assigned staff:</p>

    <ul>
        @foreach ($project->personnel as $personnel)
            <li>{{ $personnel->Name }}</li>
        @endforeach
    </ul>

    <p class="footer">Thank you for using our platform!</p>
</div>
</body>
</html>
