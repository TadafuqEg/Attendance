@extends('dashboard.layout.app')
@section('title', 'Lady Driver - Admin Home')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
  .container {
    width: 100%;
    
    text-align: center;
}

canvas {
    margin-top: 20px;
}
</style>
<div class="content-wrapper">
    <div class="container-fluid">
      <div class="card mt-3 row" style="flex-direction: row;background-color: rgba(0, 0, 0, .0);">
        <div style="width: 23.5%;text-align:center;margin:0% 1% 0% 0%; padding:10px 0px 10px 0px;border-radius: 0.5rem;background-color: rgba(0, 0, 0, .3);">
          <h3>Employees</h3>
          <h4>{{$emp_count}}</h4>
        </div>
        <div style="width: 23.5%;text-align:center;margin:0% 1% 0% 1%;padding:10px 0px 10px 0px;border-radius: 0.5rem;background-color: rgba(0, 0, 0, .3);">
          <h3>Attendance</h3>
          <h4>{{$attendance}}</h4>
        </div>
        <div style="width: 23.5%;text-align:center;margin:0% 1% 0% 1%;padding:10px 0px 10px 0px;border-radius: 0.5rem;background-color: rgba(0, 0, 0, .3);">
          <h3>Absence</h3>
          <h4>{{$absence}}</h4>
        </div>
        <div style="width: 23.5%;text-align:center;margin:0% 0% 0% 1%;padding:10px 0px 10px 0px;border-radius: 0.5rem;background-color: rgba(0, 0, 0, .3);">
          <h3>Day Off</h3>
          <h4>{{$vacation}}</h4>
        </div>
      </div>
      <div class="card mt-3 row" style="flex-direction: row;background-color: rgba(0, 0, 0, .0);justify-content: center;">
        <div style="width: 23.5%;text-align:center;margin:0% 1% 0% 0%; padding:10px 0px 10px 0px;border-radius: 0.5rem;background-color: rgba(0, 0, 0, .3);">
          <h3>Sick Leave</h3>
          <h4>{{$sick_vacation}}</h4>
        </div>
        <div style="width: 23.5%;text-align:center;margin:0% 1% 0% 1%;padding:10px 0px 10px 0px;border-radius: 0.5rem;background-color: rgba(0, 0, 0, .3);">
          <h3>Casual Leave</h3>
          <h4>{{$emergency_vacation}}</h4>
        </div>
        <div style="width: 23.5%;text-align:center;margin:0% 1% 0% 1%;padding:10px 0px 10px 0px;border-radius: 0.5rem;background-color: rgba(0, 0, 0, .3);">
          <h3>Regular Leave</h3>
          <h4>{{$ordinary_vacation}}</h4>
        </div>
      </div>
      <div class="container">
        <h2>Employee Attendance</h2>
        <h3>{{date('"F Y"')}}</h3>
        <canvas id="attendanceChart"></canvas>
    </div>
      <!--Start Dashboard Content-->

      <!--<div class="card mt-3">
        <div class="card-content">
            <div class="row row-group m-0">
                <div class="col-12 col-lg-6 col-xl-3 border-light">
                    <div class="card-body">
                      <h5 class="text-white mb-0">9526 <span class="float-right"><i class="fa fa-shopping-cart"></i></span></h5>
                        <div class="progress my-3" style="height:3px;">
                          <div class="progress-bar" style="width:55%"></div>
                        </div>
                      <p class="mb-0 text-white small-font">Total Orders <span class="float-right">+4.2% <i class="zmdi zmdi-long-arrow-up"></i></span></p>
                    </div>
                </div>
                <div class="col-12 col-lg-6 col-xl-3 border-light">
                    <div class="card-body">
                      <h5 class="text-white mb-0">8323 <span class="float-right"><i class="fa fa-usd"></i></span></h5>
                        <div class="progress my-3" style="height:3px;">
                          <div class="progress-bar" style="width:55%"></div>
                        </div>
                      <p class="mb-0 text-white small-font">Total Revenue <span class="float-right">+1.2% <i class="zmdi zmdi-long-arrow-up"></i></span></p>
                    </div>
                </div>
                <div class="col-12 col-lg-6 col-xl-3 border-light">
                    <div class="card-body">
                      <h5 class="text-white mb-0">6200 <span class="float-right"><i class="fa fa-eye"></i></span></h5>
                        <div class="progress my-3" style="height:3px;">
                          <div class="progress-bar" style="width:55%"></div>
                        </div>
                      <p class="mb-0 text-white small-font">Visitors <span class="float-right">+5.2% <i class="zmdi zmdi-long-arrow-up"></i></span></p>
                    </div>
                </div>
                <div class="col-12 col-lg-6 col-xl-3 border-light">
                    <div class="card-body">
                      <h5 class="text-white mb-0">5630 <span class="float-right"><i class="fa fa-envira"></i></span></h5>
                        <div class="progress my-3" style="height:3px;">
                          <div class="progress-bar" style="width:55%"></div>
                        </div>
                      <p class="mb-0 text-white small-font">Messages <span class="float-right">+2.2% <i class="zmdi zmdi-long-arrow-up"></i></span></p>
                    </div>
                </div>
            </div>
        </div>
      </div>  -->
      {{-- <div style="color:#fff;text-align:center;">
        <canvas id="canvas" width="900" height="800" style="color:#fff;text-align:center;"></canvas>
      </div> --}}
        
      <!--End Row-->
      
     

          <!--End Dashboard Content-->
        
      <!--start overlay-->
      <div class="overlay toggle-menu"></div>
        <!--end overlay-->
    
    </div>
    <!-- End container-fluid-->
    
  </div>
@endsection
@push('scripts')
<script>
   const namesArray = {!! json_encode($names_array) !!};
        const attendanceArray = {!! json_encode($attendance_array) !!};
        const absenceArray = {!! json_encode($absence_array) !!};
        const vacationArray = {!! json_encode($vacation_array) !!};

        const addEmptyDataPoints = (dataArray) => {
            const newDataArray = [];
            for (let i = 0; i < dataArray.length; i++) {
                newDataArray.push(dataArray[i]);
                newDataArray.push(null); // Add an empty data point
            }
            return newDataArray;
        };
        const addEmptyDataPoint = (dataArray) => {
            const newDataArray = [];
            for (let i = 0; i < dataArray.length-1; i++) {
                newDataArray.push(dataArray[i]);
                newDataArray.push(''); // Add an empty data point
            }
            newDataArray.push(dataArray[dataArray.length-1]);
            return newDataArray;
        };
        const spacedNamesArray = addEmptyDataPoint(namesArray);
        
        const spacedAttendanceArray = addEmptyDataPoints(attendanceArray);
        const spacedAbsenceArray = addEmptyDataPoints(absenceArray);
        const spacedVacationArray = addEmptyDataPoints(vacationArray);

        const attendanceData = {
            labels: spacedNamesArray,
            datasets: [
                {
                    label: 'Present Days',
                    data: spacedAttendanceArray,
                    backgroundColor: 'rgba(16, 124, 6, 0.6)',
                    borderColor: 'rgba(16, 124, 6, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Absent Days',
                    data: spacedAbsenceArray,
                    backgroundColor: 'rgba(194, 55, 0, 0.6)',
                    borderColor: 'rgba(194, 55, 0, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Vacation Days',
                    data: spacedVacationArray,
                    backgroundColor: 'rgba(14, 95, 218, 0.6)',
                    borderColor: 'rgba(14, 95, 218, 1)',
                    borderWidth: 1
                }
            ]
        };

// Configuration for the chart
const configx = {
  type: 'bar',
  data: attendanceData,
  options: {
      responsive: true,
      scales: {
          y: {
              beginAtZero: true,
              title: {
                  display: true,
                  text: 'Days',
                  color: 'rgba(14, 95, 218, 0.9)'
              },
              ticks: {
                  color: 'rgba(14, 95, 218, 0.9)' // Y-axis labels color
              }
          },
          x: {
              ticks: {
                  color: 'rgba(14, 95, 218, 0.9)' // X-axis labels color
              }
          }
      },
      plugins: {
          legend: {
              display: true,
              position: 'top',
              labels: {
                  color: 'rgba(14, 95, 218, 0.9)' // Legend labels color
              }
          }
      }
  }
  // options: {
  //     responsive: true,
  //     scales: {
  //         y: {
  //             beginAtZero: true,
  //             title: {
                 
  //                 text: 'Days',
  //                 color: '#fff'
  //             },
  //             ticks: {
  //                 color: '#fff' // Y-axis labels color
  //             }
  //         },
  //         x: {
  //             ticks: {
  //                 color: '#fff' // X-axis labels color
  //             }
  //         }
  //     },
  //     plugins: {
  //         legend: {
             
  //             position: 'top',
  //             labels: {
  //                 color: '#fff' // Legend labels color
  //             }
  //         }
  //     }
  // }
};

// Render the chart
const ctx = document.getElementById('attendanceChart').getContext('2d');
const attendanceChart = new Chart(ctx, configx);
</script>
<script>

//     const canvas = document.getElementById("canvas");
//     const ctx = canvas.getContext("2d");

//     ctx.font = "75px sans-serif"; // Set the font family to sans-serif
// ctx.strokeStyle = "white"; // Set the stroke color to white
// ctx.lineWidth = 3; // Set the stroke width if needed
// ctx.strokeText("Welcome To Dashboard", 50, 90);
  
</script>
@endpush
