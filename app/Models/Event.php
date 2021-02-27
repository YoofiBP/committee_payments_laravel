<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'venue',
        'event_date',
        'flyer'
    ];

    protected $casts = [
//        'event_date' => 'datetime'
    ];

    public $attributes = [
        'total_contribution' => 0
    ];

    public function contributions()
    {
        $this->hasMany(Contribution::class);
    }


}
