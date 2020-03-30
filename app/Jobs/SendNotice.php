<?php

namespace App\Jobs;

use App\Model\Article;
use App\Model\Supplier;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use EasyWeChat\Factory;
use Illuminate\Support\Facades\Log;

class SendNotice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $article;
    protected $app;
    public $tries = 1;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->article=Article::find($id);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            $config=[
                'app_id' => config('appid'),
                'secret' => config('appsecret'),
            ];
            $this->app=Factory::miniProgram($config);
            if(empty($this->article)){
                return;
            }
            //获取订阅的供货商
            $sendSupplierList=Supplier::where('status',1)->where('routine_openid','<>','')->get();

            foreach ($sendSupplierList as $supplier){
                $data = [
                    'template_id' => 'F_2ucE1IjX2VhBr2mTlecp6fNHhpIrwZv1eUzV_aTnc', // 所需下发的订阅模板id
                    'touser' => $supplier->routine_openid,     // 接收者（用户）的 openid
                    'page' => 'pages/newsDetail/index?id='.$this->article->id,       // 点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,（示例index?foo=bar）。该字段不填则模板无跳转。
                    'data' => [         // 模板内容，格式形如 { "key1": { "value": any }, "key2": { "value": any } }
                        'time1' => [
                            'value' =>date('Y年m月d日 H:i',strtotime($this->article->execution_time)),
                        ],
                        'thing2' => [
                            'value' =>$this->article->type?$this->article->type:'无类别',
                        ],
                        'thing3' => [
                            'value' => '详细价格调整情况请点击下方“查看详情”',
                        ],
                    ],
                ];
                $res=$this->app->subscribe_message->send($data);
                if($res['errcode']){
                    Log::error("微信订阅消息接口错误返回:".$res['errcode'].' info'.$res['errmsg']);
                    continue;
                }
            }

        }catch (\Exception $e){
            $error_log='发送价格调整通知错误:';
            Log::error($error_log.$e->getMessage().' line:'.$e->getLine());

        }
    }
}
