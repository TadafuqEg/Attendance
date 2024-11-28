<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Models\Attendance;
use App\Models\Evaluation;
use App\Models\JoinUs;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOTP;
use Illuminate\Support\Facades\Validator;

class ReportController extends ApiController
{
    public function index(){
        $response['evaluations']=Evaluation::where('user_id',auth()->user()->id)->where('year',intval(date('Y')))->orderBy('month', 'asc')->get();
        $response['leaves']= Attendance::where('user_id',auth()->user()->id)->whereIn('status',['emergency_vacation','ordinary_vacation'])->whereYear('date', date('Y'))->count();
        $response['leaves_credit']=21-$response['leaves'];
        return $this->sendResponse($response,null,200);
    }
}