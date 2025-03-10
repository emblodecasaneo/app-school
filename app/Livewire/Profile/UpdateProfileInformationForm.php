<?php

namespace App\Livewire\Profile;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Livewire\Component;
use Livewire\WithFileUploads;

class UpdateProfileInformationForm extends Component
{
    use WithFileUploads;

    /**
     * The component's state.
     *
     * @var array
     */
    public $state = [];

    /**
     * The new avatar for the user.
     *
     * @var mixed
     */
    public $photo;

    /**
     * Determine if the verification email was sent.
     *
     * @var bool
     */
    public $verificationLinkSent = false;

    /**
     * Prepare the component.
     *
     * @return void
     */
    public function mount()
    {
        $user = Auth::user();
        $this->state = [
            'name' => $user->name,
            'email' => $user->email,
        ];
    }

    /**
     * Update the user's profile information.
     *
     * @param  \Laravel\Fortify\Contracts\UpdatesUserProfileInformation  $updater
     * @return void
     */
    public function updateProfileInformation(UpdatesUserProfileInformation $updater)
    {
        try {
            $this->resetErrorBag();

            $updater->update(
                Auth::user(),
                $this->photo
                    ? array_merge($this->state, ['photo' => $this->photo])
                    : $this->state
            );

            if (isset($this->photo)) {
                return redirect()->route('profile.show');
            }

            $this->dispatch('saved');
            $this->dispatch('refresh-navigation-menu');
            
            session()->flash('success', 'Profil mis à jour avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la mise à jour du profil : ' . $e->getMessage());
        }
    }

    /**
     * Delete user's profile photo.
     *
     * @return void
     */
    public function deleteProfilePhoto()
    {
        $user = Auth::user();
        
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            
            // Mettre à jour directement la base de données
            DB::table('users')
                ->where('id', $user->id)
                ->update(['profile_photo_path' => null]);
        }

        $this->dispatch('refresh-navigation-menu');
    }

    /**
     * Send email verification.
     *
     * @return void
     */
    public function sendEmailVerification()
    {
        $user = Auth::user();
        
        // Vérifier si l'email est vérifié en vérifiant directement la colonne email_verified_at
        if ($user->email_verified_at === null) {
            // Envoyer un email de vérification (simulé ici)
            // Dans un vrai cas, vous utiliseriez une notification ou un mail
            // $user->sendEmailVerificationNotification();
            
            // Marquer comme envoyé
            $this->verificationLinkSent = true;
        }
    }

    /**
     * Get the current user.
     *
     * @return mixed
     */
    public function getUserProperty()
    {
        return Auth::user();
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('profile.update-profile-information-form');
    }
} 