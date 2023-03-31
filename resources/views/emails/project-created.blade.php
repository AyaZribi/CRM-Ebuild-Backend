<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Project Created</title>
</head>
<body>
<h1>New Project Created</h1>
<p>A new project has been created:</p>
<ul>
    <li><strong>Project Name:</strong> {{ $project->projectname }}</li>
    <li><strong>Type of Project:</strong> {{ $project->typeofproject }}</li>
    <li><strong>Frameworks:</strong> {{ $project->frameworks }}</li>
    <li><strong>Database:</strong> {{ $project->database }}</li>
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
</body>
</html>
