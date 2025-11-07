<x-mail::message>
    De nouveaux utilisateurs attendent votre approbation.

    <x-mail::panel>
        Un utilisateur a demandé à rejoindre Ma Boo en tant qu'administrateur, vendeur, ou professionnel.
        Cliquez sur le lien ci-dessous pour en savoir davantage.
    </x-mail::panel>

    <x-mail::button :url="$dashboardUrl">
        Ouvrir mon Dashboard
    </x-mail::button>

    Merci,<br>
    {{ config('app.name') }}
</x-mail::message>