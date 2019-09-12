<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 29 Aug 2019 14:57:19 +0800.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class SupSupply
 * 
 * @property int $id
 * @property string $order_sn
 * @property string $driver_name
 * @property string $goods_name
 * @property string $goods_level
 * @property string $car_number
 * @property string $mobile
 * @property string $direction
 * @property float $weight
 * @property string $Total
 * @property string $pct
 * @property float $price
 * @property string $sub_imgs
 * @property int $add_time
 * @property int $supplier_id
 *
 * @package App\Models
 */
class SupSupply extends Eloquent
{
	protected $table = 'sup_supply';
	public $timestamps = false;

	protected $casts = [
		'weight' => 'float',
		'price' => 'float',
		'add_time' => 'date:Y-m-d H:i:s',
		'supplier_id' => 'int'
	];

	protected $fillable = [
		'order_sn',
		'driver_name',
		'goods_name',
		'goods_level',
		'car_number',
        'people_num',
		'mobile',
		'direction',
		'weight',
		'Total',
		'pct',
		'price',
		'sub_imgs',
		'add_time',
		'supplier_id'
	];
    public function setSubImgsAttribute($image)
    {

        if (is_array($image)) {
            $this->attributes['sub_imgs'] = json_encode($image);
        }
    }

    public function getSubImgsAttribute($image)
    {
        return json_decode($image, true);
    }
	public function supplier(){
	    return $this->hasOne(Supplier::class,'id','supplier_id');
    }
    public function getList($request,$user){
        $where['supplier_id']=$user->id;
        if($request->input('time')&&$request->input('time')!='all'){
            if($request->input('time')=='year'){
                $start=strtotime(date("Y",time())."-1"."-1");
                $end=strtotime(date("Y",time())."-12"."-31");
            }elseif ($request->input('time')=='month'){
                $start=strtotime(date('Y')."-".date('m')."-1");
                $end=strtotime(date('Y')."-".date('m')."-".date('t'));
            }elseif ($request->input('time')=='today'){
                $start=strtotime(date("Y-m-d"),time());
                $end=strtotime(date("Y-m-d"),time())+60*60*24;
            }elseif ($request->input('time')=='week'){
                $start=strtotime(date('Y-m-d', strtotime("this week Monday", time())));
                $end=strtotime(date('Y-m-d', strtotime("this week Sunday", time()))) + 24 * 3600 - 1;
            }
            $where[]=['add_time','>=',$start];
            $where[]=['add_time','<=',$end];
        }
        if($request->input('good_id')){
            $goods_name=SubscribeGoods::where('id',$request->input('good_id'))->value('goods_name');
            $where[]=array('goods_name','like',"%{$goods_name}%");
        }
        $data['list'] = $this->where($where)
            ->forPage($request->input('page',1),$request->input('limit',10))->get(['id','driver_name','goods_name','car_number','add_time','supplier_id']);
        $data['count']=$this->where($where)->count();
        return $data;
    }
}
