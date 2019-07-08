<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 17 Jun 2019 09:59:06 +0000.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class CarLetter
 * 
 * @property int $id
 * @property string $car_letter
 *
 * @package App\Models
 */
class CarLetter extends Eloquent
{
	protected $table = 'car_letter';
	public $timestamps = false;

	protected $fillable = [
		'car_letter'
	];
}
