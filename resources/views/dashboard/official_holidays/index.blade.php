@extends('dashboard.layout.app')
@section('title', 'Dashboard - official holidays')
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
                            
                            <form id="searchForm" class="search-bar" style="margin-bottom:1%;margin-right:20px;margin-left:0px;"method="post" action="{{ route('official_holidays') }}" enctype="multipart/form-data">
                                @csrf
                                <div style="display:flex;">
                                  <h5 class="card-title" style="width: 65%;">Official Holidays</h5>
                                  <div style="display:flex;margin-bottom:1%;margin-left:0px;">
                                    {{-- <button  class="btn btn-light px-5" type="button" style="margin:0% 0% 1% 1%; width:25%;"onclick="exportData()">Export<i class="bi bi-download"></i> </button> --}}
                                   
                                    
                                    <input type="text" class="form-control" placeholder="Enter keywords" name="search" value="{{$key_search}}">
                                    <a href="javascript:void(0);" id="submitForm"><i class="icon-magnifier"></i></a>
                                  </div>
                                  
                                </div>
                                
                                
                                
                            </form>
                            {{-- <a  class="btn btn-light px-5" style="margin-bottom:1%; " href="{{route('add.user')}}">create</a> --}}
                        </div>
                       
                        <div class="table-responsive">
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th scope="col">Title</th>
                             
                              <th scope="col">Date From</th>
                              <th scope="col">Date To</th>
                              
                              <th scope="col">Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            @if(!empty($all_holidays) && $all_holidays->count())
                            @foreach($all_holidays as $holiday)
                              <tr>
                                <td>{{$holiday->title}}</td>
                                <td>{{$holiday->from}}</td>
                                
                                <td>{{$holiday->to}}</td>
                                

                                
                                <td>
                                  
                                  
                                 
                                  <a href="{{url('/admin-dashboard/official_holidays/edit/'.$holiday->id)}}" style="margin-right: 1rem;">
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
                                <td>There are no Official Holidays.</td>
                              </tr>
                          @endif
                          </tbody>
                        </table>
                        <div style="text-align: center;">
                          {!! $all_holidays->appends(['search' => request('search')])->links("pagination::bootstrap-4") !!}
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
 
@endpush
