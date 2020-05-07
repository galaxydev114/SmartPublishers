<?php

namespace DLW\Console\Commands;

use DLW\Models\Report;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Ixudra\Curl\Facades\Curl;
use DB;
use Carbon\Carbon;
use DLW\Models\Admin;

class AutoBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cronjob:autobalance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto balance get and campaigin pause.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Balance auto get and campaigin pause processor.
        $availUsers = DB::table('cron_campaign')->where(['status' => 'RUNNING'])->select(DB::raw('DISTINCT(user_id)'))->orderBy('user_id')->get();
        $runDate = date('Y-m-d');
        $subDate = Carbon::now()->subDay()->format('Y-m-d');
        $insertDataList = [];
        //Get total Deposit
        foreach ($availUsers as $user)
        {
            if(Admin::find($user->user_id)->is_subscribed === 1) continue;
            $curUser = DB::table('admins')->where('id', $user->user_id)->select('trial_end', 'is_subscribed', 'is_super', 'is_subscribed')->first();
            $totalDeposit = DB::table('deposits')->where(['user_id' => $user->user_id])->sum('amount');
            //Get User Toboola Access Info
            $userAccessInfo = DB::table('cron_init')->where(['user_id' => $user->user_id])->orderBy('id', 'desc')->first();
            if(!$userAccessInfo) continue;
            //Get Total Spent
            $totalSpent = $this->getTaboolaMonthSpentTotal($userAccessInfo->account_id, $userAccessInfo->token);

            $insertDataList[] = [
                'user_id' => $user->user_id,
                'spent' => $totalSpent,
                'deposit' => $totalDeposit,
                'run_date' => $runDate,
                'created_date' => date('Y-m-d H:i:s')
            ];

            $diffSpentList = [];
            $avgDiff = 0;

            if($totalDeposit - $totalSpent > 100)
            {
                $lastBalanceList = DB::table('cron_balance')->where(['user_id' => $user->user_id])->whereIn('run_date', [$runDate, $subDate])->orderBy('id')->limit(24)->pluck('spent', 'created_date')->toArray();

//                if(sizeof($lastBalanceList) > 4)
//                {
//                    for($index = 1; $index < sizeof($lastBalanceList) - 1 ; $index++)
//                    {
//                        $diffSpentList[] = $lastBalanceList[$index] - $lastBalanceList[$index - 1];
//                    }
//
//                    $diffSpentList = array_filter($diffSpentList);
//                    $avgDiff = array_sum($diffSpentList) / count($diffSpentList);
//                }
                if(sizeof($lastBalanceList) > 1)
                {
                    end($lastBalanceList);
                    $lastKey = key($lastBalanceList);
                    $lastHours = intval(substr($lastKey, 11, 2));
                    $lastRunDate =  substr($lastKey, 0, 10);
                    $lastPrice = $lastBalanceList[$lastKey];

                    prev($lastBalanceList);
                    $prevKey = key($lastBalanceList);
                    $prevHours = intval(substr($prevKey, 11, 2));
                    $prevRunDate = substr($prevKey, 0, 10);
                    $prevPrice = $lastBalanceList[$prevKey];

                    if($prevRunDate == $lastRunDate && $lastHours - $prevHours == 1)
                    {
                        $avgDiff = floatval($lastPrice) - floatval($prevPrice);
                        if($avgDiff < 0) $avgDiff = 0;
                    }
                }
            }
            if(($totalDeposit - $totalSpent < 100 || $totalDeposit - ($totalSpent + $avgDiff) < 100) && $this->isTrialExpried($curUser)) // Campign pause Automatically Logic
            {
                $sendVal =  [
                    'is_active' => false
                ];

                $availCmpIds = DB::table('cron_campaign')->where(['status' => 'RUNNING', 'user_id' => $user->user_id])->pluck('cmpid');

                if(sizeof($availCmpIds) > 0)
                {
                    $sendCmpVal = [
                        'campaigns' => $availCmpIds,
                        'update' => $sendVal
                    ];
                    $this->cronJobBulkUpdateTaboolaCampaigns($userAccessInfo->account_id, $userAccessInfo->token, $sendCmpVal);
                    DB::table('cron_campaign')->where(['status' => 'RUNNING', 'user_id' => $user->user_id])->update(
                        [
                            'status' => 'PAUSED',
                            'flag' => 0
                        ]
                    );
                }
            }
        }
        if(sizeof($insertDataList) > 0)
            DB::table('cron_balance')->insert($insertDataList);

        $this->info('CronAutoBalance has been send successfully');

    }

    /**
     * Get Toboola Month Spent Total
     * @param  string $account_id
     * @param  string $access_token
     * @return integer total
     */
    public function getTaboolaMonthSpentTotal($account_id, $access_token)
    {
        $base_url =env('TO_API_BASE_URL');
        $end_date = Carbon::now()->addMonth()->format('Y-m-d');

        $url = "$base_url/api/1.0/$account_id/reports/campaign-summary/dimensions/month?start_date=2019-01-01&end_date=$end_date";
        $res =  json_decode(Curl::to($url)
            ->withBearer($access_token)
            ->get(), true)['results'] ?? [];

        $total = 0;
        foreach ($res as $row)
        {
            if(array_key_exists('spent', $row))
            {
                if($row['spent'] != 0)
                {
                    $total += $row['spent'];
                }
            }
        }
        return $total;
    }

    /**
     * Get Toboola Month Spent Total
     * @param  string $account_id
     * @param  string $access_token
     * @param  array $value
     * @return integer total
     */
    public function cronJobBulkUpdateTaboolaCampaigns($account_id, $access_token, $value)
    {
        $base_url =env('TO_API_BASE_URL');
        $url = "$base_url/api/1.0/$account_id/campaigns/bulk-update";

        return Curl::to($url)
               ->withBearer($access_token)
                ->asJson()
                ->withData($value)
               ->post() ?? [];
    }

    /**
     * Get Is Trail Expried Status
     * @param  object $admin
     * @return boolean
     */
    public function isTrialExpried($admin)
    {
        if ($admin->trial_end < date('Y-m-d') &&  $admin->is_subscribed != 1 && $admin->is_super !== 1)
            return true;
        return false;
    }

}
