<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\CustomDateTimeCast;
use Illuminate\Database\Eloquent\SoftDeletes;
class Evaluation extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'evaluations';
    protected $fillable = [
        'user_id',
        'note',
        'month',
        'year',
        'evaluation',
      
    ];

    protected $allowedSorts = [
       
        'created_at',
        'updated_at'
    ];

    protected $hidden = ['deleted_at'];

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }
}
