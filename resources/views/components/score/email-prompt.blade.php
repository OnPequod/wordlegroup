<div {{ $attributes }}>
    <p>
        The easiest way to share a score is to click "Share" on your Wordle Board, select your mail client, and send it off to <a class="link" href="mailto:scores@wordlegroup.com">scores@wordlegroup.com</a>.
    </p>
    <p class="mt-4">
        <strong>Want to include Skill and Luck scores?</strong> Share from the <a class="link" href="https://www.nytimes.com/athletic/games/wordle-bot" target="_blank" rel="noopener">NYT WordleBot</a> page instead of the main Wordle game.
    </p>
    <p class="mt-4">Make sure you send it from the account you signed up with.</p>
    <div class="mt-8" x-data="{show: window.iOS()}" x-show="show" x-cloak>
        <a href="/email/WordleGroup.vcf" role="button" class="inline-flex items-center text-sm font-semibold justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-white bg-green-600 hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-700">
            <x-icon-solid.address-card class="w-4 h-4 fill-white mr-4" />
            Add Wordle Group To Your Contacts
        </a>
    </div>
</div>
