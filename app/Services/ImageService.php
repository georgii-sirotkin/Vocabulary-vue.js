<?php

namespace App\Services;

use Intervention\Image\Image;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser;

class ImageService
{
    private $maxWidth;
    private $maxHeight;

    /**
     * Folder for storing images.
     * 
     * @var string
     */
    private $imagesFolder;

    /**
     * Create a new instance of ImageService.
     *
     * @param int  $maxWidth
     * @param int  $maxHeight
     * @param string  $imagesFolder  Folder for storing images.
     */
    public function __construct($maxWidth, $maxHeight, $imagesFolder)
    {
        $this->maxWidth = $maxWidth;
        $this->maxHeight = $maxHeight;
        $this->imagesFolder = $imagesFolder;
    }

    /**
     * Process image.
     *
     * @param Image $image
     * @return void
     */
    public function processImage(Image $image)
    {
        if ($this->needsResizing($image)) {
            $this->resize($image);
        }

        $this->save($image);
    }

    /**
     * Resize image.
     *
     * @param  Image $image
     * @return void
     */
    private function resize(Image $image)
    {
        $normalizedWidth = $image->width() / $this->maxWidth;
        $normalizedHeight = $image->height() / $this->maxHeight;

        if ($normalizedWidth > $normalizedHeight) {
            $image->widen($this->maxWidth);
        } else {
            $image->heighten($this->maxHeight);
        }
    }

    /**
     * Save image.
     *
     * @param  Image $image
     * @return void
     */
    private function save(Image $image)
    {
        $path = $this->getUniqueFileName($image);
        $image->save($path);
    }

    /**
     * Determine if image needs resizing.
     *
     * @param  Image $image
     * @return bool
     */
    private function needsResizing(Image $image)
    {
        return $image->width() > $this->maxWidth || $image->height() > $this->maxHeight;
    }

    /**
     * Get unique file name for storing the image.
     *
     * @return string
     */
    private function getUniqueFileName(Image $image)
    {
        $extension = $this->getExtension($image);

        do {
            $path = $this->getFullFileName(uniqid('', true) . '.' . $extension);
        } while (file_exists($path));

        return $path;
    }

    /**
     * Get full path to the image.
     *
     * @param  string $name
     * @return string
     */
    private function getFullFileName($name)
    {
        return config('filesystems.disks.public.root') . DIRECTORY_SEPARATOR . $this->imagesFolder . DIRECTORY_SEPARATOR . $name;
    }

    /**
     * Get image file extension.
     *
     * @param Image $image
     * @return string
     */
    private function getExtension(Image $image)
    {
        $guesser = ExtensionGuesser::getInstance();
        return $guesser->guess($image->mime());
    }
}
