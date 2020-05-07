<?php

namespace DLW\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Ixudra\Curl\Facades\Curl;


class AutoInit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cronjob:autoinit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialization for CronJob.';

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

        $run_date = date('Y-m-d');
        $calc_date = date('Y-m-d', strtotime("-1 days"));
        $res = DB::table('currency')->where('id',1)->get();
        $currency = "";

        $userList = DB::table('admins')
            ->leftJoin('currency', 'admins.id', '=', 'currency.admin_id')
            ->where([['admins.id', '<>', 1]])
            ->select('admins.id', 'admins.client_id', 'admins.account_name', 'admins.client_secret', 'currency.min_value', 'currency.max_value')
            ->orderBy('admins.id')
            ->get();

        $insertData = [];
        $index = 0;
        $cmpData = [];
        foreach ($userList as $user)
        {
            //if($user->id != 7) continue;
            $index++;
            //$tokenVal = DB::table('cron_init')->where(['user_id' => $user->id, 'date' => $run_date])->get()->toArray();
            //if(sizeof($tokenVal) > 2) continue;
            $minCurrency = $user->min_value ?? 4.2;
            $maxCurrency = $user->max_value ?? 4.2;
            $client_id = $user->client_id;
            $account_id = $user->account_name;
            $client_secret = $user->client_secret;
            if(strlen($client_id) == 32 && strlen($client_secret) == 32)
            {
                $currency = strval($minCurrency). ':' . strval($maxCurrency);
                $token = $this->taboolaAccess($client_id, $client_secret);
                $insertData[] = [
                    'user_id' => $user->id,
                    'account_id' => $account_id,
                    'date' => $run_date,
                    'currency' => $currency,
                    'token' => $token
                ];

                if(!$token) continue;
                $cmpList = $this->getTaboolaAllCampaign($account_id, $token)['results'] ?? [];

                foreach ($cmpList as $key => $value) {
                    if(!array_key_exists('is_active', $value)) continue;
                    $flag = $value['is_active'] == true ? 1 : 0;
                    array_push($cmpData, ['user_id' => $user->id, 'cmpid' => $value['id'], 'status' => $value['status'], 'flag' => $flag]);
                }
            }
        }

        if(sizeof($insertData) > 0)
        {
            DB::table('cron_init')->insert($insertData);
        }
        if(sizeof($cmpData) > 0)
        {
            DB::table('cron_campaign')->truncate();
            DB::table('cron_campaign')->insert($cmpData);
        }

        $this->info('CronInit has been send successfully');
    }

    public function taboolaAccess($to_client_id, $to_client_secret)
    {
        $post = array(
            "client_id"           => $to_client_id,
            "client_secret"       => $to_client_secret,
            "grant_type"          => "client_credentials",
        );

        $base_api_url = env("TO_API_BASE_URL");

        $response = json_decode(Curl::to($base_api_url."/oauth/token")
            ->withData($post)
            ->post(), true);

        if(!$response) return 'Authentication error.';

        if(array_key_exists('access_token', $response))
        {
            return $response['access_token'];
        } else
        {
            return 'Authentication error.';
        }
    }

    public function getTaboolaAllCampaign($account_id, $access_token)
    {

        $base_url =env('TO_API_BASE_URL');
        $url = "$base_url/api/1.0/$account_id/campaigns";

        return $response = json_decode(Curl::to($url)
            ->withBearer($access_token)
            ->get(), true);

    }

}
