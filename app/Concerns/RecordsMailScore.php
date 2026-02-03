<?php

namespace App\Concerns;

use App\Models\MailScoreMessage;
use App\Models\Score;
use App\Models\User;
use PhpMimeMailParser\Parser;

class RecordsMailScore
{
    public function record(MailScoreMessage $message)
    {
        $parser = new Parser();

        try {
            // Get the email content.
            $parser->setText(base64_decode($message->message['content']));

            // Get the sending addresses.
            $sender = collect($parser->getAddresses('from'))->first();
            // If we can't find one, escape.
            if (!$sender || !isset($sender['address'])) {
                return;
            }

            // Get the email. If we can't find a user, escape.
            $senderEmail = $sender['address'];
            $user = User::where('email', $senderEmail)->first();

            if(! $user) {
                return;
            }

            // Get the message text and parse the board.
            $emailText = $parser->getMessageBody('text');

            $board = app(WordleBoard::class)->parse($emailText);

            // Record the board.
            Score::create([
                'user_id'           => $user->id,
                'recording_user_id' => $user->id,
                'date'              => $board['date'],
                'score'             => $board['scoreNumber'],
                'board_number'      => $board['boardNumber'],
                'board'             => $board['board'] ?? null,
                'hard_mode'         => $board['hardMode'] ?? null,
                'bot_skill_score'   => $board['botScores']['skill'] ?? null,
                'bot_luck_score'    => $board['botScores']['luck'] ?? null,
            ]);
        } catch (\Exception $e) {
            return;
        }
    }
}
