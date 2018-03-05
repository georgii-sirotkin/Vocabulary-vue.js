<?php

namespace App;

use Auth;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Storage;

/**
 * App\Word
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property int $user_id
 * @property int $right_guesses_number
 * @property string|null $image_filename
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Definition[] $definitions
 * @mixin \Eloquent
 */
class Word extends Model
{
    use Sluggable, SluggableScopeHelpers;

    protected static function boot()
    {
        parent::boot();

        if (Auth::check()) {
            static::addGlobalScope('currentUser', function (Builder $builder) {
                $builder->where('user_id', Auth::user()->id);
            });
        }
    }

    protected $fillable = [
        'title',
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
                'source' => 'title'
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
        if (!$this->hasImage()) {
            return null;
        }

        return Storage::disk('public')->url($this->getImagePath());
    }

    /**
     * Get image path.
     * 
     * @return string|null
     */
    public function getImagePath()
    {
        if (!$this->hasImage()) {
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
     * Add definitions to word.
     *
     * @param array $definitions
     */
    public function addDefinitions(array $definitions)
    {
        $this->definitions()->saveMany($definitions);
    }

    /**
     * Get definitions that belong to this word.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function definitions()
    {
        return $this->hasMany(Definition::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
