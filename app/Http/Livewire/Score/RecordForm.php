<?php

namespace App\Http\Livewire\Score;

use App\Concerns\WordleBoard;
use App\Models\Group;
use App\Models\User;
use App\Rules\BoardNumberMustBeValid;
use App\Rules\DateMustBeValid;
use App\Rules\ValidWordleBoard;
use App\Services\ScoreRecorder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RecordForm extends Component
{
    public $board;

    public $boardNumber;

    public $bricked;

    public $date;

    public $group;

    public $hardMode;

    public $hideEmail;

    public $isGroupAdmin;

    public $quick;

    public $recordForUserId;

    public $recordingForSelf;

    public $score;

    public $botSkillScore;

    public $botLuckScore;

    public $user;

    public function mount(User $user, Group $group = null, $quick = false, $hideEmail = false)
    {
        $this->user = $user;
        $this->recordForUserId = $user->id;
        $this->recordingForSelf = $this->user->id === Auth::user()->id;
        $this->date = app(WordleBoard::class)->activeBoardStartTime->format('Y-m-d');
        $this->boardNumber = app(WordleBoard::class)->getBoardNumberFromDate($this->date);
        $this->quick = $quick;
        $this->hideEmail = $hideEmail;

        $this->group = $group;
        $this->isGroupAdmin = $group ? $this->getIsGroupAdmin($group, $user) : false;
    }

    public function getIsGroupAdmin(Group $group, User $user)
    {
        return $group->isAdmin($user);
    }

    public function recordScoreFromBoard()
    {
        $this->validate([
            'board' => ['required', new ValidWordleBoard()],
        ]);

        app(ScoreRecorder::class)->recordFromBoard(
            $this->recordedUser(),
            $this->board,
            $this->user,
        );

        session()->flash('message', 'Score recorded.');

        return $this->redirect(route('account.home'));
    }

    public function recordScoreManually()
    {
        $this->validate([
            'date'        => [new DateMustBeValid($this->boardNumber)], // passes if board number provided
            'boardNumber' => [new BoardNumberMustBeValid($this->date)],
            'score'       => ['required_without:bricked'],
        ]);

        app(ScoreRecorder::class)->record($this->recordedUser(), [
            'score'        => $this->bricked ? 7 : $this->score,
            'boardNumber'  => $this->boardNumber ?? app(WordleBoard::class)->getBoardNumberFromDate($this->date),
            'date'         => $this->date,
            'hardMode'     => $this->hardMode ?? false,
            'botSkillScore' => $this->botSkillScore ?: null,
            'botLuckScore'  => $this->botLuckScore ?: null,
        ], $this->user);

        session()->flash('message', 'Score recorded.');

        return $this->redirect(route('account.home'));
    }

    public function updatedDate($date)
    {
        $this->boardNumber = app(WordleBoard::class)->getBoardNumberFromDate(Carbon::parse($date));

        if(!$this->boardNumber) {
            $this->dispatch('notify', [
                'type' => 'error',
                'content' => 'Cannot use a date from the future.'
            ]);
        }
    }

    public function updatedRecordForUserId($userId)
    {
        $this->recordingForSelf = (int)$userId === Auth::user()->id;
    }

    protected function recordedUser(): User
    {
        if ((int) $this->recordForUserId === $this->user->id) {
            return $this->user;
        }

        return User::query()->findOrFail($this->recordForUserId);
    }

    public function render()
    {
        return view('livewire.score.record-form');
    }
}
