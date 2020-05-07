@extends('admin.layout')
@section('content')
    @include('admin.partials.top-bar')

    <div class="page-content-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="card m-b-20">
                        <div class="card-block">
                            <h4 class="ml-3 mt-0 header-title list-inline">
                                <a class="mt-0 list-inline float-right" id="btn_back" href="javascript:history.back(1);"><i class="mdi mdi-arrow-left"></i> {{ __('globals.common.back') }}</a>
                            </h4>
                            <h4 class="mt-0 ml-3 header-title"><a href="{{ route('admins.create') }}">
                                    <button class="btn btn-success waves-effect waves-light">
                                        <i class="ion-plus"></i> Adicionar Novo
                                    </button>
                                </a>
                            </h4>
                            <table id="datatable_user_data" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>E-mail</th>
                                    <th>Saldo(BRL)</th>
                                    <th>Último Acesso</th>
                                    <th>Último IP Acessado</th>
                                    <th>Julgamento Status</th>
                                    <th>Nome da Conta</th>
                                    <th>Plano de Adesão</th>
                                    <th>ClientId</th>
                                    <th>ClinetSecret</th>
                                    <th style="min-width: 50px;">Ações</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($admins as $admin)
                                    <tr>
                                        <td style="padding: 0; display: flex;">
                                            <div style="width: 54px;padding-right: 0;display: flex;align-items: center;padding-left: 5px;padding-top: 5px;padding-bottom: 5px;">
                                                <img src="
                                                @if($admin->avatar == null)
                                                    /assets/img/no-image.png
                                                @else
                                                    /storage/{{ $admin->avatar }}
                                                @endif
                                                                " style="width: 54px; height: 54px; border-radius: 27px;"/>
                                            </div>
                                            <div class="col-md-6" style="padding: 15px 10px; display: flex; align-items: center;">
                                                {{ $admin->name }}
                                            </div>
                                        </td>
                                        <td>{{ $admin->email }}</td>
                                        <td>{{ number_format($admin->access_histories['balance'], 2, '.', ',') }}</td>
                                        @php
                                            $now = time(); // or your date as well
                                            $your_date = strtotime(substr($admin->access_histories['updated_at'], 0, 10));
                                            $datediff = $now - $your_date;
                                            $datediff = round($datediff / (60 * 60 * 24));
                                        @endphp
                                        <td>
                                            @if($datediff < 15)
                                                @if($datediff > 0)
                                                    <span class="badge badge-primary">{{ $datediff }} Dias</span>
                                                @else
                                                    <span class="badge badge-success">Hoje</span>
                                                @endif
                                            @else
                                                <span class="badge badge-danger">{{ $datediff }} Dias</span>
                                            @endif
                                        </td>
                                        <td><span class="badge badge-primary">{{ $admin->access_histories['ip_address'] }}</span></td>
                                        <td>
                                            @php
                                                $trial_version_html = '';
                                                if ($admin->trial_end < date('Y-m-d') && $admin->trial_end != "0000-00-00" && $admin->trial_end != null && $admin->is_subscribed != 1)
                                                {
                                                    $trial_version_html = '<span class="badge badge-danger">'.__('globals.subscription.expired').'</span>';
                                                } else if ($admin->trial_end >= date('Y-m-d') && $admin->is_subscribed != 1)
                                                {
                                                    $remain = (strtotime($admin->trial_end) - strtotime(date('Y-m-d')))/60/60/24;
                                                    $trial_version_html = '<span class="badge badge-primary">'.intval($remain).' '.__('globals.common.days').'</span>';
                                                } else {
                                                    $trial_version_html = '<span class="badge badge-warning">'.__('globals.common.unkonw').'</span>';
                                                }
                                            @endphp
                                            {!! $trial_version_html !!}
                                        </td>
                                        <td>
                                            @php
                                                $subStatusHtml = '';
                                                if($admin->is_subscribed == 1)
                                                {
                                                    $subID = $admin->subscribe_id;
                                                    if($subID > 4) $subID -= 4;
                                                    $membership = config('subscription.subscription_type')[$subID] ?? '';
                                                    $catMembership = $admin->subscribe_id > 5 ? __('globals.subscription.monthly') : __('globals.subscription.anual');
                                                    $subStatusHtml = '<span class="badge badge-success">'.$membership.' '.$catMembership.'</span>';
                                                } else
                                                {
                                                    $subStatusHtml = '<span class="badge badge-danger">'.__('globals.msg.not_purcharse').'</span>';
                                                }
                                            @endphp
                                            {!! $subStatusHtml !!}
                                        </td>
                                        <td>{{ $admin->account_name }}</td>
                                        <td>{{ $admin->client_id }}</td>
                                        <td>{{ $admin->client_secret }}</td>
                                        <td>
                                            <a href="{{ route('admins.edit',$admin->id) }}" title="Edit">
                                                <button class="btn-primary"><i class="mdi mdi-lead-pencil"></i></button>
                                            </a>
                                            @if($admin->is_super != 1)
                                                <button class="btn-danger btn-delete-record" title="Delete" data-id="{{ $admin->id }}"><i class="mdi mdi-delete"></i></button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> <!-- end col -->
            </div> <!-- end row -->
        </div><!-- container -->
    </div>
@endsection
@push('css')
    <link href="{{ asset('assets/admin/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        #datatable_user_data
        {

        }
    </style>
@endpush
@push('scripts')
    <script src="{{ asset('assets/admin/plugins/morris/morris.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <!-- Buttons examples -->
    <script src="{{ asset('assets/admin/plugins/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/buttons.colVis.min.js') }}"></script>
    <!-- Responsive examples -->
    <script src="{{ asset('assets/admin/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
    <!-- Datatable init js -->
    <script src="{{ asset('assets/admin/pages/datatables.init.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
    <!-- Myscript -->
    <script src="{{ asset('assets/admin/js/main.js') }}"></script>

    <script>
        var table;
        $(document).ready(function(){
                table = $('#datatable_user_data').DataTable(
                {
                    "stateSave": true,
                    "autoWidth": true,
                    "scrollY": '60vh',
                    "scrollX": true,
                    "scrollCollapse": true,
                    "bProcessing": true,
                    "order":[ 0, 'desc' ],
                    "language": {
                        buttons: {
                            pageLength: {
                                _: "{{ __('globals.datatables.show') }} %d {{ __('globals.datatables.rows') }}",
                            }
                        },
                        paginate: {
                            previous: "<i class='mdi mdi-chevron-left'>",
                            next: "<i class='mdi mdi-chevron-right'>"
                        },
                        info: "{{ __('globals.datatables.showing') }} _START_ {{ __('globals.datatables.to') }} _END_ {{ __('globals.datatables.of') }} _TOTAL_ {{ __('globals.datatables.entries') }}",
                        search: "{{ __('globals.datatables.search') }}:",
                        lengthMenu: "{{ __('globals.datatables.show') }} _MENU_ {{ __('globals.datatables.entries') }}",
                        zeroRecords: "{{ __('globals.datatables.zero_records') }}",
                    },

                    "responsive": false,
                    "lengthMenu": [
                        [ 10, 50, 100, 500, 1000],
                        [ '10', '50', '100', '500', '1000' ]
                    ],
                    "initComplete": function(settings, json) {

                    }
                });


            var SweetAlert = function () {
            };

            SweetAlert.prototype.init = function () {
                //Warning Message
                $('.btn-delete-record').click(function () {
                    var $this = $(this).parents('tr');
                    var id_del = $(this).data('id');
                    var _method = 'delete';
                    swal({
                        title: "{{ __('globals.msg.are_you_sure') }}",
                        text: "{{ __('globals.msg.you_dont_revert') }}",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonClass: 'btn btn-success',
                        cancelButtonClass: 'btn btn-danger m-l-10',
                        confirmButtonText: 'Yes, delete it!'
                    }).then(function () {
                        $.ajax({
                            url: "/admin/admins/" + id_del,
                            type: 'POST',
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            data: {
                                _method: 'delete'
                            },
                            success: function (data) {
                                if (data['status']==true) {
                                    $this.remove();
                                    table.row($this).remove().draw(false);
                                    swal(
                                        'Deleted!',
                                        'Your user have been deleted.',
                                        'success'
                                    );
                                } else {
                                    alert('Whoops Something went wrong!!');
                                }
                            },
                            error: function (data) {
                                alert(data.responseText);
                            }
                        });
                    })
                });
            },

                //init
                $.SweetAlert = new SweetAlert, $.SweetAlert.Constructor = SweetAlert;
            $.SweetAlert.init();
        });
    </script>
@endpush