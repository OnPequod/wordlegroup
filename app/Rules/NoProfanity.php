<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NoProfanity implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        $variations = $this->normalize($value);
        $words = config('profanity.words', []);

        foreach ($words as $word) {
            foreach ($variations as $normalized) {
                if ($this->containsWord($normalized, $word)) {
                    $fail('This contains inappropriate language.');
                    return;
                }
            }
        }
    }

    /**
     * Normalize text and return variations to check.
     *
     * @return array<string>
     */
    protected function normalize(string $text): array
    {
        $text = mb_strtolower($text);
        $text = strtr($text, config('profanity.substitutions', []));

        // Return both variations: reduced to 1 char and reduced to 2 chars
        // This catches both "fuuuck" -> "fuck" and "assshole" -> "asshole"
        return [
            preg_replace('/(.)\1{2,}/', '$1', $text),    // Reduce to 1
            preg_replace('/(.)\1{2,}/', '$1$1', $text),  // Reduce to 2
        ];
    }

    /**
     * Check if normalized text contains the profane word.
     */
    protected function containsWord(string $text, string $word): bool
    {
        // Check for exact match or word boundary match
        // Using \b for word boundaries, but also check without for embedded profanity
        return preg_match('/\b' . preg_quote($word, '/') . '\b/', $text) === 1
            || str_contains($text, $word);
    }
}
