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
        $wordId = $this->route('word') ? $this->route('word')->id : 'NULL';
        $userId = $this->user()->id;

        return [
            'title' => [
                'required',
                Rule::unique('words')->where(function ($query) use ($userId) {
                    return $query->where('user_id', $userId);
                })->ignore($wordId),
                'max:255',
            ],
            'definitions' => 'array',
            'definitions.*.text' => 'required|string',
            'image' => "required_without_all:definitions.0.text,imageUrl,keepImage|file",
            'imageUrl' => 'nullable|url',
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
            'title.unique' => 'You have already added this word.',
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
            /** @var ImageValidator $imageValidator */
            $imageValidator = app(ImageValidator::class);
            $imageValidator->validate($validator, $this);
        });
    }
}
