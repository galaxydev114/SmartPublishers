<?php

namespace DLW\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionList extends Model
{
    protected $table = 'subscription_list';

    protected $fillable = [
        'type', 'sub_content', 'sort'
    ];

}
