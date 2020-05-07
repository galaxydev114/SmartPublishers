@extends('admin.layout')
@section('content')
@include('admin.partials.top-bar')
<div class="page-content-wrapper ">
    <div class="container">
        <div class="row">
            <div class="col-12">
                {{ __('globals.common.currency') }}:
                <select id="selcurrency" class="minimal m-b-10 col-md-1 list-inline">
                    @foreach ($currencies as $val)
                        @if($val == $curcurrency)
                            <option value="{{ $val }}" selected>{{ $val }}</option>
                        @else
                            <option value="{{ $val }}">{{ $val }}</option>
                        @endif
                    @endforeach
                </select>
                @if(Auth::guard('admin')->user()->is_super == true || (sizeof(session('permissions')) > 0 && array_key_exists('currency_setting', session()->get('permissions')->toArray()) && session('permissions')['currency_setting'] == 1))
                    <button id="currency_setting" data-toggle="popover" onclick="showCurrencySetting(this)" class="btn btn-secondary waves-effect waves-light btn-sm list-inline"><i class="mdi mdi-settings"></i></button>
                @endif
                <button id="site_report" data-toggle="popover" onclick="showSiteReport(this)" class="btn btn-secondary waves-effect waves-light btn-sm list-inline ml-2"><i class="mdi mdi-buffer"></i> {{ __('globals.sheet.site_report') }}</button>
                <div class="m-b-10 list-inline float-right" id="reportrange">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span></span> <i class="fa fa-caret-down"></i>
                </div>
            </div>

            <div class="col-12">
                <div class="card m-b-20">
                    <div class="card-block">
                        <h4 class="mt-0 header-title list-inline">
                            <label id="sheet_title" data-id="-1">{{ __('globals.common.site_ranking') }}
                            </label>
                            <a class="mt-0 list-inline float-right" id="btn_back" href="{{ route('site_ranking.index') }}"><i class="mdi mdi-arrow-left"></i> {{ __('globals.common.back') }}</a>
                        </h4>

                        <table id="datatable_sheet_data" class="table table-bordered table-hover">
                            <thead>
                                <tr id="datatable_sheet_data_header">
                                    <th id="th_status">#</th>
                                    <th>{{ __('globals.sheet.site_id') }}</th>
                                    <th>{{ __('globals.sheet.site_name') }}</th>
                                    <th id="th_spent">{{ __('globals.sheet.spent') }}({{ $curcurrency }})</th>
                                    <th id="th_received">{{ __('globals.sheet.has_received') }}({{ $curcurrency }})</th>
                                    <th id="th_received_max">{{ __('globals.sheet.received_max') }}({{ $curcurrency }})</th>
                                    <th>{{ __('globals.sheet.roi_min') }}(%)</th>
                                    <th>{{ __('globals.sheet.roi_max') }}(%)</th>
                                    <th id="th_profit_min">{{ __('globals.sheet.profit_min') }}({{ $curcurrency }})</th>
                                    <th id="th_profit_max">{{ __('globals.sheet.profit_max') }}({{ $curcurrency }})</th>
                                    <th>{{ __('globals.sheet.clicks') }}</th>
                                    <th id='th_bounce_rate'>{{ __('globals.ads.conversion_rate') }}</th>
                                    <th id='th_page_session'>{{ __('globals.sheet.engagement') }}</th>
                                    <th id='th_avg_duration'>{{ __('globals.sheet.time') }}</th>
                                    <th id='th_ctr'>{{ __('globals.sheet.ctr') }}</th>
                                    <th id='th_ecpm'>{{ __('globals.sheet.ecpm') }}</th>
                                </tr>
                            </thead>
                            <tbody id="datatable_body">
                            </tbody>
                            @if(Auth::guard('admin')->user()->is_super == true || (sizeof(session('permissions')) > 0 && array_key_exists('show_total_row', session()->get('permissions')->toArray()) && session('permissions')['show_total_row'] == 1))
                            <tfoot id="datatable_foot">
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="modal_title" aria-hidden="true" id="site_report_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0" id="modal_title">{{ __('globals.sheet.site_summery_data') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <table id="datatable_site_data" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('globals.sheet.site_id') }}</th>
                        <th>{{ __('globals.sheet.site_name') }}</th>
                        <th>{{ __('globals.ads.viewable_impressions') }}</th>
                        <th>{{ __('globals.ads.vctr') }}</th>
                        <th>{{ __('globals.ads.clicks') }}</th>
                        <th id="th_actual_cpc">{{ __('globals.ads.actual_cpc') }}({{ $curcurrency }})</th>
                        <th id="th_vcpm">{{ __('globals.ads.vcpm') }}({{ $curcurrency }})</th>
                        <th>{{ __('globals.ads.conversion_rate') }}</th>
                        <th>{{ __('globals.ads.conversions') }}</th>
                        <th id="th_cpa">{{ __('globals.ads.cpa') }}({{ $curcurrency }})</th>
                        <th id="th_spent">{{ __('globals.ads.spent') }}({{ $curcurrency }})</th>
                        <th>{{ __('globals.sheet.block_level') }}</th>
                    </tr>
                    </thead>
                    <tbody id="modal_site_tbody">
                    </tbody>
                    <tfoot id="modal_site_tfoot">
                    </tfoot>
                </table>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@endsection

@push('css')
    <link href="{{ asset('assets/admin/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Switchery css -->
    <link href="{{ asset('assets/admin/plugins/switchery/switchery.min.css') }}" rel="stylesheet" type="text/css">
    <!-- Datarangepicker css -->
    <link href="{{ asset('assets/admin/plugins/datarangepicker/daterangepicker.css') }}" rel="stylesheet" type="text/css"/>
    <!-- Filter multi select css -->
    <link href="{{ asset('assets/admin/plugins/filter-multi-select/filter_multi_select.css') }}" rel="stylesheet" type="text/css"/>

    <style>

        .modal-lg
        {
            max-width: 80% !important;
        }

        #cmp_site_id > div.dropdown-menu.show > div.filter.dropdown-item {
            padding-left: 14px;
            padding-right: 14px;
        }

        #sheet_title > span.switchery
        {
            margin-left: 5px;
        }

        .select2-container {
            border-radius: 2px;
            width: 400px !important;
            max-width: 400px !important;
            height: 38px;
            z-index: 1;
        }
        .select2-selection {
            background-color: #fff;
            border: 1px solid #aaa;
            border-radius: 4px;
            height: 38px !important;
        }

        .daterangepicker.opensright:before {
            right:34px;
            left: unset;
        }
        .daterangepicker.opensright:after
        {
            right:35px;
            left: unset;
        }

        .select2-selection__rendered,
        .select2-selection__arrow {
            margin-top: 4px;
        }

        #selcurrency {
            border:none;
            background-color: #fafafa;
            color: #292b2c;
        }
        #datatable_foot
        {
            font-weight: 700;
        }


        .dt-button-collection
        {
            z-index: 99999 !important;
        }

        #cmp_site_id {
            width: 30%;
            display: inline-flex;
        }

        .viewbar {
            padding-top: 3px;
            padding-bottom: 3px;
        }

        #chart_view
        {
            margin-left: -5px !important;
            height: 35px;
            margin-top: -3px !important;
            border-radius: 0px 3px 3px 0px;
        }

        @media only screen and (max-width: 1045px) {
            .dt-buttons.btn-group
            {
                display: none;
            }
            .select2-container
            {
                display: none;
            }
        }

        @media only screen and (max-width: 550px) {
            .modal-lg
            {
                max-width: 100% !important;
            }
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

    <!-- Date Range Picker Js -->
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/datarangepicker/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/datarangepicker/daterangepicker.min.js') }}"></script>

    <!-- Toastr Alert Js -->
    <script src="{{ asset('assets/admin/plugins/toastr/toastr.min.js') }}"></script>
    <!-- Swtichery Library js -->
    <script src="{{ asset('assets/admin/plugins/switchery/switchery.min.js') }}"></script>

    <!-- Select2 Library Js -->
    <script src="{{ asset('assets/admin/plugins/select2/select2.min.js') }}"></script>

    <!-- Sweetalert2 Library Js -->
    <script src="{{ asset('assets/admin/plugins/sweet-alert2/sweetalert2.min.js') }}"></script>

    <!-- Filter multi select JS -->
    <script src="{{ asset('assets/admin/plugins/filter-multi-select/filter-multi-select-bundle.min.js') }}"></script>
    <!--  Apex chart JS Library -->
    <script src="{{ asset('assets/admin/plugins/apexcharts/apexcharts.min.js') }}"></script>

    <script>
        let start_date, end_date, currency;
        toastr.options.progressBar = true;
        toastr.options.closeButton = true;
        toastr.options.closeDuration = 300;
        toastr.options.timeOut = 1000; // How long the toast will display without user interaction
        let cmp_ids;

        $(function() {

            var start = new Date("{{ $rep_start_date }}".replace( /(\d{4})-(\d{2})-(\d{2})/, "$1/$2/$3"));
            start = moment(start);
            var end = new Date("{{ $rep_end_date }}".replace( /(\d{4})-(\d{2})-(\d{2})/, "$1/$2/$3"));
            end = moment(end);

            function cb(cstart, cend) {
               $('#reportrange span').html(cstart.format('MMMM D, YYYY') + '~' + cend.format('MMMM D, YYYY'));

               $('#selcampaigns').hide();

                // Grab the datatables input box and alter how it is bound to events
                start_date = cstart.format('YYYY-MM-DD');
                end_date = cend.format('YYYY-MM-DD');

                siteView();

                start = cstart;
                end = cend;
            }

            $('#reportrange').daterangepicker({
                startDate: start,
                endDate: end,
                showDropdowns: false,
                linkedCalendars: true,
                maxDate: moment().format('MM/DD/YYYY'),
                minDate: moment().subtract(2, 'years').format('MM/DD/YYYY'),
                ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb);


            $('#selcurrency').on('change', function(evt)
            {

                $('#th_spent').text(`{{ __('globals.sheet.spent') }}(${$('#selcurrency').val()})`);
                $('#th_received').text(`{{ __('globals.sheet.has_received') }}(${$('#selcurrency').val()})`);
                $('#th_received_max').text(`{{ __('globals.sheet.received_max') }}(${$('#selcurrency').val()})`);
                $('#th_profit_min').text(`{{ __('globals.sheet.profit_min') }}(${$('#selcurrency').val()})`);
                $('#th_profit_max').text(`{{ __('globals.sheet.profit_max') }}(${$('#selcurrency').val()})`);

                cb(start, end);
            });

            cb(start, end);
        });

        function siteView()
        {
            hidePopover();
            blockUI();
            currency = $('#selcurrency').val();

            $.ajax({
                    url: "{{ route('site_ranking.datatable') }}",
                    type : "POST",
                    data : {
                        start_date:start_date,
                        end_date:end_date,
                        currency:currency,
                    },
                    success : function(res) {
                        if (res.status)
                        {
                            $.unblockUI();
                            $('#datatable_sheet_data').DataTable().destroy();
                            $('#datatable_body').html(res.return_html);
                            if(res.foot != '')
                                @if(Auth::guard('admin')->user()->is_super == true || (sizeof(session('permissions')) > 0 && array_key_exists('show_total_row', session()->get('permissions')->toArray()) && session('permissions')['show_total_row'] == 1))
                                    $('#datatable_foot').html(res.foot);
                                @endif

                            dtable = $('#datatable_sheet_data').DataTable({
                                "stateSave": true,
                                "autoWidth": false,
                                "scrollY": '60vh',
                                "scrollX": true,
                                "scrollCollapse": true,
                                "dom": 'Bfrtip',
                                "order":[ 3, 'desc' ],
                                "columnDefs": [
                                    { "width": '2%', "orderable": false, "targets": 0 },
                                ],
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
                            });


                            // res.result.map((el) => {
                            //     dtable.row.add( [
                            //         el.f_gStatus,
                            //         el.f_id,
                            //         el.f_spent,
                            //         el.f_gSpent,
                            //         el.f_rMax,
                            //         el.f_roiMin,
                            //         el.f_roiMax,
                            //         el.f_lMin,
                            //         el.f_lMax,
                            //         el.f_clicks,
                            //         el.f_gBounceRate,
                            //         el.f_gPageSession,
                            //         el.f_gAvgSessionDuration,
                            //         el.f_gCTR,
                            //         el.f_gECPM,
                            //     ]);
                            // });

                            //dtable.column( 2 ).visible( false, false ); //Has Rec
                            dtable.column( 4 ).visible( false, false ); //Roi Min
                            dtable.column( 7 ).visible( false, false ); //Profit Min
                            //dtable.column( 9 ).visible( false, false ); //Taxa
                            //dtable.column( 10 ).visible( false, false ); //Engajamento
                            //dtable.column( 11 ).visible( false, false ); //Tempo
                            //dtable.order( [ 3, 'desc' ] ).draw();
                            dtable.columns.adjust().draw( false );
                        }

                    },
                    error: function (request, status, error) {
                        toastr.error("Data loading error!", "Error");
                        $.unblockUI();
                    }
                });
        }

        function radioCurrencyStatus(obj)
        {
            let curVal = $(obj).val();
            if(curVal == 'auto')
            {
                $('#currency_min').attr('disabled', true);
                $('#currency_max').attr('disabled', true);
            }
            else if(curVal == 'manual')
            {
                $('#currency_min').attr('disabled', false);
                $('#currency_max').attr('disabled', false);
            }
        }

        function siteActivate(obj)
        {
            let status = $(obj).attr('status');
            let siteid = $(obj).attr('data-id');
            let value = '';  //1:play, 0:pause
            if(status == "play")
                value = 'true';
            else if(status == "pause")
                value = 'false';

            blockUI();

            $.post("{{ route('sheet.sitechangeaccountlevel') }}", {site: siteid, value: value},
                function (resp,textStatus, jqXHR) {
                    $.unblockUI();
                    if(resp.status === 200){
                        if(value == 'false')
                        {
                            $(obj).attr('class', 'btn btn-success waves-effect waves-light btn-sm');
                            $(obj).attr('status', 'play');
                            $(obj).html('<i class="mdi mdi-play"></i>');
                        }
                        else if(value == 'true')
                        {
                            $(obj).attr('class', 'btn btn-danger waves-effect waves-light btn-sm');
                            $(obj).attr('status', 'pause');
                            $(obj).html('<i class="mdi mdi-pause"></i>');
                        }
                        toastr.success("{{ __('globals.msg.operation_success') }}", "{{ __('globals.msg.well_done') }}");
                    } else
                    {
                        toastr.warning("{{ __('globals.msg.operation_fail') }}", "{{ __('globals.msg.oh_snap') }}");
                    }
                }
            ).fail(function(res) {
                $.unblockUI();
                toastr.warning("{{ __('globals.msg.operation_fail') }}", "{{ __('globals.msg.oh_snap') }}");
            });
        }

        function showSiteReport(obj) {
            hidePopover();
            $('#modal_site_tbody').hide();
            $('#modal_site_tbody').empty();
            $('#modal_site_tfoot').empty();
            $('#site_report_modal').modal({backdrop:'static', keyboard:false, show:true});
            blockUI();
            $.post("{{ route('sheet.summery_report') }}", { currency: $('#selcurrency').val() },
                function (resp,textStatus, jqXHR) {
                    $.unblockUI();
                    if(resp.status == 200)
                    {
                        $('#datatable_site_data').DataTable().destroy();
                        $('#modal_site_tbody').html(resp.content_html);
                        $('#modal_site_tfoot').html(resp.total_html);
                        $('[data-plugin="switchery"]').on('change', function(evt)
                        {
                            let status = this.checked;
                            let site = $(this).attr('data-id');
                            blockUI();
                            $.post("{{ route('sheet.sitechangeaccountlevel') }}", {site: site, value: status},
                                function (resp,textStatus, jqXHR) {
                                    $.unblockUI();
                                    if(resp.status === 200)
                                        toastr.success("{{ __('globals.msg.operation_success') }}", "{{ __('globals.msg.well_done') }}");
                                    else
                                        toastr.warning("{{ __('globals.msg.operation_fail') }}", "{{ __('globals.msg.oh_snap') }}");
                                }
                            ).fail(function(res) {
                                $.unblockUI();
                                toastr.warning("{{ __('globals.msg.operation_fail') }}", "{{ __('globals.msg.oh_snap') }}");
                            });
                        });

                        let m_table = $('#datatable_site_data').DataTable({
                            "processing": true,
                            "scrollY": '40vh',
                            "scrollX": true,
                            "lengthMenu": [
                                [ 10, 50, 100, 500, 1000],
                                [ '10', '50', '100', '500', '1000' ]
                            ],
                            dom: 'Bfrtip',
                            "buttons": [
                                'pageLength',
                                'copy', 'csv', 'excel', 'pdf', 'print',
                            ],
                            "order": [[ 3, "desc" ]],
                            "initComplete": function(settings, json) {
                                $('#modal_site_tbody').show();
                                setTimeout(function() { m_table.search('').draw(); }, 50);
                            }
                        });



                    } else {
                        $('#site_report_modal').modal('toggle');
                        toastr.warning("{{ __('globals.msg.operation_fail') }}", "{{ __('globals.msg.oh_snap') }}");
                    }

                }
            ).fail(function(res) {
                $.unblockUI();
                toastr.warning("{{ __('globals.msg.operation_fail') }}", "{{ __('globals.msg.oh_snap') }}");
            });

        }

        function showCurrencySetting(obj)
        {
            blockUI();
            hidePopover();
            $.ajax({
                url: "{{ route('sheet.getcurrency') }}",
                type : "POST",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success : function(res) {
                    let type = parseInt(res.type);
                    let maxVal = res.maxval;
                    let minVal = res.minval;
                    var contentHtml = `
                        <div data-toggle='popover_div'>
                            <div for="" class="control-label popupcelleditor-label mb-2 header-title">Currecy Setting(USD:BRL) </div>
                            <label class="radio">
                                <input type="radio" value="auto" name="currency_rate_radio" checked="checked" disabled="disabled" onclick="radioCurrencyStatus(this)">
                                <span class="radio-label">Auto Currency</span>
                            </label>
                            <div style="margin-bottom:5px;">
                                <span style="vertical-align: top; margin-right:2px;">Min Value: {{ session('currency_BRL') }}</span>
                            </div>
                            <div style="margin-bottom:10px;">
                                <span style="vertical-align: top">Max Value: {{ session('currency_max_BRL') }}</span>
                            </div>
                            <label class="radio">
                                <input type="radio" value="manual" name="currency_rate_radio" onclick="radioCurrencyStatus(this)">
                                <span style="vertical-align: top">Manual Currency</span>
                            </label>
                            <div style="margin-bottom:5px;">
                                <span style="vertical-align: top; margin-right:2px;">Min Value: </span>
                                <input type="number" min="0" max="100" step="any" style="text-align:right;" value="${minVal}" disabled="disabled" id="currency_min" class="col-md-8">
                            </div>
                            <div style="margin-bottom:10px;">
                                <span style="vertical-align: top">Max Value: </span>
                                <input type="number" min="0" max="100" step="any" style="text-align:right;" value="${maxVal}" disabled="disabled" id="currency_max" class="col-md-8">
                            </div>
                        </div>
                        <div class="form-actions float-right mb-1">
                            <button name="save" class="btn btn-secondary" onclick="saveCurrencyData()">
                            OK <i data-type="icon-ok" class="mdi mdi-check"></i></button>
                            <button data-novalidate="" class="btn btn-secondary" onclick="hidePopover()">
                            Cancel</button>
                        </div>`;

                    if(type == 1)
                    {
                        contentHtml = `
                        <div data-toggle='popover_div'>
                            <div for="" class="control-label popupcelleditor-label mb-2 header-title">Currecy Setting(USD:BRL) </div>
                            <label class="radio">
                                <input type="radio" value="auto" disabled="disabled" name="currency_rate_radio" onclick="radioCurrencyStatus(this)">
                                <span class="radio-label">Auto Currency</span>
                            </label>
                            <div style="margin-bottom:5px;">
                                <span style="vertical-align: top; margin-right:2px;">Min Value: {{ session('currency_BRL') }}</span>
                            </div>
                            <div style="margin-bottom:10px;">
                                <span style="vertical-align: top">Max Value: {{ session('currency_max_BRL') }}</span>
                            </div>
                            <label class="radio">
                                <input type="radio" value="manual" name="currency_rate_radio" checked="checked"  onclick="radioCurrencyStatus(this)">
                                <span style="vertical-align: top">Manual Currency</span>
                            </label>
                            <div style="margin-bottom:5px;">
                                <span style="vertical-align: top; margin-right:2px;">Min Value: </span>
                                <input type="number" min="0" max="100" step="any" style="text-align:right;" value="${minVal}"  id="currency_min" class="col-md-8">
                            </div>
                            <div style="margin-bottom:10px;">
                                <span style="vertical-align: top">Max Value: </span>
                                <input type="number" min="0" max="100" step="any" style="text-align:right;" value="${maxVal}"  id="currency_max" class="col-md-8">
                            </div>
                        </div>
                        <div class="form-actions float-right mb-1">
                            <button name="save" class="btn btn-secondary" onclick="saveCurrencyData()">
                            OK <i data-type="icon-ok" class="mdi mdi-check"></i></button>
                            <button data-novalidate="" class="btn btn-secondary" onclick="hidePopover()">
                            Cancel</button>
                        </div>`;
                    }
                    $.unblockUI();
                    $(obj).popover({
                        animation: false,
                        html: true,
                        sanitize: false,
                        placement: 'bottom',
                        trigger: 'manual',
                        content: contentHtml,
                    });

                    $(obj).popover('show');
                },
            });
        }

        function saveCurrencyData()
        {
            let type = 0;   //type = auto
            if($('input[name="currency_rate_radio"]:checked').val() == "manual") //type = manual
                type = 1;
            let minVal = parseFloat($('#currency_min').val());
            let maxVal = parseFloat($('#currency_max').val());
            if(type == 1)
            {
                if(minVal < 0 || maxVal < 0 || isNaN(minVal) || isNaN(maxVal))
                {
                    toastr.warning(`Error while saving update: Currency values has not null.`, 'Warning!');
                    $('#currency_min').focus();
                    return false;
                }

                if(minVal > 100 || maxVal > 100)
                {
                    toastr.warning(`Error while saving update: Currency values have to less than 100.`, 'Warning!');
                    $('#currency_min').focus();
                    return false;
                }
            }

            hidePopover();
            blockUI();
            $.ajax({
                url: "{{ route('sheet.setcurrency') }}",
                type : "POST",
                data : {
                    type:type,
                    min_value:minVal,
                    max_value:maxVal,
                },
                success : function(res) {
                    $.unblockUI();
                    $('#selcurrency').trigger('change');
                    //toastr.success("The operation is success.", "Success!");
                }
            });
        }

        function hidePopover()
        {
            $('[data-toggle="popover"]').popover('dispose');
        }

    </script>
@endpush
