<?php

/**
 * Created by Reliese Model.
 * Date: Fri, 14 Jun 2019 10:28:29 +0000.
 */

namespace App\Model;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Article
 * 
 * @property int $id
 * @property int $cid
 * @property string $title
 * @property string $description
 * @property int $sort
 * @property int $add_time
 * @property string $content
 * @property int $status
 *
 * @package App\Models
 */
class Article extends Eloquent
{
	protected $table = 'article';
	public $timestamps = false;

	protected $casts = [
		'cid' => 'int',
		'sort' => 'int',
		'add_time' => 'date:Y-m-d H:i:s',
		'status' => 'int'
	];

	protected $fillable = [
		'cid',
		'title',
		'description',
		'sort',
		'add_time',
		'content',
		'status'
	];
	//状态为显示的文章
	public function scopeOnlie($query){
        return $query->where('status',1);
    }
	public function category(){
	    return $this->hasOne(ArticleCategory::class,'id','c_id');
    }
    public function tags(){
	    return $this->belongsToMany(ArticleTag::class,'article_tag_relation','article_id','tag_id');
    }
}
