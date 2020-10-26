<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    use HasFactory;

    /**
     * the table associated with model
     *
     * @var string
     */
    protected $table = 'calendars';

    /**
     * Get all the event's calendar
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function events() {
        return $this->hasMany('App\Models\Event');
    }

}
