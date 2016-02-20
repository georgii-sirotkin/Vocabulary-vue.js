<?php

namespace App\Services;

use Illuminate\Validation\Validator;
use Intervention\Image\ImageManager;

class ImageService
{
    private $maxWidth;
    private $maxHeight;
    /**
     * Maximum file size in kilobytes.
     * @var  int
     */
    private $maxFilesize;
    private $mimeTypes;
    /**
     * Folder for storing images.
     * @var string
     */
    private $path;
    private $image;
    private $imageManager;

    /**
     * Create a new instance of ImageService.
     *
     * @param int  $maxWidth
     * @param int  $maxHeight
     * @param int  $maxFilesize  Maximum file size in kilobytes.
     * @param array  $mimeTypes
     * @param string  $path  Path to the folder for storing images.
     * @param ImageManager $imageManager
     */
    public function __construct($maxWidth, $maxHeight, $maxFilesize, array $mimeTypes, $path, ImageManager $imageManager)
    {
        $this->maxWidth = $maxWidth;
        $this->maxHeight = $maxHeight;
        $this->maxFilesize = $maxFilesize;
        $this->mimeTypes = $mimeTypes;
        $this->path = $path;
        $this->imageManager = $imageManager;
    }

    /**
     * Validate image.
     *
     * @param  Validator $validator
     * @return void
     */
    public function validateImage(Validator $validator)
    {
        $validData = $validator->valid();
        $files = $validator->getFiles();

        if (!empty($validData['imageUrl'])) {
            $inputName = 'imageUrl';
            $value = $validData['imageUrl'];
        } elseif (isset($files['image']) && $validator->isAValidFileInstance($files['image'])) {
            $inputName = 'image';
            $value = $files['image'];
        } else {
            return;
        }

        try {
            $this->image = $this->imageManager->make($value);
        } catch (\Exception $e) {
            $validator->errors()->add($inputName, "Unable to get image. Supported image types: {$this->getMimeTypesAsString()}.");
            return;
        }

        if ($this->isImageFilesizeTooLarge()) {
            $validator->errors()->add($inputName, "Image file size is too large. Max file size: {$this->maxFilesize} kilobytes.");
        }

        if (!$this->isAllowedMimeType()) {
            $validator->errors()->add($inputName, "Image type {$this->extractImageTypeFromMimeType()} is not supported. Allowed image types: {$this->getMimeTypesAsString()}.");
        }
    }

    /**
     * Get supported mime types as string.
     *
     * @param  string $separator
     * @return  string
     */
    private function getMimeTypesAsString($separator = ', ')
    {
        return implode($separator, $this->mimeTypes);
    }

    /**
     * Determine if image mime type is allowed.
     *
     * @return boolean
     */
    private function isAllowedMimeType()
    {
        return in_array($this->extractImageTypeFromMimeType(), $this->mimeTypes);
    }

    /**
     * Extract image type from mime type.
     *
     * @return string
     */
    private function extractImageTypeFromMimeType()
    {
        return substr($this->image->mime(), strpos($this->image->mime(), '/') + 1);
    }

    /**
     * Determine if image file size is too large.
     *
     * @return boolean
     */
    private function isImageFilesizeTooLarge()
    {
        return $this->image->filesize() > $this->maxFilesize * 1024;
    }
}
