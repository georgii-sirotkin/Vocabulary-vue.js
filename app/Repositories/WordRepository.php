<?php

namespace App\Repositories;

use App\Exceptions\NoWordsException;
use App\Word;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class WordRepository
{
    /**
     * Minimum number of characters per one spelling mistake (levenshtein distance of 1) to consider a word similar when searching.
     * 
     * @var int
     */
    private $minNumberOfCharactersPerOneMistake;

    /**
     * Create a new instance of WordRepository.
     * 
     * @param int $minNumberOfCharactersPerOneMistake
     */
    public function __construct($minNumberOfCharactersPerOneMistake)
    {
        $this->minNumberOfCharactersPerOneMistake = $minNumberOfCharactersPerOneMistake;
    }

    /**
     * Get random word.
     *
     * @return Word
     */
    public function getRandomWord(array $mostRecentWordIds = array())
    {
        $query = $this->getQueryForFindingRandomWord($mostRecentWordIds);

        return $query->first();
    }

    /**
     * Find words.
     *
     * @param  string $searchString
     * @return Collection
     */
    public function findWords($searchString)
    {
        $words = $this->getWordsThatMatchExactly($searchString);

        if (!$words->isEmpty()) {
            return $words;
        }

        return $this->getSimilarWords($searchString);
    }

    /**
     * Get words that match exactly.
     *
     * @param  string $searchString
     * @return Collection
     */
    private function getWordsThatMatchExactly($searchString)
    {
        return Word::where('title', 'like', "%{$searchString}%")->get();
    }

    /**
     * Get similar words using fuzzy string searching.
     *
     * @param  string $searchString
     * @return Collection
     */
    private function getSimilarWords($searchString)
    {
        $allSimilarWords = new Collection();

        Word::chunk(1000, function ($words) use (&$allSimilarWords, $searchString) {
            $similarWords = $this->filterDissimilarWords($words, $searchString);
            $allSimilarWords = $allSimilarWords->concat($similarWords);
        });

        return $allSimilarWords;
    }

    /**
     * Filter dissimilar words.
     * Remove words that are not similar to the search string.
     *
     * @param  Collection $words
     * @param  string     $searchString
     * @return Collection
     */
    private function filterDissimilarWords(Collection $words, $searchString)
    {
        return $words->filter(function ($word) use ($searchString) {
            return $this->areWordsSimilar($searchString, $word->title);
        });
    }

    /**
     * Determine if words are similar.
     *
     * @param  [type] $searchString
     * @param  [type] $word
     * @return [type]
     */
    private function areWordsSimilar($searchString, $word)
    {
        if ($this->isWordTooShortOrTooLong($searchString, $word)) {
            return false;
        }

        $levenshteinDistance = levenshtein($searchString, $word);

        return floor(strlen($searchString) / $this->minNumberOfCharactersPerOneMistake) >= $levenshteinDistance;
    }

    /**
     * Determine if word is too short or too long in comparison with the search string.
     *
     * @param  [type]  $searchString
     * @param  [type]  $word
     * @return boolean
     */
    private function isWordTooShortOrTooLong($searchString, $word)
    {
        return abs(strlen($searchString) - strlen($word)) > strlen($searchString) / $this->minNumberOfCharactersPerOneMistake;
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
