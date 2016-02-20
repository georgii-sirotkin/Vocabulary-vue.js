<?php

namespace App;

use App\Definition;
use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Word extends Model implements SluggableInterface
{
    use SluggableTrait;

    protected $table = 'word';

    protected $fillable = [
        'word',
    ];

    protected $sluggable = [
        'build_from' => 'word',
        'save_to' => 'slug',
    ];

    /**
     * Get definitions that belong to this word.
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function definitions()
    {
        return $this->hasMany(Definition::class);
    }

    /**
     * Get all existing slugs that are similar to the given slug and have the same user_id.
     *
     * @param string $slug
     * @return array
     */
    protected function getExistingSlugs($slug)
    {
        $config = $this->getSluggableConfig();
        $save_to = $config['save_to'];
        $user_id = $this->user_id;
        $include_trashed = $config['include_trashed'];

        $instance = new static;

        //check for direct match or something that has a separator followed by a suffix
        $query = $instance->where(function ($query) use (
            $save_to,
            $config,
            $slug,
            $user_id
        ) {
            $query->where('user_id', $user_id)
                ->where(function ($query) use ($save_to, $slug, $save_to, $config) {
                    $query->where($save_to, $slug)
                        ->orWhere($save_to, 'LIKE', $slug . $config['separator'] . '%');
                });
        });

        // include trashed models if required
        if ($include_trashed && $this->usesSoftDeleting()) {
            $query = $query->withTrashed();
        }

        // get a list of all matching slugs
        $list = $query->lists($save_to, $this->getKeyName());

        // Laravel 5.0/5.1 check
        return $list instanceof Collection ? $list->all() : $list;
    }
}
