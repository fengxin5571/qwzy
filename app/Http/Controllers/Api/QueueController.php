<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/8/26
 * Time: 5:39 PM
 */
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Model\CarQueue;
use App\Model\QueueSetting;
use App\Model\TruckQueue;
use App\Services\SplDoublyLinkedList;
use Illuminate\Http\Request;

class  QueueController extends Controller{
    protected $doubly;
    protected $list=[];
    public function __construct()
    {
        $this->doubly=new SplDoublyLinkedList();
        $this->doubly->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO | SplDoublyLinkedList::IT_MODE_KEEP);
        $this->doubly->rewind();
    }

    /**
     * 排队货品分类
     * @return mixed
     */
    public function queueType(){
        $data=QueueSetting::all(['id','alias']);
        if(!$data){
            $data[]=['id'=>0,'alias'=>'全部'];
        }
        return $this->successResponse($data);
    }
    /**
     * 排队看板
     * @return mixed
     */
    public function queue(Request $request){
        $type=$request->input('type');
        $where=[];
        if($type){
            $where['Id_queue_setting']=$type;
        }
        $car_queue=TruckQueue::where($where)->orderBy('sequence')->get(['id','truckname','Id_goods','sequence','status','driver_name','Id_queue_setting']);
        $this->setQueue($car_queue);
        return $this->successResponse($this->list);
    }
    protected function setQueue($car_queues){
        $car_queues->each(function($item,$key) {
            $item->statusName=$item->getStatusName($item->status);
            $this->doubly->push($item);
        });
        foreach($this->doubly as $key=>$value)
        {
            $this->list[$key]=$value;
        }

    }
}