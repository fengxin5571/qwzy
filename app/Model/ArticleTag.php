<?php

/**
 * Created by Reliese Model.
 * Date: Fri, 14 Jun 2019 12:45:46 +0000.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ArticleTag
 * 
 * @property int $id
 * @property string $tag_name
 *
 * @package App\Models
 */
class ArticleTag extends Eloquent
{
	protected $table = 'article_tag';
	public $timestamps = false;

	protected $fillable = [
		'tag_name',
        'cid'
	];
	public function article(){
	    //return $this->belongsToMany(Article::class)
    }
    //标签所属分类
    public function category(){
	    return $this->belongsTo(ArticleCategory::class,'cid','id');
    }
}
