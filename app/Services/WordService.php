<?php

namespace App\Services;

use App\Definition;
use App\Word;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class WordService
{
    private $validationFactory;
    private $image;
    private $db;
    private $word;

    /**
     * Create a new instance of WordService.
     *
     * @param Factory         $validationFactory
     * @param ImageService    $image
     * @param DatabaseManager $db
     */
    public function __construct(Factory $validationFactory, ImageService $image, DatabaseManager $db)
    {
        $this->validationFactory = $validationFactory;
        $this->image = $image;
        $this->db = $db;
    }

    /**
     * Get validator.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $wordId
     * @return Validator
     */
    public function getValidator(Request $request, $wordId = 'NULL')
    {
        $userId = $request->user()->id;

        $messages = [
            'word.unique' => 'You have already added this word.',
            'image.required_without_all' => 'Image is required when no definitions are given.',
        ];

        $validator = $this->validationFactory->make($request->all(), [
            'word' => "required|unique:word,word,{$wordId},id,user_id,{$userId}|max:255",
            'image' => "required_without_all:definitions.0,imageUrl,keepImage",
            'imageUrl' => 'url',
        ], $messages);

        $validator->after(array($this->image, 'validateImage'));

        return $validator;
    }

    /**
     * Get array of Definition objects from old input.
     *
     * @return array
     */
    public function getDefinitionsFromOldInput()
    {
        return $this->getDefinitions(old('definitions'), old('definitionIds'));
    }

    /**
     * Create and store a new word.
     *
     * @param  Request $request
     * @return void
     */
    public function storeWord(Request $request)
    {
        $this->word = new Word($request->all());

        $this->processImage();

        try {
            $this->db->beginTransaction();
            $request->user()->addWord($this->word);
            $this->word->addDefinitionsWithoutTouch($this->getDefinitionsFromInput($request));
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            if (!$this->image->isEmpty()) {
                $this->image->delete();
            }
            throw $e;
        }
    }

    /**
     * Update word.
     *
     * @param  Request $request
     * @param  Word    $word
     * @return void
     */
    public function updateWord(Request $request, Word $word)
    {
        $this->word = $word;
        $this->word->word = $request->input('word');

        if ($this->shouldDeleteOldImage($request)) {
            $imageToDelete = $this->word->image_filename;
            $this->word->image_filename = null;
        }

        $this->processImage();

        $inputDefinitions = $this->getDefinitionsFromInput($request);
        $this->removeDefinitionsThatDoNotBelongToThisWord($inputDefinitions);

        try {
            $this->db->beginTransaction();
            $word->save();
            $word->addDefinitionsWithTouch($inputDefinitions);
            $definitionIdsToRetain = collect($inputDefinitions)->pluck('id');
            $this->deleteDefinitionsRemovedByUser($definitionIdsToRetain);
            if (!empty($imageToDelete)) {
                $this->image->delete($imageToDelete);
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            if (!$this->image->isEmpty()) {
                $this->image->delete();
            }
            throw $e;
        }
    }

    /**
     * Delete word.
     *
     * @param  Word   $word
     * @return void
     */
    public function deleteWord(Word $word)
    {
        try {
            $this->db->beginTransaction();
            $word->delete();
            if ($word->image_filename) {
                $this->image->delete($word->image_filename);
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Process image.
     *
     * @return void
     */
    private function processImage()
    {
        if (!$this->image->isEmpty()) {
            $this->image->resizeIfNecessary();
            $this->image->save();
            $this->word->image_filename = $this->image->getFileName();
        }
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
     * Get array of Definition objects from input.
     *
     * @return array
     */
    private function getDefinitionsFromInput(Request $request)
    {
        return $this->getDefinitions($request->input('definitions', array()), $request->input('definitionIds', array()));
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
     * Determine if old image should be deleted.
     *
     * @param  Request $request
     * @return bool
     */
    private function shouldDeleteOldImage(Request $request)
    {
        return $this->word->image_filename && (!$this->image->isEmpty() || !$request->has('keepImage'));
    }

    /**
     * Remove definitions that do not belong to this word.
     *
     * @param  array  &$inputDefinitions
     * @param  Word   $word
     * @return void
     */
    private function removeDefinitionsThatDoNotBelongToThisWord(array &$inputDefinitions)
    {
        $word = $this->word;
        $inputDefinitions = array_filter($inputDefinitions, function ($inputDefinition) use ($word) {
            return is_null($inputDefinition->id) || $word->definitions->pluck('id')->contains($inputDefinition->id);
        });
    }

    /**
     * Delete definitions that user removed while editing the word.
     *
     * @param  Collection $definitionIdsToRetain
     * @return void
     */
    private function deleteDefinitionsRemovedByUser(Collection $definitionIdsToRetain)
    {
        $this->word->definitions()->whereNotIn('id', $definitionIdsToRetain)->delete();
    }
}
