<?php

namespace DLW\Http\Controllers\Admin;

use DLW\Http\Controllers\Controller;
use DLW\Models\PasswordReset;
use DLW\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DLW\Libraries\GoogleAnalytics;
use Illuminate\Support\Facades\DB;

class SiteRankingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currencies = ['USD','BRL'];
        $cur_currency = 'BRL';
        $prev_currency = session('cur_currency');

        $start_date = session('rep_start_date');
        $end_date = session('rep_end_date');

        if(!isset($start_date))
        {
            //$end_date = date('Y-m-d');
            //$start_date = date('Y-m-d');
            $start_date = date('Y-m-d', strtotime("-1 days"));
            $end_date = date('Y-m-d', strtotime("-1 days"));

        }

        if(isset($prev_currency) && $prev_currency != "")
        {
            $cur_currency = $prev_currency;
        }

        return view('admin.site_ranking.index', ['title'=> __('globals.common.site_ranking'), 'currencies' => $currencies, 'curcurrency' => $cur_currency, 'rep_start_date' => $start_date, 'rep_end_date' => $end_date]);
    }

    /**
     * Show the form for datatable.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function datatable(Request  $request)
    {
        if(request()->ajax()) {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $currency = $request->get('currency');
            session()->put("rep_start_date", $start_date);
            session()->put("rep_end_date", $end_date);
            $currencyStr = 'R$';
            if ($currency == 'USD')
                $currencyStr = '$';


            $currencyType = intval(session('currency_type'));

            if ($currencyType == 0)  //Auto Method...
            {
                $currencyRate = Report::getCurrenciesRate($currency);
                $currecyMaxRate = floatval(session('currency_max_' . $currency));
                $braRate = session('currency_BRL');
            } else                  //Manual Method...
            {
                $currencyRate = floatval(session('currency_m_' . $currency));
                $currecyMaxRate = floatval(session('currency_m_max_' . $currency));
                $braRate = session('currency_m_BRL');
                session()->put('cur_currency', $currency);
            }


            $res = Report::getTaboolaCampaigns($start_date, $end_date)['results'] ?? [];
            $filterCmpRes = [];
            $cmpCondtionsList = [];

            foreach($res as $value)
            {
                $cmp_id = $value['campaign'];
                $spent = $value['spent'];
                $clicks = $value['clicks'];
                if(floatval($spent) == 0 && floatval($clicks) == 0)
                    continue;
                $filterCmpRes[] = $value;
            }
            $filterCmpRes = array_chunk($filterCmpRes, 150, true);
            foreach ($filterCmpRes as $oneRows)
            {
                $cmpCondtions = '';
                foreach ($oneRows as $value)
                {
                    $cmp_id = $value['campaign'];
                    $cmpCondtions .= "ga:adContent%3D%3D$cmp_id,";
                }
                $cmpCondtionsList[] = substr($cmpCondtions, 0, -1);
            }

            $siteLst = Report::getTaboolaAllSites($start_date, $end_date)['results'] ?? [];

            $dementionLst = ['ga:medium'];

            $matrixLst = ['ga:adsenseRevenue', 'ga:adsenseAdsClicks', 'ga:adsensePageImpressions', 'ga:bounceRate', 'ga:sessions', 'ga:pageviews', 'ga:avgSessionDuration', 'ga:adsenseCTR', 'ga:adsenseECPM'];

            $view_ids = session()->get('view_ids');

            $result = [];

            foreach ($cmpCondtionsList as $cmpCondtion)
            {
                foreach ($view_ids as $key => $value) {
                    $result = array_merge($result, GoogleAnalytics::getSiteList($value, $dementionLst, $matrixLst, $start_date, $end_date, '', $cmpCondtion));
                }
            }

            $all_blocksitelist = session()->get('all_blocksite_list');
            if (!isset($all_blocksitelist)) {
                $all_blocksitelist = Report::getAccountLevelAllSiteList()['sites'];
                session()->get('all_blocksite_list', $all_blocksitelist);
            }

            $returnResult = [];

            $s_spent = 0;
            $s_gSpent = 0;
            $s_rMax = 0;
            $s_roiMin = 0;
            $s_roiMax = 0;
            $s_lMin = 0;
            $s_lMax = 0;
            $s_clicks = 0;
            $s_gBounceRate = 0;
            $s_gPageSession = 0;
            $s_gAvgSessionDuration = 0;
            $s_gCTR = 0;
            $s_gECPM = 0;
            $count = 0;

            $returnHtml = '';
            $ss = 0;


            foreach ($siteLst as $key => $value) {

                $curRes = [];
                $site_name = $value['site'];

                $site_id = $value['site_id'];
                $spent = $value['spent'];
                $clicks = $value['clicks'];
                $real_site_name = $value['site_name'];

                $findVal = Report::findSiteAll($result, $site_name, $site_id);

                if (sizeof($findVal) == 0) continue;

                $gSpent = 0;
                $gBounceRate = 0;
                $gPageSession = 0;
                $gAvgSessionDuration = 0;
                $gCTR = 0;
                $gECPM = 0;

//                foreach ($findVal as $row)
//                {
//                    $gSpent += $row[2] * $currencyRate;
//                    $gBounceRate += $row[5];
//                    $gPageSession += $row[7];
//                    if ($row[6] != 0)
//                        $gPageSession += $row[7] / $row[6];
//
//                    $gAvgSessionDuration += $row[8];
//                    $gCTR += $row[9];
//
//                    $gECPM += $row[10] * $currencyRate;
//                }

                foreach ($findVal as $row)
                {
                    $gSpent += $row[1] * $currencyRate;
                    $gBounceRate += $row[4];
                    if (floatval($row[5]) != 0)
                        $gPageSession += (floatval($row[6]) / floatval($row[5]));
                    else
                        $gPageSession += floatval($row[6]);
                    $gAvgSessionDuration += $row[7];
                    $gCTR += $row[8];

                    $gECPM += $row[9] * $currencyRate;
                }

                $gBounceRate = $gBounceRate / sizeof($findVal);
                $gPageSession = $gPageSession / sizeof($findVal);
                $gAvgSessionDuration = $gAvgSessionDuration / sizeof($findVal);
                $gCTR = $gCTR / sizeof($findVal);
                $gECPM = $gECPM / sizeof($findVal);

                $spent = floatval($spent) / floatval($braRate) * $currencyRate;


                $rMax = $gSpent / $currencyRate * $currecyMaxRate;


                $lMin = $gSpent - $spent;

                $lMax = $gSpent / $currencyRate * $currecyMaxRate - $spent;

                if ($spent == 0) {
                    $roiMin = $lMin / 100;
                    $roiMax = $lMax / 100;
                } else {
                    $roiMin = $lMin / $spent * 100;
                    $roiMax = $lMax / $spent * 100;
                }

                $f_spent = number_format(round($spent, 2), 2, '.', ',');
                $f_gSpent = number_format(round($gSpent, 2), 2, '.', ',');
                $f_rMax = number_format(round($rMax, 2), 2, '.', ',');
                $f_roiMin = number_format(round($roiMin, 2), 2, '.', ',');
                $f_roiMax = number_format(round($roiMax, 2), 2, '.', ',');
                $f_lMin = number_format(round($lMin, 2), 2, '.', ',');
                $f_lMax = number_format(round($lMax, 2), 2, '.', ',');
                $f_clicks = number_format(floatval($clicks), 0, '.', ',');
                $f_gBounceRate = number_format(floatval($gBounceRate), 2, '.', ','); // '%'
                $f_gPageSession = number_format(floatval($gPageSession), 2, '.', ',');
                $f_gAvgSessionDuration = gmdate("H:i:s", $gAvgSessionDuration);
                $f_gCTR = number_format(floatval($gCTR), 2, '.', ','); // '%'
                $f_gECPM = number_format(floatval($gECPM), 2, '.', ',');

                $curRes['f_id'] = $site_id;
                $curRes['f_spent'] = $currencyStr. ' ' .$f_spent;
                $curRes['f_gSpent'] = $currencyStr. ' ' .$f_gSpent;
                $curRes['f_rMax'] = $currencyStr. ' ' .$f_rMax;
                $curRes['f_roiMin'] = $f_roiMin.'%';
                $curRes['f_roiMax'] = $f_roiMax.'%';
                $curRes['f_lMin'] = $currencyStr. ' ' .$f_lMin;
                $curRes['f_lMax'] = $currencyStr. ' ' .$f_lMax;
                $curRes['f_clicks'] = $f_clicks;
                $curRes['f_gBounceRate'] = $f_gBounceRate.'%';
                $curRes['f_gPageSession'] = $f_gPageSession;
                $curRes['f_gAvgSessionDuration'] = $f_gAvgSessionDuration;
                $curRes['f_gCTR'] = $f_gCTR.'%';
                $curRes['f_gECPM'] = $f_gECPM;

                $s_spent += $spent;
                $s_gSpent += $gSpent;
                $s_rMax += $rMax;
                $s_roiMin += $roiMin;
                $s_roiMax += $roiMax;
                $s_lMin += $lMin;
                $s_lMax += $lMax;
                $s_clicks += $clicks;
                $s_gBounceRate += $gBounceRate;
                $s_gPageSession += $gPageSession;
                $s_gAvgSessionDuration += $gAvgSessionDuration;
                $s_gCTR += $gCTR;
                $s_gECPM += $gECPM;

                if ((session()->get('cur_balance') < 100 && Report::isTrialExpried()) && Auth::guard('admin')->user()->id !== 1) {
                    $btn_playHtml = "<button class='btn btn-success waves-effect waves-light btn-sm' disabled title='$site_name'><i class='mdi mdi-play'></i></button>";

                    $btn_pauseHtml = "<button class='btn btn-danger waves-effect waves-light btn-sm' disabled title='$site_name'><i class='mdi mdi-pause'></i></button>";
                } else {
                    $btn_playHtml = "<button id='btn_status_$site_id' site-id='$site_id' title='$site_name' status='play' data-id='$site_name' class='btn btn-success waves-effect waves-light btn-sm' onclick='siteActivate(this)'><i class='mdi mdi-play'></i></button>";

                    $btn_pauseHtml = "<button id='btn_status_$site_id' site-id='$site_id' title='$site_name' status='pause' data-id='$site_name' class='btn btn-danger waves-effect waves-light btn-sm' onclick='siteActivate(this)'><i class='mdi mdi-pause'></i></button>";
                }

                $btn_site_stautsHtml = $btn_pauseHtml;

                if (in_array($site_name, $all_blocksitelist)) {
                    $btn_site_stautsHtml = $btn_playHtml;
                }

                $curRes['f_gStatus'] = $btn_site_stautsHtml;

                $returnHtml .= "<tr><td>".$curRes['f_gStatus']."</td>";
                $returnHtml .= "<td title='$site_name'>".$curRes['f_id']."</td>";
                $returnHtml .= "<td>".$real_site_name."</td>";
                $returnHtml .= "<td>".$curRes['f_spent']."</td>";
                $returnHtml .= "<td>".$curRes['f_gSpent']."</td>";
                $returnHtml .= "<td>".$curRes['f_rMax']."</td>";
                $returnHtml .= "<td>".$curRes['f_roiMin']."</td>";
                $returnHtml .= "<td>".$curRes['f_roiMax']."</td>";
                $returnHtml .= "<td>".$curRes['f_lMin']."</td>";
                $returnHtml .= "<td>".$curRes['f_lMax']."</td>";
                $returnHtml .= "<td>".$curRes['f_clicks']."</td>";
                $returnHtml .= "<td>".$curRes['f_gBounceRate']."</td>";
                $returnHtml .= "<td>".$curRes['f_gPageSession']."</td>";
                $returnHtml .= "<td>".$curRes['f_gAvgSessionDuration']."</td>";
                $returnHtml .= "<td>".$curRes['f_gCTR']."</td>";
                $returnHtml .= "<td>".$curRes['f_gECPM']."</td></tr>";

                //$returnResult[] = $curRes;
                $count++;
            }

            $foot = '';

            if($count > 0)
            {
                if($s_spent != 0)
                {
                    $s_roiMin = number_format(round($s_lMin/$s_spent*100, 2), 2, '.', ',');
                    $s_roiMax = number_format(round($s_lMax/$s_spent*100, 2), 2, '.', ',');
                } else
                {
                    $s_roiMin = number_format(round($s_lMin/1*100, 2), 2, '.', ',');
                    $s_roiMax = number_format(round($s_lMax/1*100, 2), 2, '.', ',');
                }

                $s_spent = number_format(round($s_spent, 2), 2, '.', ',');
                $s_gSpent = number_format(round($s_gSpent, 2), 2, '.', ',');
                $s_rMax = number_format(round($s_rMax, 2), 2, '.', ',');
                $s_lMin = number_format(round($s_lMin, 2), 2, '.', ',');
                $s_lMax = number_format(round($s_lMax, 2), 2, '.', ',');
                $s_clicks = number_format(floatval($s_clicks), 0, '.', ',');

                $s_gAdditionalHtml = "<td>".number_format(floatval($s_gBounceRate/$count), 2, '.', ',').'%'."</td>";
                $s_gAdditionalHtml .= "<td>".number_format(floatval($s_gPageSession/$count), 2, '.', ',')."</td>";
                $s_gAdditionalHtml .= "<td>".gmdate("H:i:s", $s_gAvgSessionDuration/$count)."</td>";
                $s_gAdditionalHtml .= "<td>".number_format(floatval($s_gCTR) / $count, 2, '.', ',').'%'."</td>";
                $s_gAdditionalHtml .= "<td>".number_format(floatval($s_gECPM) / $count, 2, '.', ',')."</td>";

                $foot = "<tr><td colspan='3' class='text-right'>Total</td><td>$currencyStr $s_spent</td><td>$currencyStr $s_gSpent</td><td>$currencyStr $s_rMax</td><td>$s_roiMin%</td><td>$s_roiMax%</td><td>$currencyStr $s_lMin</td><td>$currencyStr $s_lMax</td><td>$s_clicks</td>$s_gAdditionalHtml</tr>";
            }

            return response()->json(['status'=>true, 'result' => $returnResult, 'return_html' => $returnHtml, 'foot' => $foot]);
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
