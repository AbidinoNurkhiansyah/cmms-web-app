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

    public ?string $personnelTeamFilter = null;

    public function searchUser(string $value = '')
    {
        $query = User::query();

        if ($this->personnelTeamFilter) {
            $query->where('team', $this->personnelTeamFilter);
        }

        if (!empty($value)) {
            $query->where('name', 'like', "%{$value}%");
        }

        $this->users = $query->get();
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
