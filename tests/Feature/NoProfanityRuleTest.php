<?php

use App\Rules\NoProfanity;
use Illuminate\Support\Facades\Validator;

it('passes for clean text', function () {
    $rule = new NoProfanity;

    expect(passes($rule, 'Hello World'))->toBeTrue();
    expect(passes($rule, 'WordleMaster'))->toBeTrue();
    expect(passes($rule, 'Player123'))->toBeTrue();
    expect(passes($rule, 'GreenSquares'))->toBeTrue();
});

it('passes for empty or null values', function () {
    $rule = new NoProfanity;

    expect(passes($rule, ''))->toBeTrue();
    expect(passes($rule, null))->toBeTrue();
});

it('fails for basic profanity', function () {
    $rule = new NoProfanity;

    expect(passes($rule, 'fuck'))->toBeFalse();
    expect(passes($rule, 'shit'))->toBeFalse();
    expect(passes($rule, 'asshole'))->toBeFalse();
});

it('fails for profanity in mixed case', function () {
    $rule = new NoProfanity;

    expect(passes($rule, 'FUCK'))->toBeFalse();
    expect(passes($rule, 'Shit'))->toBeFalse();
    expect(passes($rule, 'AsSHoLe'))->toBeFalse();
});

it('fails for profanity embedded in text', function () {
    $rule = new NoProfanity;

    expect(passes($rule, 'whatthefuck'))->toBeFalse();
    expect(passes($rule, 'holy shit dude'))->toBeFalse();
    expect(passes($rule, 'Player_fuck_123'))->toBeFalse();
});

it('fails for leetspeak substitutions', function () {
    $rule = new NoProfanity;

    expect(passes($rule, 'f*ck'))->toBeFalse();
    expect(passes($rule, 'sh1t'))->toBeFalse();
    expect(passes($rule, 'a$$'))->toBeFalse();
    expect(passes($rule, 'f@ggot'))->toBeFalse();
    expect(passes($rule, 'n1gger'))->toBeFalse();
});

it('fails for repeated characters', function () {
    $rule = new NoProfanity;

    expect(passes($rule, 'fuuuuck'))->toBeFalse();
    expect(passes($rule, 'shiiiiit'))->toBeFalse();
    expect(passes($rule, 'assshole'))->toBeFalse();
});

it('fails for racial slurs', function () {
    $rule = new NoProfanity;

    expect(passes($rule, 'nigger'))->toBeFalse();
    expect(passes($rule, 'chink'))->toBeFalse();
    expect(passes($rule, 'spic'))->toBeFalse();
    expect(passes($rule, 'kike'))->toBeFalse();
});

it('fails for homophobic slurs', function () {
    $rule = new NoProfanity;

    expect(passes($rule, 'faggot'))->toBeFalse();
    expect(passes($rule, 'dyke'))->toBeFalse();
    expect(passes($rule, 'tranny'))->toBeFalse();
});

it('fails for ableist slurs', function () {
    $rule = new NoProfanity;

    expect(passes($rule, 'retard'))->toBeFalse();
    expect(passes($rule, 'retarded'))->toBeFalse();
    expect(passes($rule, 'spastic'))->toBeFalse();
});

/**
 * Helper to check if a value passes the validation rule.
 */
function passes(NoProfanity $rule, ?string $value): bool
{
    $failed = false;

    $rule->validate('field', $value ?? '', function () use (&$failed) {
        $failed = true;
    });

    return !$failed;
}
