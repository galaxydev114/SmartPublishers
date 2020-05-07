@extends('admin.layout')

@section('content')

@include('admin.partials.top-bar')

@php
    $isSubscription = Auth::guard('admin')->user()->is_subscribed;
    $subscriptionID = Auth::guard('admin')->user()->subscribe_id;
@endphp

<div class="page-content-wrapper">
    <div class="container">
        <div class="btm-tbl mt-2">
            <div class="card m-b-20">
                <div class="card-block">
                    <h4 class="ml-3 mt-0 header-title list-inline">
                        <a class="mt-0 list-inline float-right" id="btn_back" href="javascript:history.back(1);"><i class="mdi mdi-arrow-left"></i> {{ __('globals.common.back') }}</a>
                    </h4>
                    <div class="mt-3 p-3 box effect5">
                        <div class="container">
                            @if(isset($subscription_model))
                                <div class="top">
                                    <h2 class="text-center">{!! __('globals.subscription.plans_pricing') !!}</h2>
                                    <h5>{{ __('globals.subscription.discount_plan') }}</h5>
                                    @if($trial_status != '')
                                        <div class="justify-content-between col-lg-7">
                                            <div class="alert alert-{{ $badge_status }} alert-dismissible fade show" role="alert">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                                <strong>{{ __('globals.msg.alert') }}</strong> {{ $trial_status }}
                                            </div>
                                        </div>
                                    @endif
                                    @if(session()->has('success'))
                                        <div class="justify-content-between col-lg-7">
                                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                                <strong>{{ __('globals.msg.well_done') }}</strong> {{ session('success') }}
                                            </div>
                                        </div>
                                    @endif
                                    @if(session()->has('error'))
                                        <div class="justify-content-between col-lg-7">
                                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                                <strong>{{ __('globals.msg.well_done') }}</strong> {{ session('success') }}
                                            </div>
                                        </div>
                                    @endif

                                    <div class="toggle-btn">
                                        <span style="margin: 0.8em;">{{ __('globals.subscription.monthly') }}</span>
                                        <label class="switch">
                                            <input type="checkbox" id="checkType" onclick="checkMonthlyAnual()"/>
                                            <span class="slider round"></span>
                                        </label>
                                        <span style="margin: 0.8em;">{{ __('globals.subscription.anual') }}</span>
                                    </div>
                                </div>
                            @endif
                            <div class="package-container">
                                <div class="packages @if($isSubscription === 1 && ($subscriptionID === 1 || $subscriptionID === 5)) border-top-blue @endif">
                                    <h2 class="mt-3">{{ config('subscription')['subscription_type'][1] }}</h2>
                                    <h3 id="m_type_1" class="text1 pb-5" price-value="99"><span class="sup-currency">R$</span>99,00</h3>
                                    <h3 id="y_type_1" class="text2" price-value="1188"><span class="sup-currency">R$</span>1188,00</h3>
                                    <ul class="sub_items list">
                                        @foreach(explode('$%!', $sub_list[1]) as $key => $item)
                                            <li @if($key == 0) class="first" @endif>{{ $item }}</li>
                                        @endforeach
                                    </ul>
                                    @if($isSubscription === 1 && ($subscriptionID === 1 || $subscriptionID === 5))
                                        <a class="button button2 btn-selected" data-type="1">{{ __('globals.common.cancel') }}</a>
                                    @else
                                        <a class="button button2 @if($isSubscription === 1) disabled @endif" data-type="1">{{ __('globals.common.contract') }}</a>
                                    @endif
                                </div>
{{--                                <div class="packages @if($isSubscription === 1 && ($subscriptionID === 2 || $subscriptionID === 6)) border-top-blue @endif">--}}
{{--                                    <h2 class="mt-3">{{ config('subscription')['subscription_type'][2] }}</h2>--}}
{{--                                    <h3 id="m_type_2" class="text1" price-value="220"><span class="sup-currency">R$</span>220,00</h3>--}}
{{--                                    <h3 id="y_type_2" class="text2" price-value="2640"><span class="sup-currency">R$</span>2640,00</h3>--}}
{{--                                    <div class="ribbon">{{ __('globals.subscription.users_prepered') }}</div>--}}
{{--                                    <ul class="list">--}}
{{--                                        @foreach(explode('$%!', $sub_list[2]) as $key => $item)--}}
{{--                                            <li @if($key == 0) class="first" @endif>{{ $item }}</li>--}}
{{--                                        @endforeach--}}
{{--                                    </ul>--}}
{{--                                    @if($isSubscription === 1 && ($subscriptionID === 2  || $subscriptionID === 6))--}}
{{--                                        <a class="button button2 btn-selected" data-type="2">{{ __('globals.common.cancel') }}</a>--}}
{{--                                    @else--}}
{{--                                        <a class="button button2 @if($isSubscription === 1) disabled @endif" data-type="2">{{ __('globals.common.contract') }}</a>--}}
{{--                                    @endif--}}
{{--                                </div>--}}
{{--                                <div class="packages @if($isSubscription === 1 && ($subscriptionID === 3 || $subscriptionID === 7)) border-top-blue @endif">--}}
{{--                                    <h2 class="mt-3">{{ config('subscription')['subscription_type'][3] }}</h2>--}}
{{--                                    <span class="strike type3_strike"><span class="font-14">R$</span>5065,20</span>--}}
{{--                                    <h3 id="m_type_3" class="text1 pt-0" price-value="422.1"><span class="sup-currency">R$</span>422,10</h3>--}}
{{--                                    <h3 id="y_type_3" class="text2 pt-0" price-value="4052.1"><span class="sup-currency">R$</span>4052,10</h3>--}}

{{--                                    <div class="ribbon bg-danger cost-off">{{ __('globals.subscription.20pro_off') }}</div>--}}
{{--                                    <ul class="list">--}}
{{--                                        @foreach(explode('$%!', $sub_list[3]) as $key => $item)--}}
{{--                                            <li @if($key == 0) class="first" @endif>{{ $item }}</li>--}}
{{--                                        @endforeach--}}
{{--                                    </ul>--}}
{{--                                    @if($isSubscription === 1 && ($subscriptionID === 3 || $subscriptionID === 7))--}}
{{--                                        <a class="button button3 btn-selected" data-type="3">{{ __('globals.common.cancel') }}</a>--}}
{{--                                    @else--}}
{{--                                        <a class="button button3 @if($isSubscription === 1) disabled @endif" data-type="3">{{ __('globals.common.contract') }}</a>--}}
{{--                                    @endif--}}
{{--                                </div>--}}
{{--                                <div class="packages @if($isSubscription === 1 && ($subscriptionID === 4 || $subscriptionID === 8)) border-top-blue @endif">--}}
{{--                                    <h2 class="mt-3">{{ config('subscription')['subscription_type'][4] }}</h2>--}}
{{--                                    <div class="ribbon bg-info cost-off">{{ __('globals.subscription.20pro_off') }}</div>--}}
{{--                                    <span class="strike type4_strike"><span class="font-14">R$</span>8342,40</span>--}}
{{--                                    <h3 id="m_type_4" class="text1" price-value="695.2"><span class="sup-currency">R$</span>695,20</h3>--}}
{{--                                    <h3 id="y_type_4" class="text2" price-value="6645.1"><span class="sup-currency">R$</span>6645,10</h3>--}}
{{--                                    <ul class="list">--}}
{{--                                        @foreach(explode('$%!', $sub_list[4]) as $key => $item)--}}
{{--                                            <li @if($key == 0) class="first" @endif>{{ $item }}</li>--}}
{{--                                        @endforeach--}}
{{--                                    </ul>--}}
{{--                                    @if($isSubscription === 1 && ($subscriptionID === 4 || $subscriptionID === 8))--}}
{{--                                        <a class="button button4 btn-selected" data-type="4">{{ __('globals.common.cancel') }}</a>--}}
{{--                                    @else--}}
{{--                                        <a class="button button4 @if($isSubscription === 1) disabled @endif" data-type="4">{{ __('globals.common.contract') }}</a>--}}
{{--                                    @endif--}}
{{--                                </div>--}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- payment selection modal -->
<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myCenterModalLabel" aria-hidden="true"  id="payment_method_modal" data-backdrop="static" payment-type="1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myCenterModalLabel">{{ __('globals.subscription.payment_method') }}</h4>
                <button type="button" class="modal-close-btn close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <button type="button" class="btn btn-block btn-lg btn-outline-info waves-effect waves-light" onclick="gotoStripe()">
                    <i class="mdi mdi-credit-card"></i> {{ __('globals.subscription.credit_card') }}</button>

                <form action="#" method="post" id="payment-form" class="card card-body">
                    @csrf
                    <div class="form-group">
                        <div class="card-body">
                            <div id="card-element">
                                <!-- A Stripe Element will be inserted here. -->
                            </div>
                            <!-- Used to display form errors. -->
                            <div id="card-errors" role="alert"></div>
                            <input type="hidden" name="plan" id="plan_id"/>
                        </div>
                    </div>
                    <button class="btn btn-dark width-xs waves-effect waves-light col-md-12" id='stripe_pay_btn' type="submit">{{ __('globals.subscription.pay') }}</button>
                </form>
                <div class="mt-1"></div>
                <button type="button" class="btn btn-block btn-lg btn-outline-info waves-effect waves-light" onclick="gotoPaypal()">
                    <i class="fa fa-paypal"></i> {{ __('globals.subscription.paypal') }}</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- end modal -->

@endsection

@push('css')
    <link href="{{ asset('assets/admin/css/main.css') }}" rel="stylesheet" type="text/css" />
    <!-- Toastr css -->
    <link href="{{ asset('assets/admin/plugins/toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        #y_type_1
        {
            display: none;
        }

        .sub_items
        {
            display: none;
        }

        .page-content-wrapper
        {
            position: sticky;
        }
        .page-title
        {
            line-height: 68px !important;
        }
        .btn.disabled, .btn:disabled {
            color: #5bc0de !important;
            background-color: #464a4cf0 !important;
        }

        span.sup-currency
        {
            font-size: 17px;
        }

        .border-top-blue
        {
            border-top: solid 7px #0e90ff;
        }

        #payment-form
        {
            display:none;
            background-color: #00000091;
        }

        .modal-content
        {
            background: #0f1211 !important;
        }
        h3 {
            line-height: 12px !important;
        }
        .strike {
            text-decoration: line-through;
        }
        .modal-header
        {
            height: 65px;
            border-bottom: 1px solid #777878 !important;
        }
        .modal-title
        {
            color: #aeaeae;
        }
        .modal-close-btn
        {
            color: white !important;
        }

        .card-body {
            -webkit-box-flex: 1 !important;
            -ms-flex: 1 1 auto !important;
            flex: 1 1 auto !important;
            padding: 1rem !important;
        }

        .ribbon
        {
            width: 160px;
            height: 32px;
            font-size: 12px;
            text-align: center;
            color: #fff;
            font-weight: bold;
            box-shadow: 0px 2px 3px rgb(136 136 136 / 25%);
            background: #4dbe3b;
            transform: rotate(
                    45deg
            );
            position: absolute;
            right: -35px;
            top: 28px;
            padding-top: 7px;
        }

        .packages {
            margin: 20px;
            width: 300px;
            overflow: hidden;
            position: relative;
            padding-bottom: 1.5em;
            height: 100%;
            background-color: #1e2321;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            border-radius: 20px;
            box-shadow: 0 19px 38px rgba(30, 35, 33, 1), 0 15px 12px rgba(30, 35, 33, 0.2);
            flex-wrap: wrap;
            color: #f4f4f4;
        }

        h1,
        h2 {
            font-size: 2.2em;
        }

        .list li {
            font-size: 15px;
            list-style: none;
            border-bottom: 1px solid #f4f4f4;
            padding-inline-start: 0;
            border-width: 1px;
            padding: 10px;
        }

        .first {
            margin-top: 40px;
            border-top: 1px solid #f4f4f4;
        }

        .list {
            width: 80%;
        }

        ol,
        ul {
            padding: 0;
        }

        .top {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        input,
        label {
            display: inline-block;
            vertical-align: middle;
            margin: 10px 0;
        }

        .button {
            padding: 10px 30px;
            text-decoration: none;
            font-size: 1.4em;
            margin: 15px 15px;
            border-radius: 50px;
            color: #f4f4f4;
            transition: all 0.3s ease 0s;
        }

        .disabled
        {
            background-color: #464a4c !important;
            box-shadow: unset !important;
            cursor: not-allowed;
        }

        .disabled:hover {
            transform: unset !important;
        }

        .btn-selected {
            background-color: #fd0700 !important;
            box-shadow: 0 0 10px 0 #fd0700 inset, 0 0 20px 2px #fd0700 !important;
        }

        .button
        {
            min-width: 170px !important;
        }

        .button:hover {
            transform: scale(1.1);
        }

        .button1 {
            background-color: #ffae42;
            box-shadow: 0 0 10px 0 #ffae42 inset, 0 0 20px 2px #ffae42;
        }

        .button2 {
            background-color: #43d821;
            box-shadow: 0 0 10px 0 #43d821 inset, 0 0 20px 2px #43d821;
        }

        .button3 {
            background-color: #00cc99;
            box-shadow: 0 0 10px 0 #00cc99 inset, 0 0 20px 2px #00cc99;
        }

        .button4 {
            background-color: #ff007c;
            box-shadow: 0 0 10px 0 #ff007c inset, 0 0 20px 2px #ff007c;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #1e2321;
            -webkit-transition: 0.4s;

            box-shadow: 2px 6px 25px #1e2321;
            transform: translate(0px, 0px);
            transition: 0.6s ease transform, 0.6s box-shadow;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: 0.4s;
            transition: 0.4s;
        }

        input:checked + .slider {
            background-color: #50bfe6;
        }

        input:focus + .slider {
            box-shadow: 0 0 1px #50bfe6;
        }

        input:checked + .slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }

        .package-container {
            display: flex;
            align-items: flex-start;
            justify-content: center;
            flex-wrap: wrap;
        }

        tspan
        {
            font-size: 12px;
            font-weight: bold;
        }
    </style>
@endpush

@push('scripts')
    <!-- Toastr Library js -->
    <script src="{{ asset('assets/admin/plugins/toastr/toastr.min.js') }}"></script>
    <!-- Date Range Picker Js -->
    <script src="{{ asset('assets/admin/plugins/datarangepicker/moment.min.js') }}"></script>
    <!-- Stripe 3d party Js -->
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        let m_curSelectPrice = 0;
        $(document).ready(function() {
            @if($isSubscription === 1 && $subscriptionID > 4)
                $('#checkType').click();
            @else
                //checkMonthlyAnual();
            @endif

            @if($isSubscription === 1)
                $('#checkType').prop('disabled', true);
            @endif

            $('a.button:not(.disabled):not(.btn-selected)').click(function () {
                let checkBox = document.getElementById("checkType");
                let type = $(this).attr('data-type');
                let paymentType = parseInt(type);
                // if (checkBox.checked)
                // {
                //     m_curSelectPrice = parseFloat($('#y_type_' + type).attr('price-value'));
                //     paymentType += 4;
                //
                // } else
                {
                    m_curSelectPrice = parseFloat($('#m_type_' + type).attr('price-value'));
                }
                gotoPaymentMethod(paymentType);
            });

            $('a.button.btn-selected').click(function () {
                $.confirm({
                    title: '{{ __('globals.msg.warning') }}!',
                    content: '{{ __('globals.msg.are_you_sure') }}',
                    draggable: true,
                    closeIcon: false,
                    boxWidth: '350px',
                    useBootstrap: false,
                    type: 'red',
                    icon: 'fa fa-exclamation-triangle',
                    closeAnimation: 'top',
                    cancelButton: "{{ __('globals.msg.cancel') }}",
                    buttons: {
                        somethingElse: {
                            text: "{{ __('globals.msg.confirm') }}",
                            btnClass: 'btn-red',
                            keys: ['shift'],
                            action: function(){
                                @if(Auth::guard('admin')->user()->card_brand !== '' && Auth::guard('admin')->user()->card_last_four !== '')
                                    location.href = "{{ route('stripe.cancel') }}";
                                @else
                                    location.href = "{{ route('paypal.cancel') }}";
                                @endif
                            }
                        },
                        cancel: {
                            text: "{{ __('globals.msg.cancel') }}",
                            action: function () {
                                return true;
                            }
                        },
                    }
                });
            });

        });

        let checkMonthlyAnual = () => {
            var checkBox = document.getElementById("checkType");
            var text1 = document.getElementsByClassName("text2");
            var text2 = document.getElementsByClassName("text1");
            if (checkBox.checked == true)
            {
                $('.cost-off').show();
                $('.type3_strike').show();
                $('.type4_strike').show();
            } else
            {
                $('.cost-off').hide();
                $('.type3_strike').hide();
                $('.type4_strike').hide();
            }

            for (var i = 0; i < text1.length; i++) {
                if (checkBox.checked == true) {
                    text1[i].style.display = "block";
                    text2[i].style.display = "none";
                } else if (checkBox.checked == false) {
                    text1[i].style.display = "none";
                    text2[i].style.display = "block";
                }
            }
        }

        function gotoPaymentMethod(type)
        {
            paymentType = type;
            $('#payment_method_modal').modal();
            $('#plan_id').val(paymentType);
            $('#payment-form').attr("action", "{{ url('admin/subscribe/stripe') }}/" + paymentType);
        }

        function gotoPaypal()
        {
            NProgress.start();
            location.href = "{{ url('admin/subscribe/paypal') }}/" + paymentType;
        }

        function gotoStripe()
        {
            if($('#payment-form').is(":visible"))
                $('#payment-form').hide();
            else
                $('#payment-form').show();
        }

        var stripe = Stripe("{{ \Config::get('services.stripe.key') }}");

        // Create an instance of Elements.
        var elements = stripe.elements();

        // Custom styling can be passed to options when creating an Element.
        // (Note that this demo uses a wider set of styles than the guide below.)
        var style = {
            base: {
                color: 'white',
                lineHeight: '18px',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                iconColor: 'white',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };

        // Create an instance of the card Element.
        var card = elements.create('card', {style: style});

        // Add an instance of the card Element into the `card-element` <div>.
        card.mount('#card-element');

        // Handle real-time validation errors from the card Element.
        card.addEventListener('change', function(event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        // Handle form submission.
        var form = document.getElementById('payment-form');
        form.addEventListener('submit', function(event) {
            $('#stripe_pay_btn').text("{{ __('globals.common.loading') }}");
            $('#stripe_pay_btn').prop('disabled', true);
            NProgress.start();
            event.preventDefault();
            stripe.createToken(card).then(function(result) {

                if (result.error) {
                    // Inform the user if there was an error.
                    var errorElement = document.getElementById('card-errors');
                    errorElement.textContent = result.error.message;
                    $('#stripe_pay_btn').text("{{ __('globals.subscription.pay') }}");
                    $('#stripe_pay_btn').prop('disabled', false);
                    NProgress.done();
                } else {
                    // Send the token to your server.
                    stripeTokenHandler(result.token);
                }
            });
        });

        // Submit the form with the token ID.
        function stripeTokenHandler(token) {
            // Insert the token ID into the form so it gets submitted to the server
            stripe.createPaymentMethod({
                type: 'card',
                card: card,
                billing_details: {
                    name: 'Test',
                },
            })
                .then(function(result) {
                    var form = document.getElementById('payment-form');
                    var hiddenInput = document.createElement('input');
                    hiddenInput.setAttribute('type', 'hidden');
                    hiddenInput.setAttribute('name', 'stripeToken');
                    hiddenInput.setAttribute('value', JSON.stringify(result.paymentMethod));
                    form.appendChild(hiddenInput);
                    // Submit the form
                    form.submit();
                });
        }



    </script>
@endpush