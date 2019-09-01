<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 29 Aug 2019 14:57:19 +0800.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class SupSupply
 * 
 * @property int $id
 * @property string $order_sn
 * @property string $driver_name
 * @property string $goods_name
 * @property string $goods_level
 * @property string $car_number
 * @property string $mobile
 * @property string $direction
 * @property float $weight
 * @property string $Total
 * @property string $pct
 * @property float $price
 * @property string $sub_imgs
 * @property int $add_time
 * @property int $supplier_id
 *
 * @package App\Models
 */
class SupSupply extends Eloquent
{
	protected $table = 'sup_supply';
	public $timestamps = false;

	protected $casts = [
		'weight' => 'float',
		'price' => 'float',
		'add_time' => 'date:Y-m-d H:i:s',
		'supplier_id' => 'int'
	];

	protected $fillable = [
		'order_sn',
		'driver_name',
		'goods_name',
		'goods_level',
		'car_number',
        'people_num',
		'mobile',
		'direction',
		'weight',
		'Total',
		'pct',
		'price',
		'sub_imgs',
		'add_time',
		'supplier_id'
	];
    public function setSubImgsAttribute($image)
    {
        if (is_array($image)) {
            $this->attributes['sub_imgs'] = json_encode($image);
        }
    }

    public function getSubImgsAttribute($image)
    {
        return json_decode($image, true);
    }
	public function supplier(){
	    return $this->hasOne(Supplier::class,'id','supplier_id');
    }
}
