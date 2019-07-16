<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 17 Jun 2019 09:14:47 +0000.
 */

namespace App\Model;

use James\Sortable\Sortable;
use James\Sortable\SortableTrait;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class CarDiscern
 * 
 * @property int $id
 * @property string $car_region
 *
 * @package App\Models
 */
class CarDiscern extends Eloquent implements Sortable
{
    use SortableTrait;
    public $sortable = [
        'sort_field' => 'sort',       // 排序字段
        'sort_when_creating' => true,   // 新增是否自增，默认自增
    ];
	protected $table = 'car_discern';
	public $timestamps = false;

	protected $fillable = [
		'car_region',
        'status',
        'sort'
	];

}
