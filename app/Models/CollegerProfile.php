<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollegerProfile extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'colleger_profiles';

    /**
     * he attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'nrp',
        'birthday',
        'address',
        'role_id'
    ];

    /**
     * Get the role associated with this profile
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function role()
    {
        return $this->hasOne('App\Models\Role');
    }

    /**
     * Get all of the users associated with this profile
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function users()
    {
        return $this->morphMany('App\Models\User', 'userable');
    }

    /**
     * Get the colleger's profile image
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function image()
    {
        return $this->morphOne('App\Models\Image', 'imageable');
    }

    /**
     * the feeds that has been seen by colleger
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function feeds()
    {
        return $this->belongsToMany('App\Models\Feed', 'seen_by')->withTimestamps();
    }
}
