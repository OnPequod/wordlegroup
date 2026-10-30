<?php

use App\Concerns\WordleBoard;

$board = "\n\n⬛⬛🟨🟨⬛\n⬛⬛🟨🟨⬛\n🟩🟨🟨🟨⬛\n🟩🟩🟩🟩🟩";

it('parses board numbers with any common thousands separator', function (string $header) use ($board) {
    $result = app(WordleBoard::class)->parse($header.$board);

    expect($result['boardNumber'])->toBe(1687);
    expect($result['scoreNumber'])->toBe(4);
    expect($result['valid'])->toBeTrue();
})->with([
    'comma' => 'Wordle 1,687 4/6',
    'period' => 'Wordle 1.687 4/6',
    'apostrophe' => "Wordle 1'687 4/6",
    'typographic apostrophe' => 'Wordle 1’687 4/6',
    'space' => 'Wordle 1 687 4/6',
    'non-breaking space' => "Wordle 1\u{00A0}687 4/6",
    'narrow non-breaking space' => "Wordle 1\u{202F}687 4/6",
    'no separator' => 'Wordle 1687 4/6',
]);

it('parses board numbers below one thousand', function () use ($board) {
    $result = app(WordleBoard::class)->parse('Wordle 959 4/6'.$board);

    expect($result['boardNumber'])->toBe(959);
});

it('parses hard mode boards with a period separator', function () use ($board) {
    $result = app(WordleBoard::class)->parse('Wordle 1.687 4/6*'.$board);

    expect($result['boardNumber'])->toBe(1687);
    expect($result['hardMode'])->toBeTrue();
});
