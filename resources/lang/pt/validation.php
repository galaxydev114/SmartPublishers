<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ' :attribute deve ser aceito.',
    'active_url'           => ' :attribute é uma URL inválida.',
    'after'                => ' :attribute deve ser uma data após :date.',
    'after_or_equal'       => ' :attribute deve ser uma data posterior ou igual a :date.',
    'alpha'                => ' :attribute pode conter apenas letras.',
    'alpha_dash'           => ' :attribute pode conter apenas letras, números e travessões.',
    'alpha_num'            => ' :attribute só pode conter letras e números.',
    'array'                => ' :attribute deve ser um array.',
    'before'               => ' :attribute deve ser uma data antes :date.',
    'before_or_equal'      => ' :attribute deve ser uma data anterior ou igual a :date.',
    'between'              => [
        'numeric' => ' :attribute deve estar entre :min e :max.',
        'file'    => ' :attribute deve estar entre :min e :max kilobytes.',
        'string'  => ' :attribute deve estar entre :min e :max caracteres.',
        'array'   => ' :attribute deve estar entre :min e :max itens.',
    ],
    'boolean'              => ' :attribute campo deve ser verdadeiro ou falso.',
    'confirmed'            => ' :attribute a confirmação não corresponde.',
    'date'                 => ' :attribute não é uma data válida.',
    'date_format'          => ' :attribute não corresponde ao formato :format.',
    'different'            => ' :attribute e :other deve ser diferente.',
    'digits'               => ' :attribute devemos ser :digits digitos.',
    'digits_between'       => ' :attribute deve estar entre :min e :max digitos.',
    'dimensions'           => ' :attribute tem dimensões de imagem inválidas.',
    'distinct'             => ' :attribute campo tem um valor duplicado.',
    'email'                => ' :attribute deve ser um endereço de e-mail válido.',
    'exists'               => ' :attribute selecionado é inválido.',
    'file'                 => ' :attribute deve ser um arquivo.',
    'filled'               => ' :attribute campo deve ter um valor.',
    'image'                => ' :attribute deve ser uma imagem.',
    'in'                   => ' :attribute selecionado é inválido.',
    'in_array'             => ' :attribute campo não existe em :other.',
    'integer'              => ' :attribute deve ser um inteiro.',
    'ip'                   => ' :attribute deve ser um endereço IP válido.',
    'ipv4'                 => ' :attribute deve ser um endereço IPv4 válido.',
    'ipv6'                 => ' :attribute deve ser um endereço IPv6 válido.',
    'json'                 => ' :attribute deve ser uma string JSON válida.',
    'max'                  => [
        'numeric' => ' :attribute não pode ser maior que :max.',
        'file'    => ' :attribute não pode ser maior que :max kilobytes.',
        'string'  => ' :attribute não pode ser maior que :max caracteres.',
        'array'   => ' :attribute não pode ter mais do que :max itens.',
    ],
    'mimes'                => ' :attribute deve ser um arquivo de type: :values.',
    'mimetypes'            => ' :attribute deve ser um arquivo de type: :values.',
    'min'                  => [
        'numeric' => ' :attribute deve ser pelo menos :min.',
        'file'    => ' :attribute deve ser pelo menos :min kilobytes.',
        'string'  => ' :attribute deve ter pelo menos :min caracteres.',
        'array'   => ' :attribute deve ter pelo menos :min itens.',
    ],
    'not_in'               => ' :attribute selecionado é inválido.',
    'numeric'              => ' :attribute deve ser um número.',
    'present'              => ' :attribute campo deve estar presente.',
    'regex'                => ' :attribute formato é inválido.',
    'required'             => ' :attribute campo é obrigatório.',
    'required_if'          => ' :attribute campo é obrigatório quando :other é :value.',
    'required_unless'      => ' :attribute campo é obrigatório, a menos :other é em :values.',
    'required_with'        => ' :attribute campo é obrigatório quando :values é presente.',
    'required_with_all'    => ' :attribute campo é obrigatório quando :values é presente.',
    'required_without'     => ' :attribute campo é obrigatório quando :values não é pressente.',
    'required_without_all' => ' :attribute campo é obrigatório quando nenhum :values são presentes.',
    'same'                 => ' :attribute e :other deve combinar.',
    'size'                 => [
        'numeric' => ' :attribute deve ser :size.',
        'file'    => ' :attribute deve ser :size kilobytes.',
        'string'  => ' :attribute deve ser :size caracteres.',
        'array'   => ' :attribute deve conter :size itens.',
    ],
    'string'               => ' :attribute deve ser um valore inteiro.',
    'timezone'             => ' :attribute deve ser uma zona válida.',
    'unique'               => ' :attribute já foi tomada.',
    'uploaded'             => ' :attribute Falha ao carregar.',
    'url'                  => ' :attribute formato é inválido.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],
    'phone' => ' :attribute campo contém um número de telefone inválido.',

];
