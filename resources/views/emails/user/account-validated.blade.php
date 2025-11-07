<x-mail::message>
    On vous souhaite la Bienvenue!

    <x-mail::panel>
        Votre demande d'accès à Ma Boo a été acceptée.
    </x-mail::panel>

    <x-mail::button :url="$dashboardUrl">
        Ouvrir mon Dashboard
    </x-mail::button>

    Merci,<br>
    {{ config('app.name') }}
</x-mail::message>