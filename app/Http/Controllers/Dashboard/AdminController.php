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
use App\Models\Car;
use App\Models\DriverLicense;
use Image;
use Str;
use File;

class AdminController extends Controller
{//done
    public function index(Request $request)
    {
        $role = Role::where('name', 'Admin')->first();
        $all_users = User::orderBy('id', 'desc')->whereHas('roles', function ($query) use ($role) {
            $query->where('roles.id', $role->id);
        });

        if ($request->has('search') && $request->search!=null ) {
            $all_users->where(function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('email', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('username', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('phone', 'LIKE', '%' . $request->search . '%');
            });
        }
        
    
       
        
        $all_users = $all_users->paginate(12);

        $all_users->getCollection()->transform(function ($user) {
            // Add the 'image' key based on some condition
            $user->image = getFirstMediaUrl($user,$user->avatarCollection);
            return $user;
        });
         
        return view('dashboard.admins.index',compact('all_users'));

    }

    public function create(){
       
        return view('dashboard.admins.create');
    }

    public function store(Request $request){

            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:191'],
                'username' => ['required'],
                'email' => ['required', 'string', 'email', 'max:191', 'unique:users'],
                'password' => ['required', 'string', 'min:8'],
                'image' => ['nullable'] ,
                'phone_number' => ['nullable', 'unique:users,phone', 'numeric'],
                'address'=> ['nullable']

            ]);

           
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            
            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email'=> $request->email ,
                'phone'=>$request->phone_number,
                'password'=>  Hash::make($request->password),
                'theme'=>'theme1',
                'address'=>$request->address
                
            ]);
            $role = Role::where('name','Admin')->first();
            
            $user->assignRole([$role->id]);
            if($request->file('image')){
                uploadMedia($request->file('image'),$user->avatarCollection,$user);
            }
          return redirect('/admin-dashboard/admins');

    }
 

    public function edit($id){
        $user=User::where('id',$id)->first();
        $user->image = getFirstMediaUrl($user,$user->avatarCollection);
       // $roles=Role::all();
        return view('dashboard.admins.edit',compact('user'));
    }

    public function update(Request $request,$id){
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:191'],
            'username' => ['required'],
            'email' => ['required', 'string', 'email', 'max:191', 'unique:users,email,' . $id],
            'password' => ['nullable', 'string', 'min:8'],
            'image' => ['nullable'] ,
            'phone_number' => ['nullable', 'unique:users,phone,' . $id, 'numeric'],
            // 'role'=>['required',Rule::in(Role::pluck('id'))],
            'address'=> ['nullable']
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }
        
        User::where('id',$id)->update([  'name' => $request->name,
                                            'username' => $request->username,
                                            'email'=> $request->email ,
                                            'phone'=>$request->phone_number,
                                            'address'=>$request->address]);
        $user=User::find($id);
        if($request->has('password') && $request->password != null){
            $user->password= Hash::make($request->password);
            $user->save();
        }
        // $role = Role::findById($request->role);
        // $user->syncRoles([$role->name]);
        if($request->file('image')){
            $image=getFirstMediaUrl($user,$user->avatarCollection);
            if($image!= null){
                deleteMedia($user,$user->avatarCollection);
                uploadMedia($request->image,$user->avatarCollection,$user);
            }else{
                uploadMedia($request->image,$user->avatarCollection,$user);
            }
        }
        return redirect('/admin-dashboard/admins');

    }


   

    public function delete($id)
    {
        User::where('id', $id)->delete();
        return redirect('/admin-dashboard/admins');
    }
}