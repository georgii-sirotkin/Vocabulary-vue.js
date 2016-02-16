<?php

namespace App;

use App\Definition;
use Illuminate\Database\Eloquent\Model;

class Word extends Model
{
    protected $table = 'word';

    protected $fillable = [
        'word',
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
}
