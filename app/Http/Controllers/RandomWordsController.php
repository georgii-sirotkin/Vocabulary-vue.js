<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\RandomWordService;
use Illuminate\Http\Request;

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

    /**
     * Check answer.
     *
     * @param  Request $request
     * @return string  In JSON format
     */
    public function checkAnswer(Request $request)
    {
        return $this->randomWordService->checkAnswer($request->input('answer'));
    }
}
