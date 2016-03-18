<?php

namespace App\Repositories;

use App\Exceptions\NoWordsException;
use App\Word;
use Illuminate\Database\Eloquent\Builder;

class WordRepository
{
    /**
     * Get random word.
     *
     * @return Word|null
     */
    public function getRandomWord(array $mostRecentWordIds = array())
    {
        $query = $this->getQueryForFindingRandomWord($mostRecentWordIds);

        $word = $query->first();

        if (is_null($word)) {
            throw new NoWordsException;
        }

        return $word;
    }

    /**
     * Get random number between the minimum and the maximum number of right guesses (inclusive).
     *
     * @return int|null
     */
    private function getRandomNumberOfRightGuesses()
    {
        $result = Word::selectRaw('MIN(right_guesses_number) AS minNumberOfRightGuesses, MAX(right_guesses_number) AS maxNumberOfRightGuesses')->toBase()->first();

        if (is_null($result->minNumberOfRightGuesses) || is_null($result->maxNumberOfRightGuesses)) {
            throw new NoWordsException;
        }

        return rand($result->minNumberOfRightGuesses, $result->maxNumberOfRightGuesses);
    }

    /**
     * Get query for finding random word.
     *
     * @param  array  $mostRecentWordIds
     * @return Illuminate\Database\Eloquent\Builder
     */
    private function getQueryForFindingRandomWord(array $mostRecentWordIds)
    {
        $randomNumberOfRightGuesses = $this->getRandomNumberOfRightGuesses();

        $query = Word::where('right_guesses_number', '<=', $randomNumberOfRightGuesses);

        if (!empty($mostRecentWordIds)) {
            $this->makeMostRecentWordsUnlikelyToBeFound($query, $mostRecentWordIds);
        }

        return $query->orderByRaw('RAND()');
    }

    /**
     * Make most recent words (words that were recently returned by getRandomWord method) unlikely to be found.
     *
     * @param  Builder $query
     * @param  array   $mostRecentWordIds
     * @return Illuminate\Database\Eloquent\Builder
     */
    private function makeMostRecentWordsUnlikelyToBeFound(Builder $query, array $mostRecentWordIds)
    {
        return $query->orderByRaw('id IN (' . implode(',', $mostRecentWordIds) . ')');
    }
}
