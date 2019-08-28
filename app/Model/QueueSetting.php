<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 28 Aug 2019 09:39:18 +0800.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class QueueSetting
 * 
 * @property int $id
 * @property string $alias
 * @property int $car_num
 *
 * @package App\Models
 */
class QueueSetting extends Eloquent
{
	protected $table = 'queue_setting';
	public $timestamps = false;

	protected $casts = [
		'car_num' => 'int'
	];

	protected $fillable = [
		'alias',
		'car_num'
	];
	//货品组合
	public function goods(){
	    return $this->belongsToMany(SubscribeGoods::class,'queue_goods_relation','q_sid','s_gid');
    }
}
