<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Validators\ImageValidator;
use Illuminate\Validation\Validator;
use Intervention\Image\Image;

class WordRequest extends Request
{
    /**
     * Image uploaded by user or downloaded from url.
     *
     * @var Intervention\Image\Image
     */
    protected $image;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     * Modify input.
     */
    public function all()
    {
        $attributes = parent::all();
        $this->sanitiseInput($attributes);
        $this->replace($attributes);
        return $attributes;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $slug = $this->route('words') ? $this->route('words') : 'NULL';
        $userId = $this->user()->id;

        return [
            'word' => "required|unique:word,word,{$slug},slug,user_id,{$userId}|max:255",
            'image' => "required_without_all:definitions.0,imageUrl,keepImage",
            'imageUrl' => 'url'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'word.unique' => 'You have already added this word.',
            'image.required_without_all' => 'Image is required when no definitions are given.',
        ];
    }

    /**
     * Set image object.
     * 
     * @param Image $image
     */
    public function setImage(Image $image)
    {
        $this->image = $image;
    }

    /**
     * Get image object.
     * 
     * @return Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Determine if request contains an image.
     * 
     * @return boolean
     */
    public function hasImage()
    {
        return !is_null($this->image);
    }

    /**
     * {@inheritdoc}
     * Attach a callback to be run after validation is completed.
     * 
     * @return Validator
     */
    protected function getValidatorInstance()
    {
        $validator = parent::getValidatorInstance();
        $validator->after(function (Validator $validator) {
            $imageValidator = new ImageValidator(config('settings.image.max_filesize'), config('settings.image.mime_types'), app('Intervention\Image\ImageManager'));
            $imageValidator->validate($validator, $this);
        });
        return $validator;
    }
        
    /**
     * Sanitise input.
     * 
     * @param  array  &$attributes
     * @return void
     */
    private function sanitiseInput(array &$attributes)
    {
        if (isset($attributes['definitions'])) {
            $this->removeEmptyDefinitions($attributes);
        }
    }
    
    /**
     * Remove empty definitions from input.
     * 
     * @param  array  &$attributes
     * @return void
     */
    private function removeEmptyDefinitions(array &$attributes)
    {
        $attributes['definitions'] = array_filter($attributes['definitions']);
    }
}
