<?php

namespace App\Validators;

use App\Http\Requests\WordRequest;
use Illuminate\Validation\Validator;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

class ImageValidator
{
    /**
     * Maximum file size in kilobytes.
     * 
     * @var  int
     */
	private $maxFilesize;
	private $mimeTypes;
	private $imageManager;

	/**
	 * Create a new validator instance.
	 * 
	 * @param int $maxFilesize Maximum file size in kilobytes.
	 * @param array  $mimeTypes
	 * @param ImageManager $imageManager
	 */
	public function __construct($maxFilesize, array $mimeTypes, ImageManager $imageManager)
	{
		$this->maxFilesize = $maxFilesize;
		$this->mimeTypes = $mimeTypes;
		$this->imageManager = $imageManager;
	}

	/**
	 * Validate image input.
	 * 
	 * @param  Validator   $validator
	 * @param  WordRequest $request
	 * @return void
	 */
	public function validate(Validator $validator, WordRequest $request)
	{
		$inputName = $this->getInputName($validator);

		if (is_null($inputName)) {
			return;
		}

		try {
            $image = $this->imageManager->make($validator->attributes()[$inputName]);
        } catch (\Exception $e) {
            $validator->errors()->add($inputName, "Unable to get image. Supported image types: {$this->getMimeTypesAsString()}.");
            return;
        }

        if ($this->isImageFilesizeTooLarge($image)) {
            $validator->errors()->add($inputName, "Image file size is too large. Max file size: {$this->maxFilesize} kilobytes.");
        }

        if (!$this->isAllowedMimeType($image)) {
            $validator->errors()->add($inputName, "Image type {$this->extractImageTypeFromMimeType($image)} is not supported. Allowed image types: {$this->getMimeTypesAsString()}.");
        }

        $request->setImage($image);
	}

	/**
	 * Get image input name.
	 * User may upload an image or submit image URL.
	 * 
	 * @param  Validator $validator
	 * @return string|null
	 */
	private function getInputName(Validator $validator)
	{
		if ($this->isImageURLValid($validator)) {
			return 'imageUrl';
		}

		if ($this->isFileSuccessfullyUploaded($validator)) {
			return 'image';
		}

		return null;
	}

	/**
	 * Determine if image URL is valid.
	 * 
	 * @param  Validator $validator
	 * @return boolean
	 */
	private function isImageURLValid(Validator $validator)
	{
		$validData = $validator->valid();
		return !empty($validData['imageUrl']);
	}

	/**
	 * Determine if image file is successfully uploaded.
	 * 
	 * @param  Validator $validator
	 * @return boolean
	 */
	private function isFileSuccessfullyUploaded(Validator $validator)
	{
		$files = $validator->getFiles();

		if (empty($files['image'])) {
			return false;
		}

		if (!$files['image']->isValid()) {
			$validator->errors()->add('image', 'Failed to upload file.');
			return false;
		}

		return true;
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
     * Determine if image file size is too large.
     *
     * @return boolean
     */
    private function isImageFilesizeTooLarge(Image $image)
    {
        return $image->filesize() > $this->maxFilesize * 1024;
    }

    /**
     * Determine if image mime type is allowed.
     *
     * @return boolean
     */
    private function isAllowedMimeType(Image $image)
    {
        return in_array($this->extractImageTypeFromMimeType($image), $this->mimeTypes);
    }

    /**
     * Extract image type from mime type.
     *
     * @return string
     */
    private function extractImageTypeFromMimeType(Image $image)
    {
        return substr($image->mime(), strpos($image->mime(), '/') + 1);
    }
}