<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 04 Aug 2019 09:46:29 +0800.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Notice
 * 
 * @property int $id
 * @property string $title
 * @property string $content
 * @property int $type
 * @property int $add_time
 *
 * @package App\Models
 */
class Notice extends Eloquent
{
	protected $table = 'notice';
	public $timestamps = false;

	protected $casts = [
		'type' => 'int',
		'add_time' => 'int'
	];

	protected $fillable = [
		'title',
		'content',
        'supplier_id',
		'type',
		'add_time'
	];
}
