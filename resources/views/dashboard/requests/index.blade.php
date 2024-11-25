@extends('dashboard.layout.app')
@section('title', 'Dashboard - leave requests')
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
                            
                            <form id="searchForm" class="search-bar" style="margin-bottom:1%;margin-right:20px;margin-left:0px;"method="post" action="{{ route('leave_requests') }}" enctype="multipart/form-data">
                                @csrf
                                <div style="display:flex;">
                                  <h5 class="card-title" style="width: 65%;">Leave Requests</h5>
                                  <div style="display:flex;margin-bottom:1%;margin-left:0px;">
                                    {{-- <button  class="btn btn-light px-5" type="button" style="margin:0% 0% 1% 1%; width:25%;"onclick="exportData()">Export<i class="bi bi-download"></i> </button> --}}
                                    <input type="hidden" id="export" name="export" value="0">
                                    <button class="btn btn-light px-5" type="button" onclick="toggleFilters()"style="margin:0% 1% 1% 1%; ">Filter</button>
                                    <input type="text" class="form-control" placeholder="Enter keywords" name="search">
                                    <a href="javascript:void(0);" id="submitForm"><i class="icon-magnifier"></i></a>
                                  </div>
                                  
                                </div>
                                
                                
                                <div id="filterOptions" style="display: none; text-align:center;">
                                    <div style="display: flex; justify-content: center; align-items: center;">
                                      
                                        <select class="form-control" style="width: 23.5%; margin: 0% 1% 0% 0%;" name="user">
                                            <option value=""@if($user_search==''||$user_search==null) selected @endif>Select Employee</option>
                                            @foreach($users as $user)
                                                <option value="{{$user->id}}" @if($user_search==$user->id) selected @endif>{{$user->name}}</option>
                                            @endforeach
                                            <!-- Add more options as needed -->
                                        </select>
                                        <select class="form-control"style="width: 23.5%;margin: 0% 0% 0% 1%;" name="type">
                                            <option value=""@if($type_search==''||$type_search==null) selected @endif>Select Type</option>
                                            
                                            <option value="emergency_vacation"@if($type_search=='emergency_vacation') selected @endif>Casual Leave</option>
                                            <option value="ordinary_vacation"@if($type_search=='ordinary_vacation') selected @endif>Regular Leave</option>
                                            <option value="sick_vacation"@if($type_search=='sick_vacation') selected @endif>Sick Leave</option>
                                            
                                            <!-- Add more options as needed -->
                                        </select>
                                        
                                        
                                        
                                        <select class="form-control"style="width: 23.5%;margin: 0% 0% 0% 1%;" name="status">
                                            <option value=""@if($status_search==''||$status_search==null) selected @endif>Select Status</option>
                                            <option value="pending"@if($status_search=='pending') selected @endif>Pending</option>
                                            <option value="accepted"@if($status_search=='accepted') selected @endif>Accepted</option>
                                            <option value="rejected"@if($status_search=='rejected') selected @endif>Rejected</option>
                                            
                                            
                                            <!-- Add more options as needed -->
                                        </select>
                                        {{-- <div class="form-group py-2"style="width: 23.5%; margin: 0% 0% 0% 0%;">
                                            <div class="icheck-material-white">
                                                <input type="checkbox"name="air_conditioned" id="user-checkbox2"/>
                                                <label for="user-checkbox2">Air conditioned</label>
                                            </div>
                                          </div> --}}
                                      </div>
                                    
                                    
                                    <button class="btn btn-light px-5" style="margin-top:10px" type="button" onclick="filter()">Apply Filters</button>
                                </div>
                            </form>
                            {{-- <a  class="btn btn-light px-5" style="margin-bottom:1%; " href="{{route('add.user')}}">create</a> --}}
                        </div>
                       
                        <div class="table-responsive">
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th scope="col">Code</th>
                              <th scope="col">Employye Name</th>
                              <th scope="col">Date From</th>
                              <th scope="col">Date To</th>
                              <th scope="col">Type</th>
                              <th scope="col">Status</th>
                              <th scope="col">Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            @if(!empty($all_requests) && $all_requests->count())
                            @foreach($all_requests as $leave_request)
                              <tr>
                                <td>{{$leave_request->code}}</td>
                                <td><span class="user-profile"><img @if(getFirstMediaUrl($leave_request->user,$leave_request->user->avatarCollection)!=null) src="{{getFirstMediaUrl($leave_request->user,$leave_request->user->avatarCollection)}}" @else src="{{asset('dashboard/user_avatar.png')}}" @endif class="img-circle" alt="user avatar"></span> {{$leave_request->user->name}}</td>
                                <td>{{$leave_request->from}}</td>
                                
                                <td>{{$leave_request->to}}</td>
                                <td>@if($leave_request->type=='sick_vacation') Sick Leave @elseif($leave_request->type=='ordinary_vacation') Regular Leave @elseif($leave_request->type=='emergency_vacation') Casual Leave @endif</td>
                                
                                <td>@if($leave_request->status=='accepted') <span class="badge badge-secondary" style="background-color:rgb(16, 124, 6); width:100%;">Accepted</span> @elseif($leave_request->status=='rejected') <span class="badge badge-secondary" style="background-color:rgb(194, 55, 0);width:100%;">Rejected</span> @elseif($leave_request->status=='pending') <span class="badge badge-secondary" style="background-color:rgb(206, 188, 29);width:100%;">Pending</span>  @endif</td>

                                
                                <td>
                                  
                                  
                                 
                                  <a href="{{url('/admin-dashboard/leave_request/edit/'.$leave_request->id)}}" style="margin-right: 1rem;">
                                    <span  class="bi bi-pen" style="font-size: 1rem; color: rgb(255,255,255);"></span>
                                  </a>
                                 
                                  {{-- <a href="{{url('/admin-dashboard/leave_request/delete/'.$leave_request->id)}}">
                                    <span class="bi bi-trash" style="font-size: 1rem; color: rgb(255,255,255);"></span>
                                  </a> --}}
                                 
                                  
                                </td>
                              </tr>
                            @endforeach
                          @else
                              <tr>
                                <td>There are no Leave Requests.</td>
                              </tr>
                          @endif
                          </tbody>
                        </table>
                        <div style="text-align: center;">
                          {!! $all_requests->appends(['search' => request('search'),'user'=>request('user'),'type'=>request('type'),'status'=>request('status')])->links("pagination::bootstrap-4") !!}
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
    <script>
      function toggleFilters() {
          var filterOptions = document.getElementById("filterOptions");
          if (filterOptions.style.display === "none") {
              filterOptions.style.display = "block";
          } else {
              filterOptions.style.display = "none";
          }
      }
      function exportData() {
        document.getElementById('export').value = 1;
        document.getElementById('searchForm').submit();
      }
      function filter() {
        document.getElementById('export').value = 0;
        document.getElementById('searchForm').submit();
      }
      
  </script>
 
@endpush
