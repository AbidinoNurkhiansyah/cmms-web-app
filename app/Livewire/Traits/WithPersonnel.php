<?php

namespace App\Livewire\Traits;

use App\Models\User;
use Illuminate\Support\Collection;

trait WithPersonnel
{
    public Collection $users;
    public array $pics = ['']; // Dynamic PIC array

    public function mountWithPersonnel(): void
    {
        $this->users = User::all();
    }

    public function searchUser(string $value = '')
    {
        if (empty($value)) {
            $this->users = User::all();
        } else {
            $this->users = User::where('name', 'like', "%{$value}%")->get();
        }
    }

    public function addPic()
    {
        $this->pics[] = '';
    }

    public function removePic($index)
    {
        unset($this->pics[$index]);
        $this->pics = array_values($this->pics);
    }
}
