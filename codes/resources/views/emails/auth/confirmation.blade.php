<x-mail::message>
Encore une étape pour finaliser votre inscription

<x-mail::panel>
{{$code}}
</x-mail::panel>
est votre code pour confirmer votre adresse email.

Merci,<br>
{{ config('app.name') }}
</x-mail::message>
