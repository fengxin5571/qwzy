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
     * 排队看板
     * @return mixed
     */
    public function queue(Request $request){
        $car_queue=CarQueue::all();
        $this->setQueue($car_queue,$request->input('page',1),$request->input('limit',15));
        return $this->successResponse($this->list);
    }
    protected function setQueue($car_queues,$page=1,$limit=15){
        $car_queues->each(function($item,$key) {
            $item->statusName=$item->getStatusName($item->status);
            $this->doubly->push($item);
        });
        foreach($this->doubly as $key=>$value)
        {
            $merge_array=array_merge(['sortNum'=>$key+1],$value->toArray());
            $this->list[$key]=$merge_array;
        }
        $start=($page-1)*$limit;
        $this->list=array_slice($this->list,$start,$limit,true);
    }
}