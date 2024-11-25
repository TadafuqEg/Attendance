@extends('dashboard.layout.app')
@section('title', 'Dashboard - evaluations')
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
                            
                            <form id="searchForm" class="search-bar" style="margin-bottom:1%;margin-right:20px;margin-left:0px;"method="post" action="{{ route('evaluations') }}" enctype="multipart/form-data">
                                @csrf
                                <div style="display:flex;">
                                  <h5 class="card-title" style="width: 55%;">Evaluations</h5>
                                  <div style="display:flex;margin-bottom:1%;margin-left:0px;">
                                    <button  class="btn btn-light px-5" type="button" style="margin:0% 0% 1% 1%; width:25%;"onclick="exportData()">Export<i class="bi bi-download"></i> </button>
                                    <input type="hidden" id="export" name="export" value="0">
                                    <button class="btn btn-light px-5" type="button" onclick="toggleFilters()"style="margin:0% 1% 1% 1%; ">Filter</button>
                                    <input type="text" class="form-control" placeholder="Enter keywords" name="search">
                                    <a href="javascript:void(0);" id="submitForm"><i class="icon-magnifier"></i></a>
                                  </div>
                                  
                                </div>
                                
                                
                                <div id="filterOptions" style="display: none; text-align:center;">
                                    <div style="display: flex; justify-content: center; align-items: center;">
                                      
                                        <select class="form-control" style="width: 23.5%; margin: 2% 1% 0% 0%;" name="user">
                                            <option value=""@if($user_search==''||$user_search==null) selected @endif>Select Employee</option>
                                            @foreach($users as $user)
                                                <option value="{{$user->id}}" @if($user_search==$user->id) selected @endif>{{$user->name}}</option>
                                            @endforeach
                                            <!-- Add more options as needed -->
                                        </select>
                                        <div class="form-group"style="width: 23.5%; margin: 0% 1% 0% 1%;">
                                          <label for="date" style="display: block;">From</label>
                                            <input type="month" name="date_from" class="form-control"   style="width:100%;" @if($date_from_search!=''&& $date_from_search!=null) value="{{$date_from_search}}" @endif>
                                            
                                          </div>
                                          <div class="form-group"style="width: 23.5%; margin: 0% 1% 0% 1%;">
                                            <label for="date" style="display: block;">to</label>
                                            <input type="month" name="date_to" class="form-control"   style="width:100%;" @if($date_to_search!=''&& $date_to_search!=null) value="{{$date_to_search}}" @endif>
                                            
                                          </div>
                                        
                                        
                                        
                                        <select class="form-control"style="width: 23.5%;margin: 2% 0% 0% 1%;" name="status">
                                            <option value=""@if($status_search==''||$status_search==null) selected @endif>Select Status</option>
                                            <option value="Needs_improvement"@if($status_search=='Needs_improvement') selected @endif>Needs improvement</option>
                                            <option value="acceptable"@if($status_search=='acceptable') selected @endif>Acceptable</option>
                                            <option value="good"@if($status_search=='good') selected @endif>Good</option>
                                            <option value="very_good"@if($status_search=='very_good') selected @endif>Very Good</option>
                                            
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
                       
                        @if(session('error'))
                        <div id="errorAlert" class="alert alert-danger" style="padding-top:5px;padding-bottom:5px; padding-left: 10px; background-color:brown;border-radius: 20px; color:beige;">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    @if(session('success'))
                        <div id="successAlert" class="alert alert-success"style="padding-top:5px;padding-bottom:5px; padding-left: 10px; background-color:green;border-radius: 20px; color:beige;">
                            {{ session('success') }}
                        </div>
                    @endif
                        <div class="table-responsive">
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              
                              <th scope="col">Employee Name</th>
                              <th scope="col">Job Title</th>
                              <th scope="col">Month</th>
                              <th scope="col">Evaluation</th>
                              <th scope="col">Status</th>
                            
                            </tr>
                          </thead>
                          <tbody>
                            @if(!empty($all_evaluations) && $all_evaluations->count())
                            @foreach($all_evaluations as $evaluation)
                              <tr>
                                <td><span class="user-profile"><img @if(getFirstMediaUrl($evaluation->user,$evaluation->user->avatarCollection)!=null) src="{{getFirstMediaUrl($evaluation->user,$evaluation->user->avatarCollection)}}" @else src="{{asset('dashboard/user_avatar.png')}}" @endif class="img-circle" alt="user avatar"></span> {{$evaluation->user->name}}</td>
                                <td>{{$evaluation->user->username}}</td>
                                
                                <td>{{$evaluation->month}} / {{$evaluation->year}}</td>
                                <td>{{$evaluation->evaluation}} %</td>
                                
                                <td>@if($evaluation->evaluation>=76) <span class="badge badge-secondary" style="background-color:rgb(16, 124, 6); width:100%;">Very Good</span> @elseif($evaluation->evaluation>=51) <span class="badge badge-secondary" style="background-color:rgb(212, 200, 33);width:100%;">Good</span> @elseif($evaluation->evaluation>=41) <span class="badge badge-secondary" style="background-color:rgb(14, 95, 218);width:100%;">Acceptable</span> @else <span class="badge badge-secondary" style="background-color:rgb(218, 62, 14);width:100%;">Needs improvement</span> @endif</td>

                                
                                {{-- <td>
                                  
                                  
                                 
                                  <a href="{{url('/admin-dashboard/evaluation/view/'.$evaluation->id)}}" style="margin-right: 1rem;">
                                    <span  class="bi bi-eye" style="font-size: 1rem; color: rgb(255,255,255);"></span>
                                  </a>
                                 
                                  <a href="{{url('/admin-dashboard/attendance/delete/'.$attendance->id)}}">
                                    <span class="bi bi-trash" style="font-size: 1rem; color: rgb(255,255,255);"></span>
                                  </a> 
                                 
                                  
                                </td> --}}
                              </tr>
                            @endforeach
                          @else
                              <tr>
                                <td>There are no evaluattins.</td>
                              </tr>
                          @endif
                          </tbody>
                        </table>
                        <div style="text-align: center;">
                          {!! $all_evaluations->appends(['search' => request('search'),'user'=>request('user'),'date_from'=>request('date_from'),'date_to'=>request('date_to'),'status'=>request('status')])->links("pagination::bootstrap-4") !!}
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
   <script>
    // Set a timeout to hide the error or success message after 5 seconds
    setTimeout(function() {
        $('#errorAlert').fadeOut();
        $('#successAlert').fadeOut();
    }, 4000); // 5000 milliseconds = 5 seconds
</script>
 
@endpush
