<?php

namespace DLW\Http\Controllers\Admin;

use Carbon\Carbon;
use DLW\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DLW\Models\SubscriptionList;
use DB;
use Auth;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::guard('admin')->user()->is_super && array_key_exists('subscription_page', session()->get('permissions')->toArray()) && session()->get('permissions')->toArray()['subscription_page'] == 1)
        {
            $subList = SubscriptionList::select('type', DB::raw("GROUP_CONCAT(sub_content SEPARATOR '$%!') sub_content"))->groupBy('type')->orderBy('sort')->pluck('sub_content', 'type')->toArray();
            $trial_status = '';
            $badge_status = '';
            $admin = Auth::guard('admin')->user();

            if ($admin->trial_end < date('Y-m-d') && $admin->trial_end != "0000-00-00" && $admin->trial_end != null && $admin->is_subscribed != 1)
            {
                $badge_status = 'danger';
                $trial_status = __('globals.msg.trial_expired');
            } else if ($admin->trial_end >= date('Y-m-d') && $admin->is_subscribed != 1)
            {
                $remain = (strtotime($admin->trial_end) - strtotime(date('Y-m-d')))/60/60/24;
                $badge_status = 'info';
                $trial_status = __('globals.msg.trail_remain', ['days' => intval($remain)]);
            } else if($admin->is_subscribed != 1)
            {
                $badge_status = 'warning';
                $trial_status = __('globals.msg.trail_status_unkown');
            }
            return view('admin.subscription.index', [
                'title' => __('globals.subscription.title'),
                'sub_list' => $subList,
                'trial_status' => $trial_status,
                'badge_status' => $badge_status
            ]);
        } else
        {
            abort(403);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
