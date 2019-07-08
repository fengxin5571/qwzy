<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 17 Jun 2019 09:14:47 +0000.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class CarDiscern
 * 
 * @property int $id
 * @property string $car_region
 *
 * @package App\Models
 */
class CarDiscern extends Eloquent
{
	protected $table = 'car_discern';
	public $timestamps = false;

	protected $fillable = [
		'car_region'
	];
}
