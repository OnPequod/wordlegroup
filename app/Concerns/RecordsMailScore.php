<?php

namespace App\Concerns;

use App\Models\MailScoreMessage;
use App\Models\User;
use App\Services\ScoreRecorder;
use Illuminate\Support\Facades\Log;
use PhpMimeMailParser\Parser;

class RecordsMailScore
{
    /**
     * Record a score from an inbound email.
     *
     * `processed_at` means ONE thing: this message produced a score. It is the
     * only signal that distinguishes a message that worked from one that was
     * dropped, and it is what makes pruning mail_score_messages provable rather
     * than sampled.
     *
     * Until 2026-08-22 nothing ever set it. The column existed from the original
     * migration and stayed NULL on all 153,431 rows, so "imported successfully"
     * and "silently discarded" were indistinguishable — every early return below
     * exited without a trace. Pruning the table therefore required sampling 30
     * rows by hand and matching each against `scores`.
     *
     * The failure paths now log instead of returning silently. They deliberately
     * do NOT stamp processed_at: a message that produced no score has not been
     * processed, and conflating the two would recreate exactly the ambiguity this
     * is here to remove.
     */
    public function record(MailScoreMessage $message)
    {
        // Idempotent: replaying a message must not double-record a score.
        if ($message->processed_at !== null) {
            return;
        }

        $parser = new Parser();

        try {
            // Get the email content.
            $parser->setText(base64_decode($message->message['content']));

            // Get the sending addresses.
            $sender = collect($parser->getAddresses('from'))->first();
            // If we can't find one, escape.
            if (!$sender || !isset($sender['address'])) {
                Log::warning('mail score: no sender address', ['message_id' => $message->id]);

                return;
            }

            // Get the email. If we can't find a user, escape.
            $senderEmail = $sender['address'];
            $user = User::where('email', $senderEmail)->first();

            if (! $user) {
                // Much the commonest reason a message produces no score, and it is
                // benign: people mail scores@ before they have an account. Verified
                // 2026-08-22 — both unmatched rows in a 30-row sample were emails
                // that arrived BEFORE the sender registered, one by five months.
                Log::info('mail score: no user for sender', [
                    'message_id' => $message->id,
                    'sender' => $senderEmail,
                ]);

                return;
            }

            app(ScoreRecorder::class)->recordFromBoard(
                $user,
                $parser->getMessageBody('text'),
                $user,
            );

            $message->forceFill(['processed_at' => now()])->save();
        } catch (\Exception $e) {
            Log::warning('mail score: failed to record', [
                'message_id' => $message->id,
                'exception' => $e->getMessage(),
            ]);

            return;
        }
    }
}
