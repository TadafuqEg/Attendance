<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\CustomDateTimeCast;

class OfficialHoliday extends Model
{
    use HasFactory;
    protected $table = 'official_holidays';
    protected $fillable = [
        'title',
        'from',
        'to'
    ];

    protected $allowedSorts = [
       
        'created_at',
        'updated_at'
    ];

}
