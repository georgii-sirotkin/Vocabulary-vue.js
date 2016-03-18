<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\RandomWordService;

class RandomWordsController extends Controller
{
    private $randomWordService;

    /**
     * Create a new controller instance.
     *
     * @param RandomWordService $randomWordService
     */
    public function __construct(RandomWordService $randomWordService)
    {
        $this->middleware('auth');
        $this->randomWordService = $randomWordService;
    }

    /**
     * Display random word.
     *
     * @return Illuminate\View\View
     */
    public function randomWord()
    {
        return $this->randomWordService->getRandomWord();
    }

    /**
     * Get a new random word.
     *
     * @return Word
     */
    public function nextRandomWord()
    {
        return $this->randomWordService->getNewRandomWord();
    }

    public function check()
    {

    }
}
