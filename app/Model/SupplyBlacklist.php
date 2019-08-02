<?php

/**
 * Created by Reliese Model.
 * Date: Fri, 02 Aug 2019 10:25:52 +0800.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class SupplyBlacklist
 * 
 * @property int $id
 * @property string $driver_name
 * @property string $mobile
 * @property string $card_id
 * @property int $add_time
 *
 * @package App\Models
 */
class SupplyBlacklist extends Eloquent
{
	protected $table = 'supply_blacklist';
	public $timestamps = false;

	protected $casts = [
		'add_time' => 'date:Y-m-d H:i:s'
	];

	protected $fillable = [
		'driver_name',
		'mobile',
		'card_id',
		'add_time'
	];
}
