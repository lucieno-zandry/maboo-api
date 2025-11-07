<x-mail::message>
Cliquez sur le lien ci-dessous pour réinitialiser votre mot de passe
 
<x-mail::panel>
    Vous pouvez ignorer cet email ci vous n'êtes pas l'auteur de cette action.
</x-mail::panel>

<x-mail::button :url="$resetUrl">
Réinitialiser le mot de passe
</x-mail::button>

Merci,<br>
{{ config('app.name') }}
</x-mail::message>