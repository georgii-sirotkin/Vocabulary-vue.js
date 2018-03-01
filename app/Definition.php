<?php

namespace App;

use App\Word;
use Illuminate\Database\Eloquent\Model;

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
     * @return  Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function word()
    {
        return $this->belongsTo(Word::class);
    }
}
