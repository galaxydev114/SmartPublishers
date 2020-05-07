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
                            <div class="row">
                                {!! Form::select('type_list', [ 0 => __('globals.common.select_all')] + $type_list, $sel_type, array('id'=>'type_list', 'class'=> 'custom-select minimal m-b-10 col-3')) !!}
                            </div>
                            <h4 class="mt-0 ml-3 header-title">
                                <button id="btn_add" class="btn btn-primary waves-effect waves-light" type="button">
                                    <i class="ion-plus"></i> {{ __('globals.common.add') }}
                                </button>
                            </h4>
                            <table id="datatable_subscription_list" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th width="8%">{{ __('globals.common.id') }}</th>
                                    <th width="12%">{{ __('globals.common.type') }}</th>
                                    <th class="text-center">{{ __('globals.subscription.content') }}</th>
                                    <th width="7%">{{ __('globals.subscription.order') }}</th>
                                    <th width="7%">{{ __('globals.common.action') }}</th>
                                </tr>
                                </thead>
                                <tbody id="datatable_tbody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> <!-- end col -->
            </div> <!-- end row -->
        </div><!-- container -->
    </div>
    <!-- Modal Form -->
    <div id="crud_modal" class="modal fade" role="dialog" aria-labelledby="curdModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" id="crud_form" name="crud_form">
                    <div class="modal-header">
                        <h5 class="modal-title mt-0" id="modal_title"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        {{ csrf_field() }}
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">{{ __('globals.subscription.title') }}:</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    {!! Form::select('type_list_modal', $type_list, old('type_list_modal'), array('id'=>'type_list_modal', 'class'=> 'custom-select minimal m-b-10 col-5')) !!}
                                </div>
                            </div>
                        </div>

                        <div class="form-group row
                            @if($errors->has('order'))
                                has-danger
                            @endif">
                            <label class="col-sm-3 col-form-label">{{ __('globals.subscription.order') }}: <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input name="order" class="form-control" type="number" value="{{ old('order') }}" id="order" required>
                                    <span class="input-group-addon bg-custom b-0"><i class="mdi mdi-call-received"></i></span>
                                </div>
                                <div class="form-control-error text-danger" id="error_order"></div>
                            </div>
                        </div>

                        <div class="form-group row
                            @if($errors->has('content'))
                                has-danger
                            @endif">
                            <label class="col-sm-3 col-form-label">{{ __('globals.subscription.content') }}: <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input name="content" class="form-control" type="text" value="{{ old('content') }}" id="content" required>
                                    <span class="input-group-addon bg-custom b-0"><i class="mdi mdi-lead-pencil"></i></span>
                                </div>
                                <div class="form-control-error text-danger" id="error_content"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('globals.common.close') }}</button>
                        <button type="submit" class="btn btn-primary" >{{ __('globals.common.save_changes')  }}</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

@endsection
@push('css')
    <link href="{{ asset('assets/admin/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        #datatable_deposit_data
        {

        }
        .select2-container[data-select2-id="3"] {
            width: 100% !important;
        }

        .select2-container[data-select2-id="1"] {
            border-radius: 2px;
            position: absolute;
            top: 103px;
            left: 468px;
            z-index: 1;
        }

        .select2-selection {
            background-color: #fff;
            border: 1px solid #aaa;
            border-radius: 4px;
            height: 38px !important;
        }

        table.dataTable.dtr-inline.collapsed>tbody>tr>td:first-child:before, table.dataTable.dtr-inline.collapsed>tbody>tr>th:first-child:before
        {
            top: 18px !important;
        }

        .select2-selection__rendered,
        .select2-selection__arrow {
            margin-top: 4px;
        }

        button
        {
            cursor: pointer;
        }

        @media only screen and (max-width: 1045px) {
            .dt-buttons.btn-group
            {
                display: none;
            }
            .select2-container[data-select2-id="1"]
            {
                display: none;
            }
        }
    </style>
@endpush
@push('scripts')
    <!-- Jquery validate Library -->
    <script src="{{ asset('assets/admin/js/jquery.validate.min.js') }}"></script>
    <!-- Datapicker -->
    <script src="{{ asset('assets/admin/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <!-- Datatable -->
    <script src="{{ asset('assets/admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <!-- Buttons Addin Datatable -->
    <script src="{{ asset('assets/admin/plugins/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/buttons.colVis.min.js') }}"></script>
    <!-- Responsive Datatable -->
    <script src="{{ asset('assets/admin/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
    <!-- Datatable init js -->
    <script src="{{ asset('assets/admin/pages/datatables.init.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
    <!-- Toastr Alert Js -->
    <script src="{{ asset('assets/admin/plugins/toastr/toastr.min.js') }}"></script>
    <!-- Select2 Library Js -->
    <script src="{{ asset('assets/admin/plugins/select2/select2.min.js') }}"></script>

    <script>
        let modalStatus = 'add';
        let editId = -1;
        let m_table;
        $(document).ready(function(){
            toastr.options.progressBar = true;
            toastr.options.closeButton = true;
            toastr.options.closeDuration = 300;
            toastr.options.timeOut = 1000; // How long the toast will display without user interaction

            m_table = $('#datatable_subscription_list').DataTable(
            {
                "dom": 'Bfrtip',
                "stateSave": true,
                "autoWidth": true,
                "scrollCollapse": true,
                "bProcessing": true,
                "order":[ 0, 'asc' ],
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
                "buttons": [
                    'pageLength',
                    {
                        "extend": 'collection',
                        "text": '{{ __('globals.datatables.export') }}',
                        "buttons": [ 'copy', 'csv', 'excel', 'pdf', 'print' ],
                        "fade": true
                    },
                        @if(sizeof(session('permissions')) > 0 && session('permissions')['column_visibility'] == 1)
                    {
                        "extend": 'colvis',
                        "text": '{{ __('globals.datatables.colvis') }}'
                    }
                    @endif
                ],
                'createdRow': function( row, data, dataIndex ) {
                    $(row).attr('id', 'someID');
                },
            });


            $('#btn_add').click((evt) => {
                modalStatus = 'add';
                editId = -1;
                $('#modal_title').text("{{ __('globals.common.add_data') }}");
                $('#type_list_modal').val(1);
                $('#type_list_modal').trigger('change');
                $('#crud_modal').modal({backdrop:'static', keyboard:false, show:true});
                $('#crud_form').trigger("reset");
            });

            $('#type_list').select2();
            $('#type_list_modal').select2();

            $('#type_list').change(function(evt){
                let curType = $(this).val();
                $.post("{{ route('subscription.ajax_setsessionsubtype') }}", { type: curType },
                    function (resp, textStatus, jqXHR) {
                        getLoadData();
                    });
            });

            $("#crud_form").validate({
                errorPlacement: function(error, element) {
                    //Custom position: first name
                    if (element.attr("name") == "order" ) {
                        $("#error_order").text('{{ __('globals.msg.field_require') }}');
                    }
                    //Custom position: second name
                    else if (element.attr("name") == "content" ) {
                        $("#error_content").text('{{ __('globals.msg.field_require') }}');
                    }
                },
                submitHandler: function (form) {
                    blockUI();
                    if(modalStatus == 'add')
                    {
                        $.post("{{ route('subscription_list.save_data') }}", { type: $('#type_list_modal').val(), order: $('#order').val(), sub_content: $('#content').val() },
                            function (resp, textStatus, jqXHR) {
                                if(resp.status !== 200)
                                {
                                    toastr.error("{{ __('globals.msg.operation_fail') }}", "{{ __('globals.msg.oh_snap') }}");
                                } else {
                                    $('#crud_modal').modal('toggle');
                                    getLoadData();
                                }
                                $.unblockUI();
                            });
                    } else if(modalStatus == 'edit')
                    {
                        $.post("{{ route('subscription_list.edit_data') }}", { id: editId, type: $('#type_list_modal').val(), order: $('#order').val(), sub_content: $('#content').val() },
                            function (resp, textStatus, jqXHR) {
                                if(resp.status !== 200)
                                {
                                    toastr.error("{{ __('globals.msg.operation_fail') }}", "{{ __('globals.msg.oh_snap') }}");
                                } else {
                                    $('#crud_modal').modal('toggle');
                                    getLoadData();
                                }
                                $.unblockUI();
                            });
                    }
                }
            });

            getLoadData();

            var SweetAlert = function () {
            };
            //init
            $.SweetAlert = new SweetAlert, $.SweetAlert.Constructor = SweetAlert;

        });

        let editRow = (obj, id) => {
            modalStatus = 'edit';
            editId = id;
            var $this = $(obj).parents('tr');
            $('#modal_title').text("{{ __('globals.common.edit_data') }}");
            $('#error_order').text('');
            $('#error_content').text('');
            $('#content').val($this.children('td').eq(2).text());
            $('#order').val($this.children('td').eq(3).text());
            $('#type_list_modal').val($($this.children('td').eq(1).children('span').eq(0)).attr('type-id'));
            $('#type_list_modal').trigger('change');

            $('#crud_modal').modal({backdrop:'static', keyboard:false, show:true});
            oldDate = $('#type_list_modal').val();
        }

        let deleteRow = (obj, id) => {
            var $this = $(obj).parents('tr');
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
                    url: "{{ url('admin/subscription_list/remove') }}" + '/'  + id,
                    type: 'POST',
                    success: function (data) {
                        if (data.status === 200) {
                            $this.remove();
                            m_table.row($this).remove().draw(false);
                            toastr.success('{{ __('globals.msg.remove_success') }}', '{{ __('globals.msg.well_done') }}');
                        } else {
                            toastr.error("{{ __('globals.msg.operation_fail') }}", "{{ __('globals.msg.oh_snap') }}");
                        }
                    },
                    error: function (data) {
                        toastr.error("{{ __('globals.msg.operation_fail') }}", "{{ __('globals.msg.oh_snap') }}");
                    }
                });
            })
        }

        let getLoadData = () => {
            blockUI();
            m_table.clear().draw();
            //$('.select2-container[data-select2-id="1"]').hide();
            $.post("{{ route('subscription_list.get_all') }}",
                function (resp, textStatus, jqXHR) {
                    $.unblockUI();
                    let insertBody = "";
                    let total_amount = 0;

                    resp.results.map((ele, index) => {
                        m_table.row.add([
                            `${ele.id}`,
                            `<span type-id="${ele.type}">${@json(config('subscription')['subscription_type'])[ele.type]}</span>`,
                            ele.sub_content,
                            ele.sort,
                            `<button class="btn-primary" title="Edit" data-type-id="${ele.type}" onclick="editRow(this, ${ele.id})"><i class="mdi mdi-lead-pencil"></i></button>
                            <button class="btn-danger btn-delete-record" title="Delete" data-id="${ele.id}" onclick="deleteRow(this, ${ele.id})"><i class="mdi mdi-delete"></i></button>`
                        ]).draw( false );
                    })
                }
            ).fail(function(res){
                $.unblockUI();
                toastr.error("{{ __('globals.msg.operation_fail') }}", "{{ __('globals.msg.oh_snap') }}");
            });
        }
    </script>
@endpush