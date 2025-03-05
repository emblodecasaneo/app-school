<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;
    
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $role = 'user';
    public $search = '';
    
    public $isModalOpen = false;
    public $editMode = false;
    public $userId = null;
    
    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8|confirmed',
        'role' => 'required|in:admin,user,teacher',
    ];
    
    protected $messages = [
        'name.required' => 'Le nom est obligatoire',
        'name.min' => 'Le nom doit contenir au moins 3 caractères',
        'email.required' => 'L\'email est obligatoire',
        'email.email' => 'Veuillez entrer un email valide',
        'email.unique' => 'Cet email est déjà utilisé',
        'password.required' => 'Le mot de passe est obligatoire',
        'password.min' => 'Le mot de passe doit contenir au moins 8 caractères',
        'password.confirmed' => 'Les mots de passe ne correspondent pas',
        'role.required' => 'Le rôle est obligatoire',
        'role.in' => 'Le rôle doit être admin, user ou teacher',
    ];
    
    public function render()
    {
        $users = User::where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->paginate(10);
        
        return view('livewire.user-management', [
            'users' => $users
        ]);
    }
    
    public function openModal()
    {
        $this->resetValidation();
        $this->resetFields();
        $this->isModalOpen = true;
        $this->editMode = false;
    }
    
    public function closeModal()
    {
        $this->isModalOpen = false;
    }
    
    public function resetFields()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->role = 'user';
        $this->userId = null;
    }
    
    public function store()
    {
        if ($this->editMode) {
            $this->update();
            return;
        }
        
        $this->validate();
        
        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role,
        ]);
        
        session()->flash('message', 'Utilisateur créé avec succès!');
        $this->closeModal();
        $this->resetFields();
    }
    
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        
        $this->editMode = true;
        $this->isModalOpen = true;
    }
    
    public function update()
    {
        $this->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'role' => 'required|in:admin,user,teacher',
        ]);
        
        $user = User::findOrFail($this->userId);
        
        $userData = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
        ];
        
        // Mettre à jour le mot de passe uniquement s'il est fourni
        if (!empty($this->password)) {
            $this->validate([
                'password' => 'min:8|confirmed',
            ]);
            $userData['password'] = Hash::make($this->password);
        }
        
        $user->update($userData);
        
        session()->flash('message', 'Utilisateur mis à jour avec succès!');
        $this->closeModal();
        $this->resetFields();
    }
    
    public function delete($id)
    {
        // Empêcher la suppression de son propre compte
        if ($id == auth()->id()) {
            session()->flash('error', 'Vous ne pouvez pas supprimer votre propre compte!');
            return;
        }
        
        User::find($id)->delete();
        session()->flash('message', 'Utilisateur supprimé avec succès!');
    }
} 