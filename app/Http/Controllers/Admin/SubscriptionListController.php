<?php

namespace DLW\Http\Controllers\Admin;

use DLW\Http\Controllers\Controller;
use DLW\Models\Admin;
use DLW\Models\Deposit;
use DLW\Models\Report;
use Illuminate\Http\Request;
use DLW\Models\SubscriptionList;
use DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class SubscriptionListController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin.guard');
        $this->middleware('issuper');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $subscriptionType = config('subscription')['subscription_type'];
        $curType = session()->get('sel_subscription_type') ?? 0;

        return view('admin.subscription.subscription_list', ['title'=>__('globals.subscription.title'), 'type_list' => $subscriptionType, 'sel_type' => $curType, 'user_list' => []]);
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
     * Get all deposits data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function ajaxGetAllData(Request $request)
    {
        if(request()->ajax()) {
            $curType = session()->get('sel_subscription_type');
            if($curType == 0)
            {
                $list = SubscriptionList::orderBy('id')->get();
            } else
            {
                $list = SubscriptionList::where(['type' => $curType])->orderBy('sort')->get();
            }
            return response()->json(['results' => $list]);
        }
    }

    /**
     * Ajax edit data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function ajaxEditData(Request $request)
    {
        if(request()->ajax()) {
            $type = $request->type;
            $id = $request->id;
            $sub_content = $request->sub_content;
            $order = $request->order;

            $validator = Validator::make($request->all(),
                [
                    'id' => 'required',
                    'sub_content' => [
                        'required',
                        Rule::unique('subscription_list')->where(function ($query) use($type, $order) {
                            return $query->where('type', $type)->where('sort', $order);
                        }),
                    ],
                    'order' => 'required|numeric',
                ]
            );

            if ($validator->fails()){
                $error_messages = $validator->errors()->messages();
                return response()->json(['status' => $error_messages]);
            }

            $subLst = new SubscriptionList();
            $updateData = [
                "sort" => $request->order,
                "type" => $type,
                "sub_content" => $sub_content ?? '',
            ];
            $subLst->whereId($id)->update($updateData);

            return response()->json(['status' => 200]);
        }
    }


    /**
     * Ajax save data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function ajaxSaveData(Request $request)
    {
        if(request()->ajax()) {
            $type = $request->type;
            $sub_content = $request->sub_content;
            $validator = Validator::make($request->all(),
                [
                    'sub_content' => [
                        'required',
                        Rule::unique('subscription_list')->where(function ($query) use($type, $sub_content) {
                            return $query->where('type', $type);
                        }),
                    ],
                    'order' => 'required|numeric',
                ]
            );
            if ($validator->fails()){
                $error_messages = $validator->errors()->messages();
                return response()->json(['status' => $error_messages]);
            }

            $subList = new SubscriptionList;
            $subList->type = $type;
            $subList->sub_content = $sub_content;
            $subList->sort = $request->order;
            $subList->save();

            return response()->json(['status' => 200]);
        }
    }

    /**
     * Remove deposits item.
     *
     * @param  SubscriptionList  $item
     * @return \Illuminate\Http\Response
     */
    public function ajaxItemRemove(SubscriptionList $item)
    {
        if (request()->ajax()) {
            $item->delete();

            return response()->json(['status' => 200]);
        }
    }

    /**
     * Set session subscription type
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function ajaxSetSessionSubType(Request $request)
    {
        if(request()->ajax()) {
            session()->put('sel_subscription_type', $request->type);
            return response()->json(['status' => 200]);
        }
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
