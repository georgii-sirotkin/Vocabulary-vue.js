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

        static::addGlobalScope('currentUser', function (Builder $builder) {
            $builder->where('user_id', Auth::user()->id);
        });
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

    // /**
    //  * Get all existing slugs that are similar to the given slug.
    //  *
    //  * @param string $slug
    //  * @return array
    //  */
    // protected function getExistingSlugs($slug)
    // {
    //     $config = $this->getSluggableConfig();
    //     $save_to = $config['save_to'];
    //     $include_trashed = $config['include_trashed'];

    //     $instance = new static;

    //     //check for direct match or something that has a separator followed by a suffix
    //     $query = $instance->where('user_id', $this->user_id)->where(function ($query) use (
    //         $save_to,
    //         $config,
    //         $slug
    //     ) {
    //         $query->where($save_to, $slug);
    //         $query->orWhere($save_to, 'LIKE',
    //             $slug . $config['separator'] . '%');
    //     });

    //     // include trashed models if required
    //     if ($include_trashed && $this->usesSoftDeleting()) {
    //         $query = $query->withTrashed();
    //     }

    //     // get a list of all matching slugs
    //     $list = $query->lists($save_to, $this->getKeyName());

    //     // Laravel 5.0/5.1 check
    //     return $list instanceof Collection ? $list->all() : $list;
    // }
}
