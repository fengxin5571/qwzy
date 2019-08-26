<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Aug 2019 15:04:28 +0800.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class CarBlacklist
 * 
 * @property int $id
 * @property string $car_number
 * @property int $add_time
 *
 * @package App\Models
 */
class CarBlacklist extends Eloquent
{
	protected $table = 'car_blacklist';
	public $timestamps = false;

	protected $casts = [
		'add_time' => 'date:Y-m-d H:s:i'
	];

	protected $fillable = [
		'car_number',
		'add_time'
	];
}
