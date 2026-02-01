<?php

namespace App\Concerns;

use Carbon\Carbon;
use Illuminate\Support\Str;

class WordleBoard
{
    public $activeBoardEndTime;

    public $activeBoardNumber;

    public $activeBoardStartTime;

    public $firstBoardEndTime;

    public $firstBoardStartTime;

    public $hardMode;

    public $wordleDate;

    public function __construct()
    {
        $this->wordleDate = app(WordleDate::class);
        $this->firstBoardStartTime = $this->wordleDate->firstBoardStartTime;
        $this->firstBoardEndTime = $this->wordleDate->firstBoardEndTime;

//        $this->activeBoardStartTime = now() <= Carbon::parse('Today 06:00:00 UTC')
//            ? Carbon::parse('Yesterday 06:00:00 UTC')
//            : Carbon::parse('Today 06:00:00 UTC');
        $this->activeBoardStartTime = $this->wordleDate->getLatestTime()->startOfDay();
        $this->activeBoardEndTime = $this->activeBoardStartTime->copy()->addDays(1)->subMicrosecond();

        $this->activeBoardNumber = $this->firstBoardStartTime->copy()->diffInDays($this->activeBoardEndTime);
    }

    public function getBoardNumberFromDate($date)
    {
        $date = app(WordleDate::class)->get($date);
        $boardNumber = $this->firstBoardStartTime->copy()->diffInDays($date);

        return $this->validateBoardNumber($boardNumber)
            ? $boardNumber
            : null;
    }

    public function parse($board)
    {
        $score = $this->getScoreFromBoard($board);

        // Store fails as 7s
        if ($score) {
            $scoreNumber = strtolower($score) === 'x' ? 7 : (int)$score;
        } else {
            $scoreNumber = null;
        }

        $boardNumber = $this->getBoardNumberFromBoard($board);
        $date = $this->wordleDate->getDateFromBoardNumber($boardNumber);
        $hardMode = $this->getHardModeFromBoard($board);
        $botScores = $this->getBotScoresFromBoard($board);
        $board = $this->extractBoard($board, $scoreNumber);

        $valid = $score !== null && $boardNumber !== null && $date !== null;

        return compact('score', 'scoreNumber', 'boardNumber', 'date', 'hardMode', 'valid', 'board', 'botScores');
    }

    public function getBotScoresFromBoard($board)
    {
        $botSkillScore = null;
        $botLuckScore = null;

        // Match WordleBot skill score: "Skill 93/99" or "Skill: 93"
        if (preg_match('/Skill[:\s]+(\d+)(?:\/\d+)?/i', $board, $matches)) {
            $botSkillScore = (int) $matches[1];
            if ($botSkillScore < 0 || $botSkillScore > 99) {
                $botSkillScore = null;
            }
        }

        // Match WordleBot luck score: "Luck 83/99" or "Luck: 83"
        if (preg_match('/Luck[:\s]+(\d+)(?:\/\d+)?/i', $board, $matches)) {
            $botLuckScore = (int) $matches[1];
            if ($botLuckScore < 0 || $botLuckScore > 99) {
                $botLuckScore = null;
            }
        }

        return [
            'skill' => $botSkillScore,
            'luck' => $botLuckScore,
        ];
    }

    public function getScoreFromBoard($board)
    {
        preg_match_all('/(\d|x|X)\/6/', $board, $matches);

        return in_array($matches[1][0] ?? null, [1, 2, 3, 4, 5, 6, 'X', 'x'])
            ? $matches[1][0]
            : null;
    }

    public function getHardModeFromBoard($board)
    {
        preg_match_all('/(\d|x|X)\/6(\*)/', $board, $matches);

        $hardMode = $matches[2][0] ?? null;

        return $hardMode === '*';
    }

    public function getBoardNumberFromBoard($board)
    {
        preg_match_all('/(\d+|\d+,\d+)(\s.+|\s)(\d|x|X)\/6/', $board, $matches);

        $boardNumberCandidate = Str::replace(',', '', $matches[1][0]);
        $boardNumber = is_numeric($boardNumberCandidate ?? null)
            ? (int) $boardNumberCandidate
            : null;

        return $this->validateBoardNumber($boardNumber)
            ? $boardNumber
            : null;
    }

    public function extractBoard($entry, $score)
    {
        preg_match_all('/([⬜🟨🟩⬛].*[⬜🟨🟩⬛])/s', $entry, $matches);

        if (!isset($matches[1][0])) {
            return null;
        }

        $board = $matches[1][0];

        $board = $this->getBoardFromString($board, $score);

        if ($this->boardHasCorrectLineBreaks($board, $score)) {
            return $board;
        }

        return $this->attemptBoardRepairForMissingLineBreaks($board);
    }

    public function getBoardFromString($board, $score)
    {
        // Get only the wordle characters
        $board = trim(preg_replace("/[^⬜🟨🟩⬛]/su", "", $board));

        return collect(mb_str_split($board, 5))->take($score)->implode("\r\n");
    }

    public function chunkSplitUnicode($str, $l = 76, $e = "\r\n")
    {
        $tmp = array_chunk(
            preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY), $l);
        $str = "";
        foreach ($tmp as $t) {
            $str .= join("", $t) . $e;
        }

        return $str;
    }

    public function attemptBoardRepairForMissingLineBreaks($board)
    {
        preg_match_all('/([^\s]{5})(\s+)/', $board, $matches);

        // If we can't match the spaces, give up.
        if (!isset($matches[2])) {
            return $board;
        }

        // If we can, grab the space strings, replace each with new lines
        foreach ($matches[2] as $spaceString) {
            // Replace first occurrence of that string.
            $pos = strpos($board, $spaceString);
            if ($pos !== false) {
                $board = substr_replace($board, "\n", $pos, strlen($spaceString));
            }
        }

        return $board;
    }

    public function boardHasCorrectLineBreaks($board, $score)
    {

        // Should have at least Score - 1 line breaks
        return Str::substrCount($board, "\n") === (int)($score - 1);
    }

    public function validateBoardNumber($boardNumber)
    {
        return $boardNumber >= 0 && $boardNumber <= $this->activeBoardNumber;
    }

    public function validateWordleDate($date)
    {
        return $date > $this->firstBoardStartTime && $date <= $this->activeBoardEndTime;
    }


    public function getBoardsBetween(Carbon $startDate, Carbon $endDate)
    {
        $firstBoardDate = app(WordleDate::class)->get($startDate);

        // If end is after today, take today's active board.
        $lastBoardDate = min(app(WordleDate::class)->getActiveBoardEndTime(), app(WordleDate::class)->get($endDate));

        $firstBoard = $this->getBoardNumberFromDate($firstBoardDate);
        $lastBoard = $this->getBoardNumberFromDate($lastBoardDate);

        return collect(range($firstBoard, $lastBoard, 1))
            ->filter(function($boardNumber) {
               return $this->validateBoardNumber($boardNumber);
            });
    }

    public function getStartAndEndBoardsFromDates($startDate = null, $endDate = null)
    {
        // Start date = board date or first board start time.
        if(!$startDate) {
            $startDate = app(WordleDate::class)->getFirstBoardStartTime();
        }

        $startDate = app(WordleDate::class)->get($startDate);

        // End date = board date or active board end time.
        $endDate = min(app(WordleDate::class)->get($endDate), app(WordleDate::class)->getActiveBoardEndTime());

        $startBoard = app(WordleBoard::class)->getBoardNumberFromDate($startDate);
        $endBoard = app(WordleBoard::class)->getBoardNumberFromDate($endDate);

        return [$startBoard, $endBoard];
    }
}
