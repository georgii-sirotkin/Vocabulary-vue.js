<?php

namespace App\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
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
    private $imagesFolder;
    private $image;
    private $imageManager;
    private $storage;
    private $name;

    /**
     * Create a new instance of ImageService.
     *
     * @param int  $maxWidth
     * @param int  $maxHeight
     * @param int  $maxFilesize  Maximum file size in kilobytes.
     * @param array  $mimeTypes
     * @param string  $imagesFolder  Folder for storing images.
     * @param ImageManager $imageManager
     */
    public function __construct($maxWidth, $maxHeight, $maxFilesize, array $mimeTypes, $imagesFolder, ImageManager $imageManager, Filesystem $storage)
    {
        $this->maxWidth = $maxWidth;
        $this->maxHeight = $maxHeight;
        $this->maxFilesize = $maxFilesize;
        $this->mimeTypes = $mimeTypes;
        $this->imagesFolder = $imagesFolder;
        $this->imageManager = $imageManager;
        $this->storage = $storage;
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
     * Determine if image is present.
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return empty($this->image);
    }

    /**
     * Resize image if necessary.
     *
     * @return void
     */
    public function resizeIfNecessary()
    {
        if ($this->needsResizing()) {

            $normalizedWidth = $this->image->width() / $this->maxWidth;
            $normalizedHeight = $this->image->height() / $this->maxHeight;

            if ($normalizedWidth > $normalizedHeight) {
                $this->image->widen($this->maxWidth);
            } else {
                $this->image->heighten($this->maxHeight);
            }
        }
    }

    /**
     * Save image.
     *
     * @return void
     */
    public function save()
    {
        $this->name = $this->getUniqueFileName();
        $this->image->save($this->getFullFileName($this->name));
    }

    /**
     * Get image file name.
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->name;
    }

    /**
     * Delete image.
     *
     * @return void.
     */
    public function delete($imageFilename = null)
    {
        $imageFilename = $imageFilename ? $imageFilename : $this->name;

        if (!$this->storage->delete($this->getFullFileName($imageFilename))) {
            throw new \ErrorException('Failed to delete file');
        }
    }

    /**
     * Get full file name to the image.
     *
     * @param  string $name
     * @return string
     */
    public function getFullFileName($name)
    {
        return public_path($this->imagesFolder . DIRECTORY_SEPARATOR . $name);
    }

    /**
     * Determine if image needs resizing.
     *
     * @return bool
     */
    private function needsResizing()
    {
        return $this->image->width() > $this->maxWidth || $this->image->height() > $this->maxHeight;
    }

    /**
     * Get unique file name for storing the image.
     *
     * @return string
     */
    private function getUniqueFileName()
    {
        $extension = $this->getExtension();
        do {
            $name = uniqid('', true) . $extension;
        } while ($this->storage->exists($this->getFullFileName($name)));

        return $name;
    }

    /**
     * Get image file extension defined by mime type.
     *
     * @return  string
     */
    private function getExtension()
    {
        switch ($this->extractImageTypeFromMimeType()) {
            case 'jpeg':
                $extension = '.jpg';
                break;
            case 'png':
                $extension = '.png';
                break;
            case 'gif':
                $extension = '.gif';
                break;
            default:
                $extension = '';
        }

        return $extension;
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
