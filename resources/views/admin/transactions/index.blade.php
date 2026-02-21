@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
   

    <!-- Content Row -->
        <div class="card">
            <div class="card-header py-3 d-flex">
                <h6 class="m-0 font-weight-bold text-primary">
                    {{ __('View File Status') }}
                </h6>
                {{-- <div class="ml-auto">
                    <a href="{{ route('admin.transactions.create') }}" class="btn btn-light bg-gradient-info">
                        <span class="icon text-white">
                            <i class="fa fa-plus"></i>
                        </span>
                        <span class="text-white">{{ __('New Transactions') }}</span>
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
                                <th>Referee Mobile</th>
                           
                                <th>Previous State</th>
                                <th>Current State</th>
                                <th>Created Date</th>
                                
                                <th>Comment</th>
                                {{-- <th>Action</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                            <tr data-entry-id="{{ $transaction->id }}">
                                <td>

                                </td>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $transaction->refereeName ?? 'N/A' }}</td>
                                <td>{{ $transaction->refereeMobile ?? 'N/A' }}</td>
                                
                                <td>{{ $transaction->previousstate->name ?? 'N/A' }}</td>
                                <td>{{ $transaction->currentstate->name ?? 'N/A'}}</td>
                                <td>{{ $transaction->created_at}}</td>
                                                               {{-- <td>
                                    @if($transaction->sms == '0')
                                        NO
                                    @else
                                        YES
                                    @endif
                                </td>
                               <td>
                                    @if($transaction->email == '0')
                                        NO
                                    @else
                                        YES
                                    @endif
                                </td> --}}
                                <td>{{ $transaction->account ?? 'N/A' }}</td>
                               
                               
                                {{-- <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.transactions.edit', $transaction->id)}}" class="btn btn-info">
                                            <i class="fa fa-pencil-alt"></i>
                                        </a>
                                        <form onclick="return confirm('are you sure ? ')" class="d-inline" action="{{ route('admin.transactions.destroy', $transaction->id) }}" method="POST">
                                            @csrf
                                            @method('delete')
                                            <button class="btn btn-danger" style="border-top-left-radius: 0;border-bottom-left-radius: 0;">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td> --}}
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
    url: "{{ route('admin.transactions.mass_destroy') }}",
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