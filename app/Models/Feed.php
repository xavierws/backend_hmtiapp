<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feed extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'feeds';

    /**
     * The attribute that are mass assignable
     *
     * @var string[]
     */
    protected $fillable = [
        'title',
        'caption',
        'day_of_week',
    ];

    /**
     * Get the colleger's profile image
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function images()
    {
        return $this->morphMany('App\Models\Image', 'imageable');
    }

    /**
     * get the colleger that has seen the feeds
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function collegerProfiles()
    {
        return $this->belongsToMany('App\Models\CollegerProfile', 'seen_by')->withTimestamps();
    }
}
