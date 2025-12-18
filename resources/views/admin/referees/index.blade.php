@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
   

    <!-- Content Row -->
        <div class="card">
            <div class="card-header py-3 d-flex">
                <h6 class="m-0 font-weight-bold text-primary">
                    {{ __('Manage Referee Status') }}
                </h6>
                {{-- <div class="ml-auto">
                    <a href="{{ route('admin.referees.create') }}" class="btn btn-light bg-gradient-info">
                        <span class="icon text-white">
                            <i class="fa fa-plus"></i>
                        </span>
                        <span class="text-white">{{ __('New Referee') }}</span>
                    </a>
                </div> --}}
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover datatable datatable-Service" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th >

                                </th>
                                <th>NO</th>
                                <th>Referee Name</th>
                                <th>Referrer Name</th>
                                <th>Referee Email</th>
                                <th>Referee Mobile</th>
                                {{-- <th>Referee Address</th> --}}
                                <th>Loan Type </th>
                                <th>Previous State</th>
                                <th>Current State</th>
                                <th>Update Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($referees as $referee)
                            <tr data-entry-id="{{ $referee->id }}">
                                <td>

                                </td>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $referee->refereeName }}</td>
                                <td>{{ $referee->referrer->referrerName ?? 'N/A' }}</td>
                                <td>{{ $referee->refereeEmail }}</td>
                                <td>{{ $referee->refereeMobile }}</td>
                                {{-- <td>{{ $referee->refereeAddress }}</td> --}}

                                <td>{{ $referee->loanType->tolName ?? 'N/A'  }}</td>
                               <td>{{ $referee->previousstate->name ?? 'N/A' }}</td>
                                <td>{{ $referee->currentstate->name ?? 'N/A' }}</td>
                               
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.referees.edit', $referee->id)}}" class="btn btn-info">
                                            <i class="fa fa-pencil-alt">Status</i>
                                        </a>
                                        {{-- <form onclick="return confirm('are you sure ? ')" class="d-inline" action="{{ route('admin.referees.destroy', $referee->id) }}" method="POST">
                                            @csrf
                                            @method('delete')
                                            <button class="btn btn-danger" style="border-top-left-radius: 0;border-bottom-left-radius: 0;">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form> --}}
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">{{ __('Data Empty') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <!-- Content Row -->

</div>
@endsection

 @push('script-alt')
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
  let deleteButtonTrans = 'delete selected'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.referees.mass_destroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });
      if (ids.length === 0) {
        alert('zero selected')
        return
      }
      if (confirm('are you sure ?')) {
        $.ajax({
          headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
          method: 'POST',
          url: config.url,
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
//   dtButtons.push(deleteButton)
  $.extend(true, $.fn.dataTable.defaults, {
    order: [[ 1, 'asc' ]],
    pageLength: 50,
  });
  $('.datatable-Service:not(.ajaxTable)').DataTable({ buttons: dtButtons })
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });
})
</script>
@endpush 