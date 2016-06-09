<?php

namespace App\Services;

use App\Word;
use Illuminate\Session\SessionManager;

class CheckAnswerService
{
    /**
     * Status codes returned by checkAnswer() method.
     */
    const INCORRECT_ANSWER = 0;
    const CORRECT_ANSWER = 1;
    const CORRECT_ANSWER_WITH_SPELLING_MISTAKES = 2;
    const NO_ANSWER = 3;

    private $session;
    /**
     * Minimum number of characters per one spelling mistake (levenshtein distance of 1) to consider answer correct.
     * 
     * @var int
     */
    private $minNumberOfCharsPerOneMistake;

    /**
     * Create a new instance of CheckAnswerService.
     * 
     * @param SessionManager $session
     * @param int         $minNumberOfCharsPerOneMistake  Minimum number of characters per one spelling mistake (levenshtein distance of 1) to consider answer correct.
     */
    public function __construct(SessionManager $session, $minNumberOfCharsPerOneMistake)
    {
        $this->session = $session;
        $this->minNumberOfCharsPerOneMistake = $minNumberOfCharsPerOneMistake;
    }

    /**
     * Check answer.
     *
     * @param  string $answer
     * @return string In JSON format
     */
    public function check($answer)
    {
        $mostRecentWord = $this->getMostRecentWord();

        $statusCode = $this->getStatusCode($mostRecentWord->word, $answer);

        $this->changeNumberOfRightGuesses($mostRecentWord, $statusCode);

        $this->session->put('mostRecentWordHasBeenChecked', true);

        return $this->createResponse($mostRecentWord->word, $statusCode);
    }

   /**
     * Get the most recent random word.
     *
     * @return Word
     */
    private function getMostRecentWord()
    {
		if ($this->session->get('mostRecentWordHasBeenChecked') || is_null($mostRecentWordId = $this->getMostRecentWordId())) {
            throw new \Exception('Something went wrong');
        }

        return Word::findOrFail($mostRecentWordId);
    }

    /**
     * Get the most recent random word id.
     * 
     * @return int|null
     */
    private function getMostRecentWordId()
    {
        return is_null($this->session->get('mostRecentWordIds')) ? null : $this->session->get('mostRecentWordIds')->top();
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
            case self::NO_ANSWER:
                $word->right_guesses_number--;
                break;
            case self::CORRECT_ANSWER:
            case self::CORRECT_ANSWER_WITH_SPELLING_MISTAKES:
                $word->right_guesses_number++;
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
     * Create response to the check answer request.
     *
     * @param  string $word
     * @param  int $statusCode
     * @return array
     */
    private function createResponse($word, $statusCode)
    {
        return [
            'statusCode' => $statusCode,
            'message' => $this->getMessage($statusCode),
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
    private function getMessage($statusCode)
    {
        switch ($statusCode) {
            case self::INCORRECT_ANSWER:
                $message = "You're wrong.";
                break;
            case self::CORRECT_ANSWER:
                $message = "You're right!";
                break;
            case self::CORRECT_ANSWER_WITH_SPELLING_MISTAKES:
                $message = "You're right! But pay attention to correct spelling.";
                break;
            default:
                $message = '';
        }

        return $message;
    }
}