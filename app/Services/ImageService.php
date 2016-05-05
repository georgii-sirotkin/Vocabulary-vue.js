<?php

namespace App\Services;

use App\Http\Requests\WordRequest;
use App\Word;
use Illuminate\Contracts\Filesystem\Factory;
use Intervention\Image\Image;

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
     * @param  WordRequest $request
     * @param  Word        $word
     * @return void
     */
    public function processImage(WordRequest $request, Word $word)
    {
        $image = $request->getImage();
        $this->resizeIfNecessary($image);
        $this->save($image);
        $word->image_filename = $image->basename;
    }

    /**
     * Resize image if necessary.
     *
     * @param  Image $image
     * @return void
     */
    private function resizeIfNecessary(Image $image)
    {
        if ($this->needsResizing($image)) {

            $normalizedWidth = $image->width() / $this->maxWidth;
            $normalizedHeight = $image->height() / $this->maxHeight;

            if ($normalizedWidth > $normalizedHeight) {
                $image->widen($this->maxWidth);
            } else {
                $image->heighten($this->maxHeight);
            }
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
     * Get full file name to the image.
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
     * @return  string
     */
    private function getExtension(Image $image)
    {
        switch ($image->mime()) {
            case 'image/jpeg':
                $extension = 'jpg';
                break;
            case 'image/png':
                $extension = 'png';
                break;
            case 'image/gif':
                $extension = 'gif';
                break;
            default:
                $extension = '';
        }

        return $extension;
    }
}
