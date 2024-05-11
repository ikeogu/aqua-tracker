<x-mail::message>
# Hello dear,

This is to notify you that your payment for {{$subscribedPlan->subscriptionPlan->title}} plan for {{$organization}} was successful, and {{$subscribedPlan->amount}} amount was paid for {{$subscribedPlan->no_of_months}} month(s), and plan starts {{$subscribedPlan->start_date}} and ends on {{$subscribedPlan->end_date}};

Thank you for using {{ config('app.name') }} .

Thanks ,<br>
{{ config('app.name') }}
</x-mail::message>
