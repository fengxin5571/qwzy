<?php

/**
 * Created by Reliese Model.
 * Date: Fri, 14 Jun 2019 09:39:28 +0000.
 */

namespace App\Model;

use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ArticleCategory
 * 
 * @property int $id
 * @property int $pid
 * @property string $title
 * @property int $sort
 * @property int $add_time
 *
 * @package App\Models
 */
class ArticleCategory extends Eloquent
{
    use ModelTree,AdminBuilder;
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setParentColumn('pid');
        $this->setOrderColumn('sort');
        $this->setTitleColumn('title');
    }
	protected $table = 'article_category';
	public $timestamps = false;
    protected $primaryKey='id';
	protected $casts = [
		'pid' => 'int',
		'sort' => 'int',
		'add_time' => 'int'
	];

	protected $fillable = [
		'pid',
		'title',
		'sort',
		'add_time'
	];
}
