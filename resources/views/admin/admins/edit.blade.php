@extends('admin.layout')

@section('content')

@include('admin.partials.top-bar')

<div class="page-content-wrapper ">

    <div class="container">

        <div class="row">
            <div class="col-12">
                <div class="card m-b-20">
                    <div class="card-block">
                        <form method="post" action="{{ route('admins.update',$admin->id) }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}
                            @php
                                $display = '';
                                if($admin->is_super == 1) $display = 'none';
                            @endphp
                            @if($errors->any())
                                @php
                                    $admin->name=old('name');
                                    $admin->email=old('email');
                                    $admin->view_id=old('view_id');
                                    $admin->client_id=old('client_id');
                                    $admin->client_secret=old('client_secret');
                                    $admin->account_name=old('account_name');
                                @endphp
                            @endif
                            <div class="form-group row
                            @if($errors->has('name'))
                                has-danger
                            @endif" style="display: {{ $display }}">
                                <label class="col-sm-2 col-form-label">Nome</label>
                                <div class="col-sm-10">
                                    <input name="name" class="form-control" type="text" value="{{ $admin->name }}" id="example-text-input">
                                    @if ($errors->has('name'))
                                        <div class="form-control-feedback" >{{ $errors->first('name') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row
                            @if($errors->has('email'))
                                has-danger
                            @endif">
                                <label class="col-sm-2 col-form-label">E-mail</label>
                                <div class="col-sm-10">
                                    <input name="email" class="form-control" type="email" id="example-email-input" value="{{ $admin->email }}" required>
                                    @if ($errors->has('email'))
                                        <div class="form-control-feedback" >{{ $errors->first('email') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row
                            @if($errors->has('view_id'))
                                has-danger
                            @endif">
                                <label class="col-sm-2 col-form-label">Ver IDS</label>
                                <div class="col-sm-10">
                                    <input name="view_id" class="form-control" data-role="tagsinput" id="tag-viewids" value="{{ $admin->view_id }}">
                                    @if ($errors->has('view_id'))
                                        <div class="form-control-feedback" >{{ $errors->first('view_id') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row
                            @if($errors->has('client_id'))
                                has-danger
                            @endif">
                                <label class="col-sm-2 col-form-label">Client ID</label>
                                <div class="col-sm-10">
                                    <input name="client_id" class="form-control" id="example-view_id-input" value="{{ $admin->client_id }}" required>
                                    @if ($errors->has('client_id'))
                                        <div class="form-control-feedback" >{{ $errors->first('client_id') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row
                            @if($errors->has('client_secret'))
                                has-danger
                            @endif">
                                <label class="col-sm-2 col-form-label">Client Secret</label>
                                <div class="col-sm-10">
                                    <input name="client_secret" class="form-control" id="example-view_id-input" value="{{ $admin->client_secret }}" required>
                                    @if ($errors->has('client_secret'))
                                        <div class="form-control-feedback" >{{ $errors->first('client_secret') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row
                            @if($errors->has('account_name'))
                                has-danger
                            @endif">
                                <label class="col-sm-2 col-form-label">Nome da Conta</label>
                                <div class="col-sm-10">
                                    <input name="account_name" class="form-control" id="example-view_id-input" value="{{ $admin->account_name }}" required>
                                    @if ($errors->has('account_name'))
                                        <div class="form-control-feedback" >{{ $errors->first('account_name') }}</div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="form-group row
                            @if($errors->has('avatar'))
                                has-danger
                            @endif">
                                <label class="col-sm-2 col-form-label">Foto</label>
                                <div class="col-sm-10">
                                    <input name="avatar" class="form-control" type="file" id="avatar">
                                    @if ($errors->has('avatar'))
                                        <div class="form-control-feedback" >{{ $errors->first('avatar') }}</div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="form-group row
                            @if($errors->has('password'))
                                has-danger
                            @endif">
                                <label for="example-password-input" class="col-sm-2 col-form-label">Senha</label>
                                <div class="col-sm-10">
                                    <input name="password" class="form-control" type="password" id="example-password-input">
                                    @if ($errors->has('password'))
                                        <div class="form-control-feedback" >{{ $errors->first('password') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row
                            @if($errors->has('currency_min'))
                                    has-danger
                            @endif">
                                <label class="col-sm-2 col-form-label">Moeda Min(USD:BRL)</label>
                                <div class="col-sm-10">
                                    <input name="currency_min" class="form-control" type="number" min=0 max=100 step="0.01" id="currency_min" value="{{ old('currency_min', $currency_min) }}" required>
                                    @if ($errors->has('currency_min'))
                                        <div class="form-control-feedback" >{{ $errors->first('currency_min') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row
                            @if($errors->has('currency_max'))
                                    has-danger
                            @endif">
                                <label class="col-sm-2 col-form-label">Moeda Max(USD:BRL)</label>
                                <div class="col-sm-10">
                                    <input name="currency_max" class="form-control" type="number" min=0 max=100 step="0.01" id="currency_max" value="{{ old('currency_max', $currency_max) }}" required>
                                    @if ($errors->has('currency_max'))
                                        <div class="form-control-feedback" >{{ $errors->first('currency_max') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row" style="display:{{ $display }};">
                                <label class="col-sm-2 col-form-label">Permissões</label>
                                <div class="col-sm-10">
                                    <label class="custom-control custom-checkbox">
                                        {{ Form::checkbox('report_page', null, old('report_page', $report_page ?? ''), array('id'=>'report_page', 'class'=> 'custom-control-input')) }}
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Relatórios</span>
                                    </label>

                                    <label class="custom-control custom-checkbox">
                                        {{ Form::checkbox('campaign_management_page', null, old('campaign_management_page', $campaign_management_page ?? ''), array('id'=>'campaign_management_page', 'class'=> 'custom-control-input')) }}
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Campanhas</span>
                                    </label>

                                    <label class="custom-control custom-checkbox">
                                        {{ Form::checkbox('ads_page', null, old('ads_page', $ads_page ?? ''), array('id'=>'ads_page', 'class'=> 'custom-control-input')) }}
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Anuncios</span>
                                    </label>

                                    <label class="custom-control custom-checkbox">
                                        {{ Form::checkbox('campaign_page', null, old('campaign_page', $campaign_page ?? ''), array('id'=>'campaign_page', 'class'=> 'custom-control-input')) }}
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Painel de Controle</span>
                                    </label>

                                    <label class="custom-control custom-checkbox">
                                        {{ Form::checkbox('column_visibility', null, old('column_visibility', $column_visibility ?? ''), array('id'=>'column_visibility', 'class'=> 'custom-control-input')) }}
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Colunas Visiveis</span>
                                    </label>

                                    <label class="custom-control custom-checkbox">
                                        {{ Form::checkbox('currency_setting', null, old('currency_setting', $currency_setting ?? ''), array('id'=>'currency_setting', 'class'=> 'custom-control-input')) }}
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Configurações de Moeda</span>
                                    </label>

                                    <label class="custom-control custom-checkbox">
                                        {{ Form::checkbox('financial_setting', null, old('financial_setting', $financial_setting ?? ''), array('id'=>'financial_setting', 'class'=> 'custom-control-input')) }}
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Configurações Financeiras</span>
                                    </label>

                                    <label class="custom-control custom-checkbox">
                                        {{ Form::checkbox('payment_history', null, old('payment_history', $payment_history ?? ''), array('id'=>'payment_history', 'class'=> 'custom-control-input')) }}
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Histórico de Pagamentos</span>
                                    </label>

                                    <label class="custom-control custom-checkbox">
                                        {{ Form::checkbox('content_page', null, old('content_page', $content_page ?? ''), array('id'=>'content_page', 'class'=> 'custom-control-input')) }}
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Conteúdos</span>
                                    </label>

                                    <label class="custom-control custom-checkbox">
                                        {{ Form::checkbox('site_ranking_page', null, old('content_page', $site_ranking_page ?? ''), array('id'=>'content_page', 'class'=> 'custom-control-input')) }}
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Classificação do Site</span>
                                    </label>

                                    <label class="custom-control custom-checkbox">
                                        {{ Form::checkbox('utm_generator', null, old('utm_generator', $utm_generator ?? ''), array('id'=>'utm_generator', 'class'=> 'custom-control-input')) }}
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Gerador de UTM</span>
                                    </label>

                                    <label class="custom-control custom-checkbox">
                                        {{ Form::checkbox('show_total_row', null, old('show_total_row', $show_total_row), array('id'=>'show_total_row', 'class'=> 'custom-control-input')) }}
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">{{ __('globals.common.show_total_row') }}</span>
                                    </label>

                                    <label class="custom-control custom-checkbox">
                                        {{ Form::checkbox('subscription_page', null, old('subscription_page', $subscription_page), array('id'=>'subscription_page', 'class'=> 'custom-control-input')) }}
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">{{ __('globals.common.signature') }}</span>
                                    </label>
                                </div>
                            </div>
                        
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label"></label>
                                <div class="col-sm-10">
                                    <div class="button-items">
                                        <button class="btn btn-primary waves-effect waves-light" type="submit">Salvar Ajustes</button>
                                        <button class="btn btn-secondary waves-effect" type="button" onclick="history.back(1);">Cancelar</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->

    </div><!-- container -->

</div>
@endsection

@push('css')
    <link href="{{ asset('assets/admin/css/tagify.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('scripts')
    <!-- Jquery Tagify -->
    <script src="{{ asset('assets/admin/js/jQuery.tagify.min.js') }}"></script>
<script>
    let viewids = $("#tag-viewids").tagify({
        delimiters:",",
        pattern:/\w+:./,
        maxTags: Infinity
    });
</script>
@endpush
