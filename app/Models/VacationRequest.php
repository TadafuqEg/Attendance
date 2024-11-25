<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\CustomDateTimeCast;
use Illuminate\Database\Eloquent\SoftDeletes;
class VacationRequest extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'requests';
    public $AttachmentCollection = 'attachment-image';
    protected $fillable = [
        'user_id',
        'code',
        'from',
        'to',
        'message',
        'type',
        'status',
        'hr_approval',
        'Manager_approval',
        'rejection_reason'
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
