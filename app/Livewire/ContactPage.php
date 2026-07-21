<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ContactMessage;

class ContactPage extends Component
{
    public string $name = '';
    public string $email = '';
    public string $message = '';

    protected array $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email',
        'message' => 'required|min:3',
    ];

    public function sendMessage()
    {
        $this->validate();

        ContactMessage::create([
            'name' => $this->name,
            'email' => $this->email,
            'message' => $this->message,
        ]);

        session()->flash('success', 'Thank you! Your message has been sent successfully.');

        $this->reset(['name', 'email', 'message']);
    }

    public function render()
    {
        return view('livewire.contact-page')
            ->layout('components.layouts.frontend');
    }
}
