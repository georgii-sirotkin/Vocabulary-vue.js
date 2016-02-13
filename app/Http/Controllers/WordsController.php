<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WordsController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display words.
     *
     * @return Illuminate\View\View
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for adding a new word.
     *
     * @return Illuminate\View\View
     */
    public function create()
    {
        //
    }

    /**
     * Store a new word in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display word.
     *
     * @param  string  $word
     * @return Illuminate\View\View
     */
    public function show($word)
    {
        //
    }

    /**
     * Display random word.
     *
     * @return Illuminate\View\View
     */
    public function randomWord()
    {

    }

    /**
     * Show the form for editing the specified word.
     *
     * @param  string  $word
     * @return Illuminate\View\View
     */
    public function edit($word)
    {
        //
    }

    /**
     * Update the specified word in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $word
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $word)
    {
        //
    }

    /**
     * Remove the specified word from storage.
     *
     * @param  string  $word
     * @return \Illuminate\Http\Response
     */
    public function destroy($word)
    {
        //
    }
}
