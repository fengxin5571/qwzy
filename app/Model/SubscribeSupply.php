<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 18 Jun 2019 01:57:36 +0000.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class SubscribeSupply
 * 
 * @property int $id
 * @property string $driver_name
 * @property string $shipper_name
 * @property string $car_number
 * @property int $mobile
 * @property int $sub_type
 * @property int $sub_time
 * @property string $goods_name
 * @property int $sub_code
 * @property int $status
 *
 * @package App\Models
 */
class SubscribeSupply extends Eloquent
{
	protected $table = 'subscribe_supply';
	public $timestamps = false;

	protected $casts = [
		'sub_type' => 'int',
		'sub_time' => 'int',
		'sub_code' => 'int',
		'status' => 'int',
        'supplier_id'=>'int'
	];

	protected $fillable = [
		'driver_name',
		'shipper_name',
		'car_number',
		'mobile',
		'sub_type',
		'sub_time',
        'expire_time',
        'supplier_id',
		'goods_name',
		'sub_code',
        'bank_address',
        'bank_code',
		'status',
        'card_id'
	];
    public function setSubImagesAttribute($image)
    {
        if (is_array($image)) {
            $this->attributes['sub_images'] = json_encode($image);
        }
    }

    public function getSubImagesAttribute($image)
    {
        return json_decode($image, true);
    }
}
