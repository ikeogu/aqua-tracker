
<x-mail::message>
# Hello,

You are receiving this mail because you have been invited to join {{$tenant->organization_name}} as a team member with the role of {{$role}}.

Your Email Password is: {{$password}}

Please login to the system using the following link:
<x-mail::button :url="route('login')">
    Login
</x-mail::button>



Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
