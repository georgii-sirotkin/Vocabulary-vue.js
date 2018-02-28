<?php

namespace App;

use Auth;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Word extends Model
{
    use Sluggable, SluggableScopeHelpers;

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('currentUser', function (Builder $builder) {
            if (Auth::check()) {
                $builder->where('user_id', Auth::user()->id);
            }
        });
    }

    protected $fillable = [
        'word',
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'word'
            ]
        ];
    }

    /**
     * Get image url.
     *
     * @return string|null
     */
    public function getImageUrl()
    {
        if (empty($this->image_filename)) {
            return null;
        }

        return Storage::disk('public')->url(config('settings.image.folder') . '/' . $this->image_filename);
    }

    /**
     * Get image path.
     * 
     * @return string|null
     */
    public function getImagePath()
    {
        if (empty($this->image_filename)) {
            return null;
        }

        return config('settings.image.folder') . DIRECTORY_SEPARATOR . $this->image_filename;
    }

    /**
     * Determine if word has an image.
     * 
     * @return boolean
     */
    public function hasImage()
    {
        return !is_null($this->image_filename);
    }

    /**
     * Add definitions to word without touching word's updated_at field.
     *
     * @param array $definitions
     */
    public function addDefinitionsWithoutTouch(array $definitions)
    {
        foreach ($definitions as $definition) {
            $definition->word_id = $this->id;
            $definition->save(['touch' => false]);
        }
    }

    /**
     * Add definitions to word touching word's updated_at field.
     *
     * @param array $definitions
     */
    public function addDefinitionsWithTouch(array $definitions)
    {
        $this->definitions()->saveMany($definitions);
    }

    /**
     * Get definitions that belong to this word.
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function definitions()
    {
        return $this->hasMany(Definition::class);
    }
}
