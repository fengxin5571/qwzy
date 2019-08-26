<?php

namespace App\Jobs;

use App\Model\SupplyBlacklist;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Model\SubscribeSupply;
class AutoHandleBlackList implements ShouldQueue
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
        //获取状态为已取卡供货记录
        $this->supply=SubscribeSupply::where(['status'=>2])->get();
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
            if($item->take_time+config('take_time')*60*60<time()){
                $insert_array=[
                    'driver_name'=>$item->driver_name,
                    'mobile'     =>$item->mobile,
                    'card_id'    =>$item->card_id,
                    'car_number' =>$item->car_number,
                    'add_time'   =>time()
                ];
                SupplyBlacklist::updateOrCreate(['card_id'=>$item->card_id],$insert_array);
                $item->update(['status'=>4]);
            }
        });
    }
}
