<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//\Auth::loginUsingId(1);

Route::get('/', \App\Http\Livewire\Home::class)->name('home');

Route::get('/privacy-policy', \App\Http\Livewire\PrivacyPolicy::class)->name('privacy-policy');
Route::get('/contact', \App\Http\Livewire\Contact::class)->name('contact');
Route::get('/about', \App\Http\Livewire\About::class)->name('about');
Route::get('/rules-and-frequently-asked-questions', \App\Http\Livewire\RulesAndFrequentlyAskedQuestions::class)->name('rules-and-faq');

Route::get('/group/create', \App\Http\Livewire\Group\Create::class)->name('group.create');
Route::get('/group/{group}/verify-email', \App\Http\Livewire\Group\VerifyEmailNotification::class)
    ->name('group.verify-email-notification');
Route::get('/group/{groupId}/verify', \App\Http\Livewire\Group\Verify::class)->name('group.verify');
Route::get('/group/invitation/{invitationId}', \App\Http\Livewire\Group\Invitation::class)->name('group.invitation');

Route::post('/score/email', \App\Http\Controllers\MailScoreMessageController::class)
    ->name('score.email')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::get('/score/{score}', \App\Http\Livewire\Score\SharePage::class)->name('score.share-page');

Route::get('/group/{group}', \App\Http\Livewire\Group\Home::class)->name('group.home');

Route::middleware(['auth'])->group(function () {
    Route::get('/account', \App\Http\Livewire\Account\Home::class)->name('account.home');
    Route::get('/account/groups', \App\Http\Livewire\Account\Groups::class)->name('account.groups');
    Route::get('/account/settings', \App\Http\Livewire\Account\Settings::class)->name('account.settings');
    Route::get('/account/record-score', \App\Http\Livewire\Account\RecordScore::class)->name('account.record-score');
    Route::get('/group/{group}/settings', \App\Http\Livewire\Group\Settings::class)->name('group.settings');
    Route::get('/group/{group}/not-verified', \App\Http\Livewire\Group\NotVerifiedNotification::class)->name('group.not-verified');

    Route::get('/logout', \App\Http\Controllers\LogoutController::class)->name('logout');
});

Route::middleware(['guest'])->group(function () {
    Route::get('/login', \App\Http\Livewire\Account\Login::class)->name('login');
    Route::get('/register', \App\Http\Livewire\Account\Register::class)->name('register');
    Route::get('/account/{user}/verify-email', \App\Http\Livewire\Account\VerifyEmailNotification::class)
         ->name('account.verify-email-notification');
    Route::get('/account/{user}/verify', \App\Http\Livewire\Account\Verify::class)->name('account.verify');
});

Route::get('/u/{user}', \App\Http\Livewire\Account\Profile::class)->name('account.profile');


