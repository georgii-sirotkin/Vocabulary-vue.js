<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Definition
 *
 * @property int $id
 * @property string $text
 * @property int $word_id
 * @property-read \App\Word $word
 * @mixin \Eloquent
 */
class Definition extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'text',
    ];

    /**
     * All of the relationships to be touched (update timestamps).
     *
     * @var array
     */
    protected $touches = ['word'];

    /**
     * Get word to which this definition belongs.
     *
     * @return  \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function word()
    {
        return $this->belongsTo(Word::class);
    }
}
