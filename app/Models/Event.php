<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    /**
     * The table associated with the model
     *
     * @var string
     */
    protected $table = 'events';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'calendar_id',
        'name',
        'category',
        'description',
        'background_color',
        'start_date',
        'end_date'
    ];

    /**
     * Get the date associated with the event
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function calendar() {
        return $this->belongsTo('App\Models\Calendar');
    }
}
