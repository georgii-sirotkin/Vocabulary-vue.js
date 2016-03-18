<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\WordService;
use App\Word;
use Illuminate\Http\Request;

class WordsController extends Controller
{
    private $wordService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(WordService $wordService)
    {
        $this->middleware('auth');
        $this->wordService = $wordService;
    }

    /**
     * Display words.
     *
     * @param  Request $request
     * @return Illuminate\View\View
     */
    public function index(Request $request)
    {
        if ($request->has('search')) {
            return 'search results';
        }
        return Word::lists('word');
    }

    /**
     * Show the form for adding a new word.
     *
     * @return Illuminate\View\View
     */
    public function create()
    {
        if ($this->hasOldInput()) {
            $definitions = $this->wordService->getDefinitionsFromOldInput();
        } else {
            $definitions = [];
        }

        return view('words.create', ['definitions' => $definitions, 'buttonName' => 'Add word']);
    }

    /**
     * Store a new word in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = $this->wordService->getValidator($request);
        if ($validator->fails()) {
            return redirect()->route('add_word')
                ->withErrors($validator)
                ->withInput();
        }

        $this->wordService->storeWord($request);

        return redirect()->route('words');
    }

    /**
     * Display word.
     *
     * @param  string  $slugOrId
     * @return Illuminate\View\View
     */
    public function show($slugOrId)
    {
        $word = Word::findBySlugOrIdOrFail($slugOrId);
        $word->load('definitions');
        return $word;
    }

    /**
     * Show the form for editing the specified word.
     *
     * @param  string  $slugOrId
     * @return Illuminate\View\View
     */
    public function edit($slugOrId)
    {
        $word = Word::findBySlugOrIdOrFail($slugOrId);
        if ($this->hasOldInput()) {
            $definitions = $this->wordService->getDefinitionsFromOldInput();
        } else {
            $definitions = $word->definitions;
        }
        return view('words.edit', ['word' => $word, 'definitions' => $definitions, 'buttonName' => 'Save']);
    }

    /**
     * Update the specified word in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $slugOrId
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slugOrId)
    {
        $word = Word::findBySlugOrIdOrFail($slugOrId);

        $validator = $this->wordService->getValidator($request, $word->id);
        if ($validator->fails()) {
            return redirect()->route('edit_word', [$word->slug])
                ->withErrors($validator)
                ->withInput();
        }

        $this->wordService->updateWord($request, $word);

        return redirect()->route('words');
    }

    /**
     * Remove the specified word from storage.
     *
     * @param  string  $slugOrId
     * @return \Illuminate\Http\Response
     */
    public function destroy($slugOrId)
    {
        $word = Word::findBySlugOrIdOrFail($slugOrId);

        $this->wordService->deleteWord($word);

        return redirect()->route('words');
    }

    /**
     * Determine if request contains old input definitions.
     *
     * @return boolean
     */
    private function hasOldInput()
    {
        return !is_null(old('definitions')) && is_array(old('definitions')) && !is_null(old('definitionIds')) && is_array(old('definitionIds'));
    }
}
