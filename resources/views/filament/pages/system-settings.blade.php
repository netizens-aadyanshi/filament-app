<x-filament-panels::page>
    <x-filament::section
        icon="heroicon-o-cog-6-tooth"
        collapsible
        persist-collapsed
        id="settings-general"
    >
        <x-slot name="heading">General</x-slot>
        <x-slot name="description">Basic panel configuration settings</x-slot>

        {{ $this->form }}

    </x-filament::section>
</x-filament-panels::page>
