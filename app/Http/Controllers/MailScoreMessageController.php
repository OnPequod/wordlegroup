<?php

namespace App\Http\Controllers;

use App\Events\ScoreEmailReceived;
use App\Models\MailScoreMessage;
use Aws\Sns\Exception\InvalidSnsMessageException;
use Aws\Sns\Message;
use Aws\Sns\MessageValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MailScoreMessageController extends Controller
{
    public function __invoke(Request $request)
    {
        // Use fromJsonString instead of fromRawPostData to avoid dependency
        // on X-Amz-Sns-Message-Type header which may not pass through proxies
        try {
            $message = Message::fromJsonString($request->getContent());
        } catch (\RuntimeException $e) {
            // Invalid JSON or malformed request
            abort(400, 'Invalid request');
        }

        $validator = new MessageValidator();

        // Validate the message and log errors if invalid.
        try {
            $validator->validate($message);
        } catch (InvalidSnsMessageException $e) {
            // Pretend we're not here if the message is invalid.
            abort(404);
        }

        // Check the type of the message and handle the subscription.
        if ($message['Type'] === 'SubscriptionConfirmation') {
            return Http::get($message['SubscribeURL']);
        }

        // Record the message
        try {
            $mailMessage = MailScoreMessage::create([
                'message' => json_decode(request()->json()->get('Message')),
            ]);

            event(new ScoreEmailReceived($mailMessage));
        } catch(\Exception $e) {
            abort(400);
        }
    }
}
