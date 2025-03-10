<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        {{ __('Information de compte') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Mettre à jour les informations de votre compte') }}
    </x-slot>

    <x-slot name="form">
        <!-- Profile Photo -->
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{photoName: null, photoPreview: null}" class="col-span-6 sm:col-span-4">
                <!-- Profile Photo File Input -->
                <input type="file" id="photo" class="hidden"
                            wire:model.live="photo"
                            x-ref="photo"
                            accept="image/*"
                            x-on:change="
                                if ($refs.photo.files.length > 0) {
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.photo.files[0]);
                                }
                            " />

                <x-label for="photo" value="{{ __('Photo') }}" />

                <!-- Current Profile Photo -->
                <div class="mt-2" x-show="! photoPreview">
                    <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->name }}" class="rounded-full h-20 w-20 object-cover">
                </div>

                <!-- New Profile Photo Preview -->
                <div class="mt-2" x-show="photoPreview" style="display: none;">
                    <span class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                          x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                    </span>
                </div>

                <div class="mt-2 flex items-center space-x-2">
                    <x-secondary-button type="button" class="relative" x-on:click.prevent="$refs.photo.click()">
                        <span class="flex items-center">
                            <x-icons name="add" class="w-5 h-5 mr-2" />
                            {{ __('Choisir une photo') }}
                        </span>
                    </x-secondary-button>

                    @if ($this->user->profile_photo_path)
                        <x-secondary-button type="button" class="relative" wire:click="deleteProfilePhoto" wire:loading.attr="disabled">
                            <span class="flex items-center">
                                <x-icons name="delete" class="w-5 h-5 mr-2" />
                                {{ __('Supprimer') }}
                            </span>
                            <div wire:loading wire:target="deleteProfilePhoto" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-50">
                                <x-icons name="loading" class="w-5 h-5 animate-spin" />
                            </div>
                        </x-secondary-button>
                    @endif
                </div>

                <x-input-error for="photo" class="mt-2" />
            </div>
        @endif

        <!-- Name -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="name" value="{{ __('Nom') }}" />
            <x-input id="name" type="text" class="mt-1 block w-full" wire:model.defer="state.name" required autocomplete="name" />
            <x-input-error for="name" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="email" value="{{ __('Email') }}" />
            <x-input id="email" type="email" class="mt-1 block w-full" wire:model.defer="state.email" required autocomplete="username" />
            <x-input-error for="email" class="mt-2" />

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                <p class="text-sm mt-2">
                    {{ __('Votre adresse email n\'est pas vérifiée.') }}

                    <button type="button" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" wire:click.prevent="sendEmailVerification" wire:loading.attr="disabled">
                        {{ __('Cliquez ici pour renvoyer l\'email de vérification.') }}
                    </button>
                </p>

                @if ($this->verificationLinkSent)
                    <p class="mt-2 font-medium text-sm text-green-600">
                        {{ __('Un nouveau lien de vérification a été envoyé à votre adresse email.') }}
                    </p>
                @endif
            @endif
        </div>
    </x-slot>

    <x-slot name="actions">
        <div class="flex items-center space-x-3">
            <x-action-message class="mr-3" on="saved">
                {{ __('Enregistré.') }}
            </x-action-message>

            <x-button type="submit" class="relative">
                <span class="flex items-center">
                    <x-icons name="check" class="w-5 h-5 mr-2" />
                    {{ __('Enregistrer') }}
                </span>
                <div wire:loading wire:target="updateProfileInformation" class="absolute inset-0 flex items-center justify-center bg-indigo-600 bg-opacity-50">
                    <x-icons name="loading" class="w-5 h-5 animate-spin text-white" />
                </div>
            </x-button>
        </div>
    </x-slot>
</x-form-section>
