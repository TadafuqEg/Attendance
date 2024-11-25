@extends('dashboard.layout.app')
@section('title', 'Dashboard - admins')
@section('content')	
<style>
    .pagination{
        display: inline-flex;
    }
    .user-status {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-left: -4%;
        margin-bottom: 4.65%;
    }

    .online {
        background-color: green;
    }

    .offline {
        background-color: gray;
    }
</style>
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                      <div class="card-body">
                        <div>
                            
                            <form id="searchForm" class="search-bar" style="margin-bottom:1%;margin-right:20px;margin-left:0px;"method="post" action="{{ route('admins') }}" enctype="multipart/form-data">
                                @csrf
                                <div style="display:flex;">
                                  <h5 class="card-title" style="width: 60%;">Admins</h5>
                                  <div style="display:flex;margin-bottom:1%;margin-left:0px;">
                                    <a  class="btn btn-light px-5" style="margin:0% 1% 1% 1%; " href="{{route('add.admin')}}">create</a>
                                    {{-- <button class="btn btn-light px-5" type="button" onclick="toggleFilters()"style="margin:0% 1% 1% 1%; ">Filter</button> --}}
                                    <input type="text" class="form-control" placeholder="Enter keywords" name="search">
                                    <a href="javascript:void(0);" id="submitForm"><i class="icon-magnifier"></i></a>
                                  </div>
                                  
                                </div>
                                
                                
                                {{-- <div id="filterOptions" style="display: none; text-align:center;">
                                  <div style="display: flex; justify-content: center; align-items: center;">

                                    <select class="form-control"style="width: 33%;" name="role">
                                        <option value="">Select Role</option>
                                        <option value="Admin">Admin</option>
                                        <option value="Client">Client</option>
                                        <!-- Add more options as needed -->
                                    </select>
                                    
                                    
                                  </div>
                                    
                                    
                                    <button class="btn btn-light px-5" style="margin-top:10px" type="submit">Apply Filters</button>
                                </div> --}}
                            </form>
                            {{-- <a  class="btn btn-light px-5" style="margin-bottom:1%; " href="{{route('add.user')}}">create</a> --}}
                        </div>
                       
                        <div class="table-responsive">
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              
                              <th scope="col">Name</th>
                              <th scope="col">Email</th>
                              <th scope="col">Phone Number</th>
                              <th scope="col">Job Title</th>
                              
                              <th scope="col">Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            @if(!empty($all_users) && $all_users->count())
                            @foreach($all_users as $user)
                              <tr>
                                <td><span class="user-profile"><img @if(getFirstMediaUrl($user,$user->avatarCollection)!=null) src="{{getFirstMediaUrl($user,$user->avatarCollection)}}" @else src="{{asset('dashboard/user_avatar.png')}}" @endif class="img-circle" alt="user avatar"></span> {{$user->name}}</td>
                                <td>{{$user->email}}</td>
                                
                                <td>{{$user->phone}}</td>
                                <td>{{$user->username}}</td>
                                
                                {{-- <td>{{$user->roles->first()->name}}</td> --}}
                                
                                <td>
                                  
                                  
                                 
                                  <a href="{{url('/admin-dashboard/admin/edit/'.$user->id)}}" style="margin-right: 1rem;">
                                    <span  class="bi bi-pen" style="font-size: 1rem; color: rgb(255,255,255);"></span>
                                  </a>
                                 
                                  <a href="{{url('/admin-dashboard/admin/delete/'.$user->id)}}">
                                    <span class="bi bi-trash" style="font-size: 1rem; color: rgb(255,255,255);"></span>
                                  </a>
                                 
                                  
                                </td>
                              </tr>
                            @endforeach
                          @else
                              <tr>
                                <td>There are no Admins.</td>
                              </tr>
                          @endif
                          </tbody>
                        </table>
                        <div style="text-align: center;">
                          {!! $all_users->appends(['search' => request('search')])->links("pagination::bootstrap-4") !!}
                        </div>
                      </div>
                      </div>
                    </div>
                  </div>
            </div>
            <div class="overlay toggle-menu"></div>
        </div>
    </div>
@endsection
@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#submitForm').on('click', function() {
            $('#searchForm').submit();
        });
    });
    </script>
    {{-- <script>
      function toggleFilters() {
          var filterOptions = document.getElementById("filterOptions");
          if (filterOptions.style.display === "none") {
              filterOptions.style.display = "block";
          } else {
              filterOptions.style.display = "none";
          }
      }
  
      
  </script> --}}
@endpush
