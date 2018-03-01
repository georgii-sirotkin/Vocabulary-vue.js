<?php

namespace App\Http\Requests;

use App\Validators\ImageValidator;
use Illuminate\Validation\Rule;
use Intervention\Image\Image;

class WordRequest extends Request
{
    /**
     * Image uploaded by user or downloaded from url.
     *
     * @var Image
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // if editing word, then the url looks like /words/{word}/ where {word} is slug
        $slug = $this->route('word') ?: 'NULL';
        $userId = $this->user()->id;

        return [
            'title' => [
                'required',
                Rule::unique('words')->where(function ($query) use ($userId) {
                    return $query->where('user_id', $userId);
                })->ignore($slug, 'slug'),
                'max:255',
            ],
            'image' => "required_without_all:definitions.0,imageUrl,keepImage",
            'imageUrl' => 'nullable|url'
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
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $imageValidator = new ImageValidator(config('settings.image.max_filesize'), config('settings.image.mime_types'), app('Intervention\Image\ImageManager'));
            $imageValidator->validate($validator, $this);
        });
    }
}
