<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\WordRequest;
use App\Repositories\WordRepository;
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
     * Display words or search results.
     *
     * @param  Request $request
     * @return Illuminate\View\View
     */
    public function index(Request $request, WordRepository $wordRepository)
    {
        if ($request->has('search')) {
            return $wordRepository->findWords($request->input('search'));
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
            $definitions = $this->getDefinitionsFromOldInput();
        } else {
            $definitions = [];
        }

        return view('words.create', ['definitions' => $definitions]);
    }

    /**
     * Store a new word in storage.
     *
     * @param  WordRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(WordRequest $request)
    {
        $this->wordService->storeWord($request);

        return redirect()->route('words');
    }

    /**
     * Display word.
     *
     * @param  string  $slug
     * @return Illuminate\View\View
     */
    public function show($slug)
    {
        $word = Word::findBySlugOrFail($slug);
        $word->load('definitions'); /// ???
        return $word;
    }

    /**
     * Show the form for editing the specified word.
     *
     * @param  string  $slug
     * @return Illuminate\View\View
     */
    public function edit($slug)
    {
        $word = Word::findBySlugOrFail($slug);
        if ($this->hasOldInput()) {
            $definitions = $this->getDefinitionsFromOldInput();
        } else {
            $definitions = $word->definitions;
        }
        return view('words.edit', ['word' => $word, 'definitions' => $definitions]);
    }

    /**
     * Update the specified word.
     *
     * @param  WordRequest  $request
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function update(WordRequest $request, $slug)
    {
        $this->wordService->updateWord($request, $slug);

        return redirect()->route('words');
    }

    /**
     * Remove the specified word from storage.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug)
    {
        $this->wordService->deleteWord($slug);
        return redirect()->route('words');
    }

    /**
     * Determine if request contains old input definitions.
     *
     * @return boolean
     */
    private function hasOldInput()
    {
        return !is_null(old('definitions')) && is_array(old('definitions'));
    }

    /**
     * Get array of Definition objects from old input.
     *
     * @return array
     */
    public function getDefinitionsFromOldInput()
    {
        return $this->wordService->getDefinitionObjects(old('definitions'));
    }
}
