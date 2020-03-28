<?php

namespace App\Jobs;

use App\Model\SupSupply;
use EasyWeChat\Factory;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class SendSupply implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $app;
    protected $supplys;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //获取未发送的供货记录
        $this->supplys=SupSupply::whereHas('supplier',function ($query){
            $query->where('routine_openid','<>','')->where('status',1);
        })->where('is_send',0)->get();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        if(empty($this->supplys)){
            return;
        }
        if(!config('is_send')){
            return;
        }
        $config=[
            'app_id' => config('appid'),
            'secret' => config('appsecret'),
        ];
        try{
            $this->app=Factory::miniProgram($config);
            foreach ($this->supplys as $supply){
                $data = [
                    'template_id' => 'A6e5WI_zKf4IN3slJfK4DI7O0lGq_L_qoiQZn52OJ8g', // 所需下发的订阅模板id
                    'touser' => $supply->supplier->routine_openid,     // 接收者（用户）的 openid
                    'page' => ' pages/myCommodity/detail?id='.$supply->id,       // 点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,（示例index?foo=bar）。该字段不填则模板无跳转。
                    'data' => [         // 模板内容，格式形如 { "key1": { "value": any }, "key2": { "value": any } }
                        'time1' => [
                            'value' =>date('Y年m月d日 H:i',strtotime($supply->add_time)) ,
                        ],
                        'thing2' => [
                            'value' =>$supply->supplier->shipper_name,
                        ],
                        'thing3' => [
                            'value' => $supply->goods_name,
                        ],
                        'thing4' => [
                            'value' => $supply->weight.'吨',
                        ],
                        'amount5' => [
                            'value' => $supply->Total.'元',
                        ],
                    ],
                ];
                $res=$this->app->subscribe_message->send($data);
                if($res['errcode']){
                    Log::error("微信订阅消息接口错误返回:".$res['errcode'].' info'.$res['errmsg']);
                    //continue;
                }
                $supply->update(['is_send'=>1]);
            }
        }catch (\Exception $e){
            $error_log='发送供货记录通知错误:';
            Log::error($error_log.$e->getMessage());
        }
    }
}
