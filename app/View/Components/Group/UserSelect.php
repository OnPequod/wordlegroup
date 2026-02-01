<?php

namespace App\View\Components\Group;

use App\Models\Group;
use App\Models\User;
use Illuminate\View\Component;

class UserSelect extends Component
{
    public $errors;

    public $group;

    public $label;

    public $name;

    public $options;

    public $defaultEmpty;

    public $selectedUserId;

    public $emptyPhrase;

    public function __construct($name, Group $group, $label = 'Group Member', $selectedUserId = null, $defaultEmpty = false, $emptyPhrase = 'Select A User')
    {
        $this->name = $name . uniqid();
        $this->label = $label;
        $this->defaultEmpty = $defaultEmpty;
        $this->group = $group;
        $this->selectedUserId = $selectedUserId;

        // Reuse loaded memberships or load them once
        if (!$group->relationLoaded('memberships')) {
            $group->load('memberships.user');
        }

        $this->options = $group->memberships
            ->map(function ($membership) {
                return [
                    'value' => $membership->user->id,
                    'label' => $membership->user->name,
                ];
            });

            if($defaultEmpty) {
                $this->options->prepend(['value' => '', 'label' => $emptyPhrase]);
            }
    }

    public function render()
    {
        return view('components.group.user-select');
    }
}
