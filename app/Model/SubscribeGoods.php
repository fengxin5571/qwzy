<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 17 Jun 2019 10:28:04 +0000.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class SubscribeGood
 * 
 * @property int $id
 * @property string $goods_name
 * @property int $add_time
 * @property int $is_ temp
 * @property int $is_sup
 *
 * @package App\Models
 */
class SubscribeGoods extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'add_time' => 'date:Y-m-d H:i:s',
		'is_ temp' => 'int',
		'is_sup' => 'int'
	];

	protected $fillable = [
		'goods_name',
		'add_time',
		'is_temp',
		'is_sup'
	];
	//获取对应货品
	public static function getTypeGoods($type=1,$fields=['*']){
	    $where=$type==1?['is_temp'=>'1']:['is_sup'=>'1'];
	    return self::where($where)->get($fields);
    }
    public function  queueSetting(){
	    return $this->hasOne(QueueGoodsRelation::class,'s_gid','id');
    }
}
