<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Dashboard extends Component
{
    public function testClick()
    {
        Session::flash('test_message', 'Livewire is working! Button click was successful.');
    }

    public function render()
    {
        $user = Auth::user();
        $recentOrders = $user->orders()->latest()->take(3)->get();

        return view('livewire.dashboard', [
            'recentOrders' => $recentOrders
        ]);
    }
}
