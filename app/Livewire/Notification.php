<?php

namespace App\Livewire;

use Livewire\Component;

class Notification extends Component
{
    public $message = '';
    public $type = 'info'; // info, success, error, warning
    public $show = false;

    protected $listeners = ['notify' => 'showNotification'];

    public function showNotification($data)
    {
        $this->message = $data['message'] ?? 'Notification';
        $this->type = $data['type'] ?? 'info';
        $this->show = true;

        // Auto-hide after 3 seconds
        $this->dispatch('dismissNotification');
    }

    public function dismiss()
    {
        $this->show = false;
    }

    public function render()
    {
        return view('livewire.notification');
    }
}
