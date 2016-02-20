<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Validator;

class WordsController extends Controller
{
    private $image;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ImageService $image)
    {
        $this->middleware('auth');
        $this->image = $image;
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
        return view('words.create');
    }

    /**
     * Store a new word in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        \DB::enableQueryLog();

        $validator = $this->validator($request);
        if ($validator->fails()) {
            return redirect(route('add_word'))
                ->withErrors($validator)
                ->withInput();
        }

        $request->user()->words()->create(
            $request->all());
        // dd(\DB::getQueryLog());
        // return 'passed validation';

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

    /**
     * Get validator.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Validator
     */
    private function validator(Request $request)
    {
        $userId = $request->user()->id;

        $messages = [
            'word.unique' => 'You have already added this word.',
            'image.required_without_all' => 'Image is required when no definitions are given.',
        ];

        $validator = Validator::make($request->all(), [
            'word' => "required|unique:word,word,NULL,id,user_id,{$userId}|max:255",
            'image' => "required_without_all:definitions.0,imageUrl",
            'imageUrl' => 'url',
        ], $messages);

        $validator->after(array($this->image, 'validateImage'));

        return $validator;
    }
}
