<x-mail::message>
# Hello {{ $user }}

You are receiving this mail because you are trying to reset your password at {{ config('app.name') }}.

Your Password Code is:

<x-mail::panel>
 {{ $otp }}
</x-mail::panel>

This code expires in 5 minutes.

Do not share the above code and please ignore this message if you didn't make this request and we won't allow the request.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
