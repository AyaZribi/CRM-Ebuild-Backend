<x-mail::message>
    # Welcome to our app!

    Thank you for registering with us. Here are your login details:

    Email: {{ $email }}
    Password: {{ $password }}

    Please keep this information safe and don't share it with anyone.

    Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
