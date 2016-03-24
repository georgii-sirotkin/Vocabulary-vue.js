<?php

namespace App\Services;

use App\DataStructures\RingBuffer;
use App\Repositories\WordRepository;
use App\Word;
use Illuminate\Session\SessionManager;

class RandomWordService
{
    /**
     * Status codes returned by checkAnswer() method.
     */
    const INCORRECT_ANSWER = 0;
    const CORRECT_ANSWER = 1;
    const CORRECT_ANSWER_WITH_SPELLING_MISTAKES = 2;
    const NO_ANSWER = 3;

    private $repository;
    private $session;

    /**
     * Ids of words that were recently returned by getNewRandomWord method.
     *
     * @var RingBuffer
     */
    private $mostRecentWordIds;
    private $minNumberOfCharsPerOneMistake;

    /**
     * Create a new instance of RandomWordService.
     *
     * @param WordRepository $repository
     * @param SessionManager $session
     * @param int         $minNumberOfCharsPerOneMistake  Minimum number of characters per one spelling mistake (levenshtein distance of 1) to consider answer correct.
     * @param int         $numberOfWordsToRemember The number of most recent words (words that were recently returned by getNewRandomWord method) to remember.
     */
    public function __construct(WordRepository $repository, SessionManager $session, $minNumberOfCharsPerOneMistake, $numberOfWordsToRemember)
    {
        $this->repository = $repository;
        $this->session = $session;
        $this->minNumberOfCharsPerOneMistake = $minNumberOfCharsPerOneMistake;

        $this->initMostRecentWordIds($numberOfWordsToRemember);
    }

    /**
     * Get random word.
     *
     * @return Word
     */
    public function getRandomWord()
    {
        if ($this->shouldReturnMostRecentWord()) {
            return $this->getMostRecentWord();
        }

        return $this->getNewRandomWord();
    }

    /**
     * Fetch a new random word.
     *
     * @return Word
     */
    public function getNewRandomWord()
    {
        $newRandomWord = $this->repository->getRandomWord($this->mostRecentWordIds->getNonemptyElements());

        $this->session->put('mostRecentWordHaveBeenChecked', false);
        $this->rememberWordId($newRandomWord->id);

        return $newRandomWord;
    }

    /**
     * Check answer.
     *
     * @param  string $answer
     * @return string In JSON format
     */
    public function checkAnswer($answer)
    {
        $mostRecentWord = $this->getMostRecentWord();

        $statusCode = $this->getStatusCode($mostRecentWord->word, $answer);

        $this->changeNumberOfRightGuesses($mostRecentWord, $statusCode);

        $this->session->put('mostRecentWordHaveBeenChecked', true);

        return $this->createResponse($mostRecentWord->word, $statusCode);
    }

    /**
     * Initialise mostRecentWordIds property and session value with key 'mostRecentWordIds'
     *
     * @param  int $numberOfWordsToRemember
     * @return void
     */
    private function initMostRecentWordIds($numberOfWordsToRemember)
    {
        if (is_null($this->session->get('mostRecentWordIds'))) {
            $this->session->put('mostRecentWordIds', new RingBuffer($numberOfWordsToRemember));
        }

        $this->mostRecentWordIds = $this->session->get('mostRecentWordIds');
    }

    /**
     * Add wordId to mostRecentWordIds.
     *
     * @param  int $wordId
     * @return void
     */
    private function rememberWordId($wordId)
    {
        $this->mostRecentWordIds->add($wordId);
    }

    /**
     * Determine if the most recent word should be returned instead of fetching a new random word.
     *
     * @return bool
     */
    private function shouldReturnMostRecentWord()
    {
        return !is_null($this->mostRecentWordIds->top()) && !$this->session->get('mostRecentWordHaveBeenChecked');
    }

    /**
     * Get the most recent random word.
     *
     * @return Word
     */
    private function getMostRecentWord()
    {
        $mostRecentWordId = $this->mostRecentWordIds->top();

        if (is_null($mostRecentWordId)) {
            throw new \Exception('Something went wrong'); /////////////
        }

        return Word::findOrFail($mostRecentWordId);
    }

    /**
     * Get status code.
     *
     * @param  string $word
     * @param  string $answer
     * @return int
     */
    private function getStatusCode($word, $answer)
    {
        if (empty($answer)) {
            return self::NO_ANSWER;
        }

        $levenshteinDistance = levenshtein($word, $answer);

        if ($levenshteinDistance == 0) {
            return self::CORRECT_ANSWER;
        }

        if ($this->canWordBeConsideredCorrect($word, $levenshteinDistance)) {
            return self::CORRECT_ANSWER_WITH_SPELLING_MISTAKES;
        }

        return self::INCORRECT_ANSWER;
    }

    /**
     * Determine if word can be considered correct.
     * If word has a small number of spelling mistakes, it can be considered correct. It depends on the length of the word.
     *
     * @param  string $word
     * @param  int $levenshteinDistance
     * @return bool
     */
    private function canWordBeConsideredCorrect($word, $levenshteinDistance)
    {
        return floor(strlen($word) / $this->minNumberOfCharsPerOneMistake) >= $levenshteinDistance;
    }

    /**
     * Change number of right guesses for the word that is being guessed.
     *
     * @param  Word   $word
     * @param  int $statusCode
     * @return void
     */
    private function changeNumberOfRightGuesses(Word $word, $statusCode)
    {
        if (!$this->isNecessaryToChangeNumberOfRightGuesses($word, $statusCode)) {
            return;
        }

        switch ($statusCode) {
            case self::INCORRECT_ANSWER:
                $word->right_guesses_number--;
                break;
            case self::CORRECT_ANSWER:
                $word->right_guesses_number++;
                break;
            case self::CORRECT_ANSWER_WITH_SPELLING_MISTAKES:
                $word->right_guesses_number++;
                break;
            case self::NO_ANSWER:
                $word->right_guesses_number--;
                break;
        }

        $word->save();
    }

    /**
     * Determine if it's necessary to change number of right guesses for the word that is being guessed.
     *
     * @param  Word    $word
     * @param  int  $statusCode
     * @return boolean
     */
    private function isNecessaryToChangeNumberOfRightGuesses(Word $word, $statusCode)
    {
        return !(($statusCode == self::INCORRECT_ANSWER || $statusCode == self::NO_ANSWER) && $word->right_guesses_number == 0);
    }

    /**
     * Create response to check answer request.
     *
     * @param  string $word
     * @param  int $statusCode
     * @return array
     */
    private function createResponse($word, $statusCode)
    {
        return [
            'statusCode' => $statusCode,
            'message' => $this->getMessage($word, $statusCode),
            'correctAnswer' => $word,
        ];
    }

    /**
     * Get message for response to check answer request.
     *
     * @param  string $word
     * @param  int $statusCode
     * @return string
     */
    private function getMessage($word, $statusCode)
    {
        switch ($statusCode) {
            case self::INCORRECT_ANSWER:
                $message = "You're wrong. Correct answer: $word.";
                break;
            case self::CORRECT_ANSWER:
                $message = "You're right!";
                break;
            case self::CORRECT_ANSWER_WITH_SPELLING_MISTAKES:
                $message = "You're right! But pay attention to correct spelling: $word.";
                break;
            case self::NO_ANSWER:
                $message = "Correct answer: $word.";
                break;
        }

        return $message;
    }
}
