<?php

namespace App;

use App\Definition;
use Auth;
use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Word extends Model implements SluggableInterface
{
    use SluggableTrait;

    protected static function boot()
    {
        parent::boot();

        if (Auth::check()) {
            static::addGlobalScope('currentUser', function (Builder $builder) {
                $builder->where('user_id', Auth::user()->id);
            });
        }
    }

    protected $table = 'word';

    protected $fillable = [
        'word',
    ];

    protected $sluggable = [
        'build_from' => 'word',
        'save_to' => 'slug',
    ];

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

        return '/' . config('settings.image.folder') . '/' . $this->image_filename;
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
