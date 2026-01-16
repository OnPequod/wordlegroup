<?php

use App\Http\Livewire\Account\Login;
use App\Models\User;
use Livewire\Livewire;

it('logs in with a valid email link token', function () {
    $user = User::factory()->create([
        'auth_token' => 'auth-token',
        'auth_token_generated_at' => now(),
    ]);

    $response = $this->get(route('login', [
        'id' => $user->id,
        'token' => $user->auth_token,
    ]));

    $response->assertRedirect(route('account.home'));
    $this->assertAuthenticatedAs($user);
});

it('logs in with a valid login code', function () {
    $user = User::factory()->create([
        'login_code' => '123456',
        'login_code_generated_at' => now(),
    ]);

    Livewire::test(Login::class)
        ->set('user', $user)
        ->set('loginCode', '123456')
        ->call('attemptLoginWithCode')
        ->assertRedirect(route('account.home'));

    $this->assertAuthenticatedAs($user);
});
