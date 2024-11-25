<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\CustomDateTimeCast;
use Illuminate\Database\Eloquent\SoftDeletes;
class Attendance extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'attendances';
    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_out',
        'lat',
        'lng',
        'status',
        'auto_logout'
    ];

    protected $allowedSorts = [
       
        'created_at',
        'updated_at'
    ];

    protected $hidden = ['deleted_at'];

    public function user(){
        return $this->belongsTo(User::class,'user_id','id')->withTrashed();
    }
}
