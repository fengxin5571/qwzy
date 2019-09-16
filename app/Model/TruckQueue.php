<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 09 Sep 2019 09:26:19 +0800.
 */

namespace App\Model;

use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;
use James\Sortable\Sortable;
use James\Sortable\SortableTrait;
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
class TruckQueue extends Eloquent implements Sortable
{
    use SortableTrait;
	protected $table = 'truck_queue';
	private $statusName=['1'=>'等待过磅','2'=>'等待上磅','3'=>'正在过磅','4'=>'出厂','5'=>'超时等待处理'];
	public $timestamps = false;
    public $sortable = [
        'sort_field' => 'sequence',       // 排序字段
        'sort_when_creating' => false,   // 新增是否自增，默认自增
    ];
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
		'IDCard',
        'truckname'
	];
    public function getStatusName($status){
        return $this->statusName[$status];
    }
}
