<?php

namespace App\Http\Controllers;

use App\Services\CheckAnswerService;
use App\Services\RandomWordService;
use Illuminate\Http\Request;

class RandomWordsController extends Controller
{
    /**
     * Display random word.
     *
     * @param RandomWordService $randomWordService
     * @return Illuminate\View\View
     */
    public function randomWord(RandomWordService $randomWordService)
    {
        $word = $randomWordService->getRandomWord();

        return view('words.random')->with('word', $word);
    }

    /**
     * Get a new random word.
     * 
     * @param RandomWordService $randomWordService
     * @return Illuminate\View\View
     */
    public function nextRandomWord(RandomWordService $randomWordService)
    {
        $word = $randomWordService->getNewRandomWord();

        return view('words.partials.random')->with('word', $word);
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
