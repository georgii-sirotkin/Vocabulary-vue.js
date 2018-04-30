<?php

namespace App\Http\Controllers;

use App\Http\Requests\WordRequest;
use App\Repositories\WordRepository;
use App\Services\WordService;
use App\Word;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class WordsController extends Controller
{
    /**
     * @var WordService
     */
    private $wordService;

    /**
     * Create a new controller instance.
     *
     * @param WordService $wordService
     */
    public function __construct(WordService $wordService)
    {
        $this->wordService = $wordService;
    }

    /**
     * Display words or search results.
     *
     * @param  Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request, WordRepository $wordRepository)
    {
        if ($request->exists('search')) {
            $searchString = $request->input('search');
            $words = $searchString ? $wordRepository->findWords($searchString) : collect();
            return view('words.search', compact('words', 'searchString'));
        }

        $words = Word::orderBy('title', 'asc')->paginate(config('settings.number_of_words_on_one_page'));
        return view('words.index', compact('words'));
    }

    /**
     * Show the form for adding a new word.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('words.create');
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

        return redirect()->route('words.index');
    }

    /**
     * Display word.
     *
     * @param Word $word
     * @return \Illuminate\View\View
     */
    public function show(Word $word)
    {
        return view('words.view', compact('word'));
    }

    /**
     * Show the form for editing the specified word.
     *
     * @param Word $word
     * @return \Illuminate\View\View
     */
    public function edit(Word $word)
    {
        if ($this->anyDefinitionsInOldInput()) {
            $definitions = $this->getDefinitionsFromOldInput();
        } else {
            $definitions = $word->definitions;
        }

        return view('words.edit', compact('word', 'definitions'));
    }

    /**
     * Update the specified word.
     *
     * @param  WordRequest $request
     * @param Word $word
     * @return \Illuminate\Http\Response
     */
    public function update(WordRequest $request, Word $word)
    {
        $this->wordService->updateWord($word, $request);

        return redirect()->route('words.index');
    }

    /**
     * Remove the specified word from storage.
     *
     * @param Word $word
     * @return \Illuminate\Http\Response
     */
    public function destroy(Word $word)
    {
        $this->wordService->deleteWord($word);

        return redirect()->route('words.index');
    }

    /**
     * Determine if request contains old input definitions.
     *
     * @return boolean
     */
    private function anyDefinitionsInOldInput()
    {
        return !is_null(old('definitions')) && is_array(old('definitions'));
    }

    /**
     * Get Collection of Definition objects from old input.
     *
     * @return Collection
     */
    private function getDefinitionsFromOldInput()
    {
        return collect($this->wordService->getDefinitionObjects(old('definitions')));
    }
}
