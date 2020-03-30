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
		'status',
        'is_top',
        'execution_time',
        'notice_type'
	];
	private $type_show=[1=>'废纸',2=>'普报'];
	public function getExecutionTimeAttribute($value){
	    if(!$value){
            $value=time();
        }
	    return date('Y-m-d H:i:s',$value);
    }
    public function setNoticeTypeAttribute($value)
    {
        $this->attributes['notice_type'] = implode(',', $value);
    }
    public function getNoticeTypeAttribute($value)
    {
        if($value){
            return explode(',', $value);
        }

    }
    public function getTypeAttribute(){
	    $txt='';
	    if($this->notice_type){
            foreach ($this->notice_type as $v){
                $txt.=$this->type_show[$v].',';
            }
        }
        return rtrim($txt,',');
    }
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
    public function getList($request){
	    $article=$this;
        $where = [];
        if($request->input('title')){
            $where[]=array('title','like',"%{$request->input('title')}%");
        }
        if($request->input('c_id')){
            $where['c_id']=$request->input('c_id');
        }
        if($request->input('time')){
            if($request->input('time')=='year'){
                $start=strtotime(date("Y",time())."-1"."-1");
                $end=strtotime(date("Y",time())."-12"."-31");
            }elseif ($request->input('time')=='month'){
                $start=strtotime(date('Y')."-".date('m')."-1");
                $end=strtotime(date('Y')."-".date('m')."-".date('t'));
            }elseif ($request->input('time')=='today'){
                $start=strtotime(date("Y-m-d"),time());
                $end=strtotime(date("Y-m-d"),time())+60*60*24;
            }
            $where[]=['add_time','>=',$start];
            $where[]=['add_time','<=',$end];
        }
        $data['list'] = $article->Onlie()->where($where)->whereHas('tags',function($query) use($request){
            if($request->input('tag_id')){
                $query->where('tag_id',$request->input('tag_id'));
            }

        })->forPage($request->input('page',1),$request->input('limit',10))->orderBy('is_top','desc')->orderBy('add_time','desc')->get(['id','c_id','title','description','add_time']);
        $data['count']=$article->Onlie()->where($where)->whereHas('tags',function($query) use($request){
            if($request->input('tag_id')){
                $query->where('tag_id',$request->input('tag_id'));
            }

        })->count();
        return $data;
    }
}
