<x-layout.page-container title="Rules &amp; Frequently Asked Questions - Wordle Group" heading="Rules & Frequently Asked Questions">
    <x-layout.social-meta
        title="Rules &amp; Frequently Asked Questions - Wordle Group"
        :url="route('rules-and-faq')"
        description="Information on how scores are calculated and shared in a Wordle Group."
    />
    <x-layout.json-ld :schema="[
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'name' => 'Rules & Frequently Asked Questions',
        'description' => 'Information on how scores are calculated and shared in a Wordle Group.',
        'url' => route('rules-and-faq'),
        'mainEntity' => [
            [
                '@type' => 'Question',
                'name' => 'Can I submit scores from before I joined a group?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => 'No. We believe everyone in life deserves a fresh start. Competition starts the day you join a group. Only scores from the day you join a group, or after, will count.',
                ],
            ],
            [
                '@type' => 'Question',
                'name' => 'I emailed my scores to scores@wordlegroup.com and it didn\'t record. Why not?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => 'Make sure you are emailing us from the same account you signed up with. Otherwise we won\'t know who to attach the score with.',
                ],
            ],
            [
                '@type' => 'Question',
                'name' => 'Why can\'t I see other people\'s boards from today\'s Wordle?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => 'Seeing other people\'s boards provides you an advantage. For this reason, on the day a Wordle puzzle is active, we hide the board attached to someone\'s score submission for anyone you share a group with. Once you submit your score, you can see the board.',
                ],
            ],
            [
                '@type' => 'Question',
                'name' => 'Who can see my profile and stats?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => 'If your profile is set to private in your Account Settings page, only you and members of your groups can see your stats and profile.',
                ],
            ],
        ],
        'isPartOf' => ['@id' => url('/') . '#website'],
    ]" />
    <div class="prose tracking-tight max-w-none rounded-2xl border border-gray-100 bg-white p-6 sm:p-8 shadow-[0_10px_30px_var(--color-shadow)]">
        <p>If something appears not to be working correctly or works differently from how you expect, please read the below question and answers. If we don't answer your question below, you disagree with how we do things, or something seems broken, please <a href="mailto:erik@pequod.sh">email us</a>.</p>
        <ul class="mt-8">
            <li>
                <span class="font-bold leading-5">Can I submit scores from before I joined a group?</span>

                <p>No. We believe everyone in life deserves a fresh start. Competition starts the day you join a group. Only scores from the day you join a group, or after, will count. (We also want to prevent people from juking the stats by submitting a bunch of old boards.)</p>
            </li>
            <li>
                <span class="font-bold leading-5">I emailed my scores to <a href="mailto:scores@wordlegroup.com">scores@wordlegroup.com</a> and it didn't record. Why not?</span>

                <p>Make sure you are emailing us from the same account you signed up with. Otherwise we won't know who to attach the score with.</p>
            </li>
            <li>
                <span class="font-bold leading-5">Why can't I see other people's boards from today's Wordle?</span>

                <p>Seeing other people's boards provides you an advantage. For this reason, on the day a Wordle puzzle is active, we hide the board attached to someone's score submission for anyone you share a group with. Once you submit your score, you can see the board.</p>
            </li>
            <li>
                <span class="font-bold leading-5">How can I let someone see my board without them submitting a score?</span>

                <p>If you share your score using the Share buttons on a score page, we'll let others see it, even if they're in your group. If someone's not in any groups with you, you can set your profile to Public on the <a href="{{ route('account.settings') }}">Account Settings</a> page, and people will be able to see your activity.</p>
            </li>
            <li>
                <span class="font-bold leading-5">Who can see my profile and stats?</span>

                <p>If your profile is set to <span class="font-bold">private</span> on your <a href="{{ route('account.settings') }}">Account Settings</a> page, only you and members of your groups can see your stats and profile.</p>
            </li>
            <li>
                <span class="font-bold leading-5">Who can see the scores page associated with each submission?</span>

                <p>If your profile is set to <span class="font-bold">private</span> on your <a href="{{ route('account.settings') }}">Account Settings</a> page, only you and members of your groups can see the scores page. However, if you share a score by clicking the share buttons on the page, it will be made public; the rest of your profile will remain private.</p>
            </li>
            <li>
                <span class="font-bold leading-5">I'm getting my ass kicked. Can we reset the stats?</span>

                <p>No. But there will soon be a "Seasons" feature, where group administrators can start a new "season," where stats get calculated only for a specific period of time.</p>
            </li>

        </ul>

    </div>
</x-layout.page-container>
