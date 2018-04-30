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
     * @param \Illuminate\Contracts\Filesystem\Factory $storage
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
        $word = new Word(['title' => $request->input('title')]);

        if ($request->hasImage()) {
            $this->processImage($request, $word);
        }

        try {
            $this->db->beginTransaction();
            $request->user()->addWord($word);
            $word->addDefinitionsWithoutTouch($this->getDefinitionsFromInput($request));
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            if ($word->hasImage()) {
                $this->deleteImage($word->getImagePath());
            }
            throw $e;
        }
    }

    /**
     * Update word.
     *
     * @param Word $word
     * @param  WordRequest $request
     * @return void
     * @throws \Exception
     */
    public function updateWord(Word $word, WordRequest $request)
    {
        $word->title = $request->input('title');

        if ($this->shouldDeleteOldImage($request, $word)) {
            $imageToDeletePath = $word->getImagePath();
            $word->image_filename = null;
        }

        if ($request->hasImage()) {
            $this->processImage($request, $word);
        }

        try {
            $this->db->beginTransaction();
            $word->save();
            $word->definitions()->delete();
            $word->addDefinitions($this->getDefinitionsFromInput($request));
            if (!empty($imageToDeletePath)) {
                $this->deleteImage($imageToDeletePath);
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            if ($word->hasImage()) {
                $this->deleteImage($word->getImagePath());
            }
            throw $e;
        }
    }

    /**
     * Delete word.
     *
     * @param Word $word
     * @return void
     * @throws \Exception
     */
    public function deleteWord(Word $word)
    {
        $this->db->transaction(function () use ($word) {
            $word->delete();

            if ($word->hasImage()) {
                $this->deleteImage($word->getImagePath());
            }
        });
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
        return $this->getDefinitionObjects(array_filter($request->input('definitions', array())));
    }

    /**
     * Get a new Definition instance.
     *
     * @param  string $definition
     * @return Definition
     */
    public function createDefinition($definitionData)
    {
        return new Definition($definitionData);
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

    /**
     * Delete image.
     * 
     * @param  string $path
     * @return bool
     */
    private function deleteImage($path)
    {
        return $this->storage->disk('public')->delete($path);
    }

    /**
     * @param WordRequest $request
     * @param $word
     */
    protected function processImage(WordRequest $request, $word)
    {
        $image = $request->getImage();
        $this->imageService->processImage($image);
        $word->image_filename = $image->basename;
    }
}