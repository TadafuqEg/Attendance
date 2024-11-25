<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Evaluation;
use App\Models\DriverLicense;
use Image;
use Str;
use App\Exports\AttendanceExport;
use File;
use Maatwebsite\Excel\Facades\Excel;
class EvaluationController extends Controller
{//done
    public function index(Request $request)
    {//dd($request->all());
        $validator  =   Validator::make($request->all(), [
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from'
            
        ]);
        if ($validator->fails()) {

           // return Redirect::back()->withErrors(['error'=>'trdfssfghtrh']);
            return Redirect::back()->with('error', 'The end date must be after the start date.');
        }
        $all_evaluations=Evaluation::orderBy('year', 'desc')->orderBy('month', 'desc')->orderBy('id', 'desc');
        if($request->has('user')&& $request->user!=null) {
            $all_evaluations->where('user_id', $request->user);
        }

        if($request->has('date_from')&& $request->date_from!=null && $request->has('date_to')&& $request->date_to!=null) {
            $startMonth = intval(date('m', strtotime($request->date_from)));
            $startYear = intval(date('Y', strtotime($request->date_from)));
        
            $endMonth = intval(date('m', strtotime($request->date_to)));
            $endYear = intval(date('Y', strtotime($request->date_to)));
        
            $all_evaluations = $all_evaluations->where(function ($query) use ($startMonth, $startYear, $endMonth, $endYear) {
                $query->where(function ($query) use ($startMonth, $startYear, $endMonth, $endYear) {
                    $query->where('year', '>', $startYear)
                          ->orWhere(function ($query) use ($startMonth, $startYear) {
                              $query->where('year', $startYear)
                                    ->where('month', '>=', $startMonth);
                          });
                })->where(function ($query) use ($endMonth, $endYear) {
                    $query->where('year', '<', $endYear)
                          ->orWhere(function ($query) use ($endMonth, $endYear) {
                              $query->where('year', $endYear)
                                    ->where('month', '<=', $endMonth);
                          });
                });
            });
        }elseif($request->has('date_from')&& $request->date_from!=null && $request->date_to==null){
            $all_evaluations->where('month', intval(date('m',strtotime($request->date_from))))->where('year', intval(date('Y',strtotime($request->date_from))));
        }

        if ($request->has('status')&& $request->status!=null) {
            switch ($request->status) {
                case 'very_good':
                    // Code for case1
                    $all_evaluations->where('evaluation','>=',76)->where('evaluation','<=',100);
                    break;
                case 'good':
                    // Code for case2
                    $all_evaluations->where('evaluation','>=',51)->where('evaluation','<=',75);
                    break;
                case 'acceptable':
                    // Code for case2
                    $all_evaluations->where('evaluation','>=',41)->where('evaluation','<=',50);
                    break;
                
                default:
                    $all_evaluations->where('evaluation','>=',0)->where('evaluation','<=',40);
            }
            
        }
        $all_evaluations = $all_evaluations->paginate(12);
        $users=User::whereHas('roles', function ($query) {
            $query->where('roles.name', 'Client');
        })->get();
        $status_search=$request->status;
        $date_from_search=$request->date_from;
        $date_to_search=$request->date_to;
        $user_search=$request->user;
        return view('dashboard.evaluations.index',compact('all_evaluations','users','status_search','date_to_search','date_from_search','user_search'));

    }
}