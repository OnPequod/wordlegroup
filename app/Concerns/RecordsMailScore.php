<?php

namespace App\Concerns;

use App\Models\MailScoreMessage;
use App\Models\User;
use App\Services\ScoreRecorder;
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

            app(ScoreRecorder::class)->recordFromBoard(
                $user,
                $parser->getMessageBody('text'),
                $user,
            );
        } catch (\Exception $e) {
            return;
        }
    }
}
