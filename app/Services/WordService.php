<?php

namespace App\Services;

use App\Definition;
use App\Http\Requests\WordRequest;
use App\Word;
use Illuminate\Contracts\Filesystem\Factory;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Collection;

class WordService
{
    private $imageService;
    private $db;
    private $storage;

    /**
     * Create a new instance of WordService.
     *
     * @param ImageService    $imageService
     * @param DatabaseManager $db
     * @param Illuminate\Contracts\Filesystem\Factory $storage
     */
    public function __construct(ImageService $imageService, DatabaseManager $db, Factory $storage)
    {
        $this->imageService = $imageService;
        $this->db = $db;
        $this->storage = $storage;
    }

    /**
     * Create and store a new word.
     *
     * @param  WordRequest $request
     * @return void
     */
    public function storeWord(WordRequest $request)
    {
        $word = new Word($request->all());

        if ($request->hasImage()) {
            $this->imageService->processImage($request, $word);
        }

        try {
            $this->db->beginTransaction();
            $request->user()->addWord($word);
            $word->addDefinitionsWithoutTouch($this->getDefinitionsFromInput($request));
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            if ($word->hasImage()) {
                $this->storage->disk('public')->delete($word->getImagePath());
            }
            throw $e;
        }
    }

    /**
     * Update word.
     *
     * @param  WordRequest $request
     * @param  string $slug
     * @return void
     */
    public function updateWord(WordRequest $request, $slug)
    {
        $word = Word::findBySlugOrFail($slug);
        $word->word = $request->input('word');

        if ($this->shouldDeleteOldImage($request, $word)) {
            $imageToDeletePath = $word->getImagePath();
            $word->image_filename = null;
        }

        if ($request->hasImage()) {
            $this->imageService->processImage($request, $word);
        }

        try {
            $this->db->beginTransaction();
            $word->save();
            $word->definitions()->delete();
            $word->addDefinitionsWithTouch($this->getDefinitionsFromInput($request));
            if (!empty($imageToDeletePath)) {
                $this->storage->disk('public')->delete($imageToDeletePath);
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            if ($word->hasImage()) {
                $this->storage->disk('public')->delete($word->getImagePath());
            }
            throw $e;
        }
    }

    /**
     * Delete word.
     *
     * @param  string $slug
     * @return void
     */
    public function deleteWord($slug)
    {
        $word = Word::findBySlugOrFail($slug);

        try {
            $this->db->beginTransaction();
            $word->delete();
            if ($word->hasImage()) {
                $this->storage->disk('public')->delete($word->getImagePath());
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Get array of Definition objects.
     *
     * @param  array  $definitions
     * @return array
     */
    public function getDefinitionObjects(array $definitions)
    {
        return array_map(array($this, 'createDefinition'), $definitions);
    }

    /**
     * Get array of Definition objects from input.
     *
     * @return array
     */
    private function getDefinitionsFromInput(WordRequest $request)
    {
        return $this->getDefinitionObjects($request->input('definitions', array()));
    }

    /**
     * Get a new Definition instance.
     *
     * @param  string $definition
     * @return Definition
     */
    private function createDefinition($definition)
    {
        return new Definition(['definition' => $definition]);
    }

    /**
     * Determine if old image should be deleted.
     *
     * @param  WordRequest $request
     * @param  Word $word
     * @return bool
     */
    private function shouldDeleteOldImage(WordRequest $request, Word $word)
    {
        return $word->hasImage() && ($request->hasImage() || !$request->has('keepImage'));
    }
}