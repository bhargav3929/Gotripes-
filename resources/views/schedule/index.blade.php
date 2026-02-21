    <!-- Custom fonts for this template-->
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css" rel="stylesheet" />
    <link href="{{ asset('backend/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('backend/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include jQuery UI -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@section('content')

    <div class="card text-dark bg-gradient-success mb-3">
        
    <div class="container ">
        {{-- For Search --}}

 <div class="row"> 

 <div class="col-md-3 mt-5">
            <div class="input-group " >
                <div style="width: 100px;">
                 <a href="{{ route('admin.appointments.index') }}" class="btn btn-primary btn-sm shadow-sm ">{{ __('❮  Go Back') }}</a>
                </div>
                   
            </div>
 </div> 

        <div class="col-md-6 mt-4">
            <div class="input-group ">
                <input type="text" id="searchInput" class="form-control" placeholder="Search details">
                    <div class="input-group-append">
                        <button id="searchButton" class="btn btn-primary">Search</button>
                    </div>&nbsp;&nbsp;
                        <select type="text" id="searchInput1" class="form-control" >
                            <option style="color:black" value=""><b>-- All Conventions --</b></option>
                            @foreach($conventions as  $convention)
                                <option > {{ $convention }}</option>
                            @endforeach
                        </select>
                       <button id="searchButton1" class="btn btn-primary btn-sm shadow-sm">Search</button>
            </div>
            
        </div>
        <div class="col-md-3">
    <div class="d-flex flex-column">
        <div class="d-flex align-items-center">
            <div style="width: 20px; height: 20px; background-color: #c2ff33; margin-right: 10px;"></div>
            <span> ⟶ <b>Morning (1 AM to 4 PM)</b></span>
        </div>
        <div class="d-flex align-items-center">
            <div style="width: 20px; height: 20px; background-color: #c459da; margin-right: 10px;"></div>
            <span> ⟶ <b>Evening (6 PM to 12 PM)</b></span>
        </div>
        <div class="d-flex align-items-center">
            <div style="width: 20px; height: 20px; background-color: #4b3610; margin-right: 10px;"></div>
            <span> ⟶ <b>Other (____ to ____)</b></span>
        </div>
    </div>
</div>

 </div>  
        

        <div class="card shadow">
            <div class="card-body " >
                <div id="calendar" style="width: auto; height: auto;"></div>

            </div>
        </div>
        
</div>
    </div>


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var calendarEl = document.getElementById('calendar');
    var events = [];
    var calendar = new FullCalendar.Calendar(calendarEl, {
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            //right: 'dayGridMonth,timeGridWeek,timeGridDay'
            right: 'dayGridMonth'
        },
        initialView: 'dayGridMonth',
        timeZone: 'UTC',
        events: '/events',
        editable: true,

        // Deleting The Event
        eventContent: function(info) {
            var eventTitle = info.event.title;
            var eventElement = document.createElement('div');
            //eventElement.innerHTML = '<span style="cursor: pointer;">❌</span> ' + eventTitle;
            eventElement.innerHTML = '<span style="cursor: pointer;"></span> ' + eventTitle;

            eventElement.querySelector('span').addEventListener('click', function() {
                if (confirm("Are you sure you want to delete this event?")) {
                    var eventId = info.event.id;
                    $.ajax({
                        method: 'DELETE',
                        url: '/schedule/' + eventId,
                        success: function(response) {
                            console.log('Event deleted successfully.');
                            calendar.refetchEvents(); // Refresh events after deletion
                        },
                        error: function(error) {
                            console.error('Error deleting event:', error);
                        }
                    });
                }
            });
            return {
                domNodes: [eventElement]
            };
        },

        // Drag And Drop

        eventDrop: function(info) {
            var eventId = info.event.id;
            var newStartDate = info.event.start;
            var newEndDate = info.event.end || newStartDate;
            var newStartDateUTC = newStartDate.toISOString().slice(0, 10);
            var newEndDateUTC = newEndDate.toISOString().slice(0, 10);

            $.ajax({
                method: 'PUT',
                url: `/schedule/${eventId}`,
                data: {
                    start_date: newStartDateUTC,
                    end_date: newEndDateUTC,
                },
                success: function() {
                    console.log('Event moved successfully.');
                },
                error: function(error) {
                    console.error('Error moving event:', error);
                }
            });
        },

        // Event Resizing
        eventResize: function(info) {
            var eventId = info.event.id;
            var newEndDate = info.event.end;
            var newEndDateUTC = newEndDate.toISOString().slice(0, 10);

            $.ajax({
                method: 'PUT',
                url: `/schedule/${eventId}/resize`,
                data: {
                    end_date: newEndDateUTC
                },
                success: function() {
                    console.log('Event resized successfully.');
                },
                error: function(error) {
                    console.error('Error resizing event:', error);
                }
            });
        },
    });

    calendar.render();

    document.getElementById('searchButton').addEventListener('click', function() {
        var searchKeywords = document.getElementById('searchInput').value.toLowerCase();
        filterAndDisplayEvents(searchKeywords);
    });


    function filterAndDisplayEvents(searchKeywords) {
        $.ajax({
            method: 'GET',
            url: `/events/search?title=${searchKeywords}`,
            success: function(response) {
                calendar.removeAllEvents();
                calendar.addEventSource(response);
            },
            error: function(error) {
                console.error('Error searching events:', error);
            }
        });
    }



    // Exporting Function
    document.getElementById('exportButton').addEventListener('click', function() {
        var events = calendar.getEvents().map(function(event) {
            return {
                title: event.title,
                start: event.start ? event.start.toISOString() : null,
                end: event.end ? event.end.toISOString() : null,
                color: event.backgroundColor,
            };
        });

        var wb = XLSX.utils.book_new();

        var ws = XLSX.utils.json_to_sheet(events);

        XLSX.utils.book_append_sheet(wb, ws, 'Events');

        var arrayBuffer = XLSX.write(wb, {
            bookType: 'xlsx',
            type: 'array'
        });

        var blob = new Blob([arrayBuffer], {
            type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        });

        var downloadLink = document.createElement('a');
        downloadLink.href = URL.createObjectURL(blob);
        downloadLink.download = 'events.xlsx';
        downloadLink.click();
    })


</script>

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var calendarEl = document.getElementById('calendar');
    var events = [];
    var calendar = new FullCalendar.Calendar(calendarEl, {
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            //right: 'dayGridMonth,timeGridWeek,timeGridDay'
            right: 'dayGridMonth'
        },
        initialView: 'dayGridMonth',
        timeZone: 'UTC',
        events: '/events',
        editable: true,

        // Deleting The Event
        eventContent: function(info) {
            var eventTitle = info.event.title;
            var eventElement = document.createElement('div');
            //eventElement.innerHTML = '<span style="cursor: pointer;">❌</span> ' + eventTitle;
            eventElement.innerHTML = '<span style="cursor: pointer;"></span> ' + eventTitle;

            eventElement.querySelector('span').addEventListener('click', function() {
                if (confirm("Are you sure you want to delete this event?")) {
                    var eventId = info.event.id;
                    $.ajax({
                        method: 'DELETE',
                        url: '/schedule/' + eventId,
                        success: function(response) {
                            console.log('Event deleted successfully.');
                            calendar.refetchEvents(); // Refresh events after deletion
                        },
                        error: function(error) {
                            console.error('Error deleting event:', error);
                        }
                    });
                }
            });
            return {
                domNodes: [eventElement]
            };
        },

        // Drag And Drop

        eventDrop: function(info) {
            var eventId = info.event.id;
            var newStartDate = info.event.start;
            var newEndDate = info.event.end || newStartDate;
            var newStartDateUTC = newStartDate.toISOString().slice(0, 10);
            var newEndDateUTC = newEndDate.toISOString().slice(0, 10);

            $.ajax({
                method: 'PUT',
                url: `/schedule/${eventId}`,
                data: {
                    start_date: newStartDateUTC,
                    end_date: newEndDateUTC,
                },
                success: function() {
                    console.log('Event moved successfully.');
                },
                error: function(error) {
                    console.error('Error moving event:', error);
                }
            });
        },

        // Event Resizing
        eventResize: function(info) {
            var eventId = info.event.id;
            var newEndDate = info.event.end;
            var newEndDateUTC = newEndDate.toISOString().slice(0, 10);

            $.ajax({
                method: 'PUT',
                url: `/schedule/${eventId}/resize`,
                data: {
                    end_date: newEndDateUTC
                },
                success: function() {
                    console.log('Event resized successfully.');
                },
                error: function(error) {
                    console.error('Error resizing event:', error);
                }
            });
        },
    });

    calendar.render();

    document.getElementById('searchButton1').addEventListener('click', function() {
        var searchKeywords = document.getElementById('searchInput1').value.toLowerCase();
        filterAndDisplayEvents(searchKeywords);
    });


    function filterAndDisplayEvents(searchKeywords) {
        $.ajax({
            method: 'GET',
            url: `/events/search?title=${searchKeywords}`,
            success: function(response) {
                calendar.removeAllEvents();
                calendar.addEventSource(response);
            },
            error: function(error) {
                console.error('Error searching events:', error);
            }
        });
    }



    // Exporting Function
    document.getElementById('exportButton').addEventListener('click', function() {
        var events = calendar.getEvents().map(function(event) {
            return {
                title: event.title,
                start: event.start ? event.start.toISOString() : null,
                end: event.end ? event.end.toISOString() : null,
                color: event.backgroundColor,
            };
        });

        var wb = XLSX.utils.book_new();

        var ws = XLSX.utils.json_to_sheet(events);

        XLSX.utils.book_append_sheet(wb, ws, 'Events');

        var arrayBuffer = XLSX.write(wb, {
            bookType: 'xlsx',
            type: 'array'
        });

        var blob = new Blob([arrayBuffer], {
            type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        });

        var downloadLink = document.createElement('a');
        downloadLink.href = URL.createObjectURL(blob);
        downloadLink.download = 'events.xlsx';
        downloadLink.click();
    })


</script>


{{-- @endsection --}}
