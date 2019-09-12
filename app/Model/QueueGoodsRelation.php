<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 12 Sep 2019 20:55:36 +0800.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class QueueGoodsRelation
 * 
 * @property int $id
 * @property int $q_sid
 * @property int $s_gid
 *
 * @package App\Models
 */
class QueueGoodsRelation extends Eloquent
{
	protected $table = 'queue_goods_relation';
	public $timestamps = false;

	protected $casts = [
		'q_sid' => 'int',
		's_gid' => 'int'
	];

	protected $fillable = [
		'q_sid',
		's_gid'
	];
}
