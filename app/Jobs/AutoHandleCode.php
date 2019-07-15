<?php

namespace App\Jobs;

use App\Model\SubscribeSupply;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AutoHandleCode implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $supply;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //获取状态为有效且已经过期的供货记录
        $this->supply=SubscribeSupply::where(['status'=>0,['expire_time','<',time()]])->get();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        if(empty($this->supply)){
            return;
        }
        $this->supply->each(function ($item,$key){
            $item->update(['status'=>1]);
        });
    }
}
