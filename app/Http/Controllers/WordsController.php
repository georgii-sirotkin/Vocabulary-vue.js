<?php

namespace App\Http\Controllers;

use App\Definition;
use App\Http\Controllers\Controller;
use App\Services\ImageService;
use App\Word;
use DB;
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
        // DB::enableQueryLog(); ////////
        $validator = $this->validator($request);
        if ($validator->fails()) {
            return redirect()->route('add_word')
                ->withErrors($validator)
                ->withInput();
        }

        $word = new Word($request->all());

        if (!$this->image->isEmpty()) {
            $this->image->resizeIfNecessary();
            $this->image->save();
            $word->image_filename = $this->image->getFileName();
        }

        try {
            DB::beginTransaction();
            $request->user()->addWord($word);
            $word->addDefinitionsWithoutTouch($this->getDefinitionsFromInput($request));
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            if (!$this->image->isEmpty()) {
                $this->image->delete();
            }
            throw $e;
        }
        // dd(DB::getQueryLog()); /////
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
     * @param  string  $slugOrId
     * @return Illuminate\View\View
     */
    public function edit($slugOrId)
    {
        $word = Word::findBySlugOrIdOrFail($slugOrId);
        if ($this->hasOldInput()) {
            $definitions = $this->getDefinitionsFromOldInput();
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
        // DB::enableQueryLog(); ////////
        $validator = $this->validator($request, $word->id);
        if ($validator->fails()) {
            return redirect()->route('edit_word', [$word->slug])
                ->withErrors($validator)
                ->withInput();
        }

        $word->word = $request->input('word');

        if ($word->image_filename && (!$this->image->isEmpty() || !$request->has('keepImage'))) {
            $imageToDelete = $word->image_filename;
            $word->image_filename = null;
        }

        if (!$this->image->isEmpty()) {
            $this->image->resizeIfNecessary();
            $this->image->save();
            $word->image_filename = $this->image->getFileName();
        }

        $inputDefinitions = $this->getDefinitionsFromInput($request);
        $this->removeDefinitionsThatDoNotBelongToThisWord($inputDefinitions, $word);

        try {
            $word->save();
            $word->addDefinitionsWithTouch($inputDefinitions);
            $inputDefinitionIds = collect($inputDefinitions)->pluck('id');
            $word->definitions()->whereNotIn('id', $inputDefinitionIds)->delete();
            if (!empty($imageToDelete)) {
                $this->image->delete($imageToDelete);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            if (!$this->image->isEmpty()) {
                $this->image->delete();
            }
            throw $e;
        }

        // dd(DB::getQueryLog());

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
        try {
            DB::beginTransaction();
            $word->delete();
            if ($word->image_filename) {
                $this->image->delete($word->image_filename);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return redirect()->route('words');
    }

    /**
     * Get validator.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $wordId
     * @return Validator
     */
    private function validator(Request $request, $wordId = 'NULL')
    {
        $userId = $request->user()->id;

        $messages = [
            'word.unique' => 'You have already added this word.',
            'image.required_without_all' => 'Image is required when no definitions are given.',
        ];

        $validator = Validator::make($request->all(), [
            'word' => "required|unique:word,word,{$wordId},id,user_id,{$userId}|max:255",
            'image' => "required_without_all:definitions.0,imageUrl,keepImage",
            'imageUrl' => 'url',
        ], $messages);

        $validator->after(array($this->image, 'validateImage'));

        return $validator;
    }

    /**
     * Get array of Definition objects.
     *
     * @param  array  $definitions
     * @param  array  $definitionIds
     * @return array
     */
    private function getDefinitions(array $definitions, array $definitionIds)
    {
        $definitions = array_map(array($this, 'createDefinition'), $definitions, $definitionIds);
        return array_filter($definitions);
    }

    /**
     * Get array of Definition objects from old input.
     *
     * @return array
     */
    private function getDefinitionsFromOldInput()
    {
        return $this->getDefinitions(old('definitions'), old('definitionIds'));
    }

    /**
     * Get array of Definition objects from input.
     *
     * @return array
     */
    private function getDefinitionsFromInput(Request $request)
    {
        return $this->getDefinitions($request->input('definitions', array()), $request->input('definitionIds', array()));
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

    /**
     * Get a new Definition instance.
     *
     * @param  string $definition
     * @param  string $definitionId
     * @return Definition|null
     */
    private function createDefinition($definition, $definitionId)
    {
        if (empty($definition)) {
            return;
        }

        $definitionObject = new Definition(['definition' => $definition]);

        if (!empty($definitionId)) {
            $definitionObject->id = $definitionId;
            $definitionObject->exists = true;
        }

        return $definitionObject;
    }

    /**
     * Remove definitions that do not belong to this word.
     *
     * @param  array  &$inputDefinitions
     * @param  Word   $word
     * @return void
     */
    private function removeDefinitionsThatDoNotBelongToThisWord(array &$inputDefinitions, Word $word)
    {
        $inputDefinitions = array_filter($inputDefinitions, function ($inputDefinition) use ($word) {
            return is_null($inputDefinition->id) || $word->definitions->pluck('id')->contains($inputDefinition->id);
        });
    }
}
