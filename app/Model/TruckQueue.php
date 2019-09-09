<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 09 Sep 2019 09:26:19 +0800.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class TruckQueue
 * 
 * @property int $id
 * @property string $truckname
 * @property string $Id_goods
 * @property int $Id_queue_setting
 * @property int $add_time
 * @property int $sequence
 * @property int $status
 * @property string $driver_name
 * @property string $IDCard
 *
 * @package App\Models
 */
class TruckQueue extends Eloquent
{
	protected $table = 'truck_queue';
	private $statusName=['1'=>'取卡','2'=>'等待','3'=>'过磅','4'=>'出厂','5'=>'超时等待处理'];
	public $timestamps = false;

	protected $casts = [
		'Id_queue_setting' => 'int',
		'add_time' => 'int',
		'sequence' => 'int',
		'status' => 'int'
	];

	protected $fillable = [
		'Id_goods',
		'Id_queue_setting',
		'add_time',
		'sequence',
		'status',
		'IDCard'
	];
    public function getStatusName($status){
        return $this->statusName[$status];
    }
}
