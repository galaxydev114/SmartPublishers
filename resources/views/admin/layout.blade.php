<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
        <title>Smart Publishers :: {{ $title }}</title>
        <meta content="Admin Dashboard" name="description" />
        <meta content="ThemeDesign" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @stack('css')
        <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">
        <link href="{{ asset('assets/admin/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('assets/admin/css/icons.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('assets/admin/css/style.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('assets/admin/css/main.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('assets/admin/css/jquery.readonly.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('assets/admin/css/jquery-confirm.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/admin/plugins/npprogress/nprogress.css') }}" rel="stylesheet" type="text/css" />


    </head>


    <body class="fixed-left">

        <!-- Loader -->
        <div id="preloader"><div id="status"><div class="spinner"></div></div></div>

        <!-- Begin page -->
        <div id="wrapper">

            <!-- ========== Left Sidebar Start ========== -->
            <div class="left side-menu">
                @include('admin.partials.side-menu')
                
            </div>
            <!-- Left Sidebar End -->

            <!-- Start right Content here -->

            <div class="content-page">
                <!-- Start content -->
                <div class="content" id="pjax-container">

                    @yield('content')

                </div> <!-- content -->

                <footer class="footer">
                    © Copyright 2021 Smart Publishers Todos os Direitos Resrvados
                </footer>

            </div>
            <!-- End Right content here -->

        </div>
        <!-- END wrapper -->


        <!-- jQuery  -->
        <script src="{{ asset('assets/admin/js/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/admin/js/tether.min.js') }}"></script><!-- Tether for Bootstrap -->
        <script src="{{ asset('assets/admin/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('assets/admin/js/modernizr.min.js') }}"></script>
        <script src="{{ asset('assets/admin/js/detect.js') }}"></script>
        <script src="{{ asset('assets/admin/js/fastclick.js') }}"></script>
        <script src="{{ asset('assets/admin/js/jquery.slimscroll.js') }}"></script>
        <script src="{{ asset('assets/admin/js/waves.js') }}"></script>
        <script src="{{ asset('assets/admin/js/jquery.nicescroll.js') }}"></script>
        <script src="{{ asset('assets/admin/js/jquery.scrollTo.min.js') }}"></script>
        <script src="{{ asset('assets/admin/js/jquery.readonly.min.js') }}"></script>
        <script src="{{ asset('assets/admin/js/jquery-confirm.min.js') }}"></script>
        <script src="{{ asset('assets/admin/plugins/npprogress/nprogress.min.js') }}"></script>
        <script src="{{ asset('assets/admin/js/blockUI.js') }}"></script>
        <!--Morris Chart-->


        <!-- App js -->
        <script src="{{ asset('assets/admin/js/app.js') }}"></script>
        
        {{--  <script src="https://js.pusher.com/5.0/pusher.min.js"></script>  --}}
        <script>
                       //Blockui plugin...this causes the blockui to run
            //whenever the content is within an <asp:UpdatePanel>
            //Balance

            $('#span_balance').popover();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                }
            });

            // $(document).on('click', function (e) {
            //     if ($('div.popover:visible').hasClass('show'))
            //     {
            //         if($(e.target).attr('class') != 'popover-title' && $(e.target).attr('class') != 'popover-content' && $(e.target).attr('id') != 'span_balance')
            //             $('#span_balance').not(this).popover('hide');
            //     }
            // })

            //Center the element
            $.fn.center = function () {
                this.css("position", "absolute");
                this.css("top", ($(window).height() - this.height()) / 2 + $(window).scrollTop() + "px");
                this.css("left", ($(window).width() - this.width()) / 2 + $(window).scrollLeft() + "px");
                return this;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function isUrlValid(userInput) {
                var res = userInput.match(/(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g);
                if(res == null)
                    return false;
                else
                    return true;
            }

            //Brazil Money type change
            function changeRealMoneyUnit(str)
            {
                return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL', minimumFractionDigits: 2 }).format(str);
            }

            //blockUI
            function blockUI() {
                $.blockUI({
                css: {
                    backgroundColor: 'transparent',
                    border: 'none'
                },
                message: '<div class="spinner"></div>',
                baseZ: 1500,
                overlayCSS: {
                    backgroundColor: '#FFFFFF',
                    opacity: 0.7,
                    cursor: 'wait'
                }
                });

                NProgress.start();
                
                $('.blockUI.blockMsg').center();
            }//end Blockui   

            var new_notification_count = 0;
            //init_new_notification();
            
            function init_new_notification(){
                $.ajax({
                    url: "/admin/notifications/newNotification",
                    type: 'GET',
                    success: function(res) {
                        if(res['status']==true){
                            var new_notifs = res['new_notifs'];
                            new_notification_count = new_notifs.length;
                            if(new_notification_count > 0){
                                var notif_item = $('.notif-link-temp').find('.noti-icon-badge').clone(true, true);
                                notif_item.html(new_notification_count);
                                $('.notif-link').append(notif_item);
                                new_notifs.forEach(function(item, index){
                                    if(index < 3){
                                        var notif_message = $('.noti-message-container-temp').find('a').clone(true, true);
                                        notif_message.attr('href', item['url']);
                                        notif_message.find('.notify-title').html(item['title']);
                                        notif_message.find('.text-muted').html(item['message']);
                                        notif_message.find('.notify-icon').find('i').remove();
                                        notif_message.find('.notify-icon').append('<i class="'+item['icon']+'"></i>');
                                        
                                        $('.noti-message-container').append(notif_message);
                                    }
                                });
                            }
                        }else{
                            alert('Oops! Something went wrong.');
                        }
                    }
                });
            }

            {{--  Pusher.logToConsole = true;

            var pusher = new Pusher('50ee1edbd97b049fe03f', {
            cluster: 'eu',
            forceTLS: true
            });

            var channel = pusher.subscribe('admin-channel');
            channel.bind('notification-event', function(data) {
                var old_notif_item = $('.notif-link').find('.noti-icon-badge');
                if(old_notif_item.length > 0){
                    old_notif_item.html(++new_notification_count);
                }else{
                    var notif_item = $('.notif-link-temp').find('.noti-icon-badge').clone(true, true);
                    notif_item.html(++new_notification_count);
                    $('.notif-link').append(notif_item);
                }
                var notif_message = $('.noti-message-container-temp').find('a').clone(true, true);
                notif_message.attr('href', data.url);
                notif_message.find('.notify-title').html(data.title);
                notif_message.find('.text-muted').html(data.message);
                notif_message.find('.notify-icon').find('i').remove();
                notif_message.find('.notify-icon').append('<i class="'+data.icon+'"></i>');
                
                $('.noti-message-container').prepend(notif_message);

                $('.noti-message-container').find('a').slice(3).remove();
            });  --}}

            $('.notif-link').click(function(){
                if(new_notification_count > 0){
                    new_notification_count = 0;
                    $(this).find('.noti-icon-badge').remove();

                    $.ajax({
                        url: "/admin/notifications/readall",
                        type: 'GET',
                        success: function(res) {
                            if(res['status']==true){

                            }else{
                                alert('Oops! Something went wrong.');
                            }
                        }
                    });
                }
            });

            let getRandomColor = () => {
                var letters = '0123456789ABCDEF';
                var color = '#';
                for (var i = 0; i < 6; i++) {
                    color += letters[Math.floor(Math.random() * 16)];
                }
                return color;
            }

        </script>

        @stack('scripts')

    </body>
</html>

