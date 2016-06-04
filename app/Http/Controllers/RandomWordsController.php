<?php

namespace App\Http\Controllers;

use App\Exceptions\NoWordsException;
use App\Http\Controllers\Controller;
use App\Services\CheckAnswerService;
use App\Services\RandomWordService;
use Illuminate\Http\Request;

class RandomWordsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display random word.
     *
     * @param RandomWordService $randomWordService
     * @return Illuminate\View\View
     */
    public function randomWord(RandomWordService $randomWordService)
    {
        try {
            $word = $randomWordService->getRandomWord();
        } catch (NoWordsException $e) {
            $word = null;
        }

        return view('words.random')->with('word', $word);
    }

    /**
     * Get a new random word.
     * 
     * @param RandomWordService $randomWordService
     * @return Word
     */
    public function nextRandomWord(RandomWordService $randomWordService)
    {
        return $randomWordService->getNewRandomWord();
    }

    /**
     * Check answer.
     *
     * @param  Request $request
     * @param  CheckAnswerService $answerChecker
     * @return string  In JSON format
     */
    public function checkAnswer(Request $request, CheckAnswerService $answerChecker)
    {
        return $answerChecker->check($request->input('answer'));
    }
}
