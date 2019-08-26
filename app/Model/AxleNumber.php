<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 26 Aug 2019 09:39:44 +0800.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class AxleNumber
 * 
 * @property int $id
 * @property string $axle_number
 * @property int $add_time
 *
 * @package App\Models
 */
class AxleNumber extends Eloquent
{
	protected $table = 'axle_number';
	public $timestamps = false;

	protected $casts = [
		'add_time' => 'date:Y-m-d H:i:s',
        'axle_number'=>'int'
	];

	protected $fillable = [
		'axle_number',
		'add_time'
	];
}
