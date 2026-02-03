<?php

namespace App\View\Components\Account;

use App\Services\AuthenticatedUserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class Nav extends Component
{
    public $activePage;

    public $noneSelected;

    public $user;

    public $routeMap;

    public function __construct($activePage)
    {
        $this->user = app(AuthenticatedUserService::class)->get();
        $this->routeMap = $this->getRouteMap();
        $this->noneSelected = !in_array($activePage, array_keys($this->routeMap));
        $this->activePage = $this->noneSelected ? 'navigation' : $activePage;
    }

    public function getRouteMap()
    {
        $routes = collect($this->getPages())
            ->flatMap(function ($page, $pageName) {

                if ($pageName === 'userGroups') {
                    return $this->user->memberships
                        ->flatMap(function ($membership) {
                            return ["group.{$membership->group->id}" => route('group.home', $membership->group)];
                        });
                } else {
                    return [$pageName => $page['route']];
                }
            })
            ->reject(fn($page) => $page === null)
            ->toArray();

        // Add createGroup route for users with no groups
        $routes['createGroup'] = route('group.create');

        return $routes;
    }

    public function getPages()
    {
        $routes = [
            'placeholder'  => [
                'title'       => 'Navigation',
                'placeholder' => true,
                'route'       => null,
            ],
            'home'         => [
                'route' => route('account.home'),
                'title' => 'My Stats',
            ],
            'groups'       => [
                'route' => route('account.groups'),
                'title' => 'My Groups',
            ],
            'userGroups'   => [
                'title' => 'Group Pages',
            ],
            'record-score' => [
                'route' => route('account.record-score'),
                'title' => 'Record Score',
            ],
            'settings'     => [
                'route' => route('account.settings'),
                'title' => 'My Settings',
            ],
        ];

        if(! Auth::check()) {
            unset($routes['userGroups']);
        }

        return $routes;
    }

    public function render()
    {
        return view('components.account.nav', ['pages' => $this->getPages()]);
    }
}
