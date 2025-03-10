<x-form-section submit="updatePassword">
    <x-slot name="title">
        {{ __('Modifier le mot de passe') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Le mot de passe doit avoir 08 carractère au mois et des chiffres') }}
    </x-slot>

    <x-slot name="form">
        <div class="col-span-6 sm:col-span-4">
            <x-label for="current_password" value="{{ __('Ancien mot de passe') }}" />
            <x-input id="current_password" type="password" class="mt-1 block w-full" wire:model.defer="state.current_password" autocomplete="current-password" />
            <x-input-error for="current_password" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="password" value="{{ __('Nouveau mot de passe') }}" />
            <x-input id="password" type="password" class="mt-1 block w-full" wire:model.defer="state.password" autocomplete="new-password" />
            <x-input-error for="password" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="password_confirmation" value="{{ __('Confirmer le mot de passe') }}" />
            <x-input id="password_confirmation" type="password" class="mt-1 block w-full" wire:model.defer="state.password_confirmation" autocomplete="new-password" />
            <x-input-error for="password_confirmation" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('Réussi...') }}
        </x-action-message>

        <x-button type="submit" class="relative">
            <span class="flex items-center">
                <x-icons name="check" class="w-5 h-5 mr-2" />
                {{ __('Enregistrer') }}
            </span>
            <div wire:loading wire:target="updatePassword" class="absolute inset-0 flex items-center justify-center bg-indigo-600 bg-opacity-50">
                <x-icons name="loading" class="w-5 h-5 animate-spin text-white" />
            </div>
        </x-button>
    </x-slot>
</x-form-section>
