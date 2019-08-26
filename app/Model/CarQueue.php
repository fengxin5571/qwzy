<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Aug 2019 17:56:18 +0800.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class CarQueue
 * 
 * @property int $id
 * @property string $driver_name
 * @property string $car_number
 * @property string $goods_name
 * @property int $status
 *
 * @package App\Models
 */
class CarQueue extends Eloquent
{
	protected $table = 'car_queue';
	public $timestamps = false;
    private $statusName=[
        1=>'正在排队',
        2=>'正在过磅',
    ];
	protected $casts = [
		'status' => 'int',
        'add_time'=>'date:Y-m-d H:i:s'
	];

	protected $fillable = [
		'driver_name',
		'car_number',
		'goods_name',
		'status',
        'add_time'
	];
	public  function getStatusName($key){
	    return $this->statusName[$key];
    }
}
