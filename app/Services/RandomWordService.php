<?php

namespace App\Services;

use App\DataStructures\RingBuffer;
use App\Exceptions\NoWordsException;
use App\Repositories\WordRepository;
use App\Word;
use Illuminate\Session\SessionManager;

class RandomWordService
{
    private $repository;
    private $session;

    /**
     * Ids of words that were recently returned by getNewRandomWord method.
     *
     * @var RingBuffer
     */
    private $mostRecentWordIds;

    /**
     * Create a new instance of RandomWordService.
     *
     * @param WordRepository $repository
     * @param SessionManager $session
     * @param int         $numberOfWordsToRemember The number of most recent words (words that were recently returned by getNewRandomWord method) to remember.
     */
    public function __construct(WordRepository $repository, SessionManager $session, $numberOfWordsToRemember)
    {
        $this->repository = $repository;
        $this->session = $session;
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
        try {
            $newRandomWord = $this->repository->getRandomWord($this->mostRecentWordIds->getNonemptyElements());
        } catch (NoWordsException $e) {
            return null;
        }

        $this->session->put('mostRecentWordHasBeenChecked', false);
        $this->rememberWordId($newRandomWord->id);

        return $newRandomWord;
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
     * Get the most recent random word.
     *
     * @return Word
     */
    private function getMostRecentWord()
    {
        $mostRecentWordId = $this->mostRecentWordIds->top();
        return Word::findOrFail($mostRecentWordId);
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
        return !is_null($this->mostRecentWordIds->top()) && !$this->session->get('mostRecentWordHasBeenChecked') && $this->mostRecentWordExists();
    }

    /**
     * Determine if the most recent word exists.
     * 
     * @return bool
     */
    private function mostRecentWordExists()
    {
        return Word::where('id', $this->mostRecentWordIds->top())->exists();
    }
}
