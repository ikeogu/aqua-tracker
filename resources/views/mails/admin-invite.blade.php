<x-mail::message>
# Hello {{ $user->first_name }},

You are receiving this mail because you have been added as an employee to {{$farm->name}}, with a role of {{$role}}.

Your Email Password is: {{$password}}

Please login to the system using the following link:
<x-mail::button :url="route(env('FRONTEND_URL') .'/login')">
    Login
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
