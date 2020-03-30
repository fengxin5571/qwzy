<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/6/14
 * Time: 6:29 PM
 */
namespace App\Admin\Controllers;
use App\Admin\Extensions\SendNotice;
use App\Jobs\SendSupply;
use App\Model\Article;
use App\Model\ArticleCategory;
use App\Model\ArticleTag;
use App\Model\SupSupply;
use EasyWeChat\Factory;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Form;

class ArticleController extends AdminController{
    /**
     * 资讯列表
     * @param Content $content
     * @return Content
     */
    public function index(Content $content){
        return $content->header('资讯管理')
                ->description(trans('admin.list'))
                ->breadcrumb(['text'=>'资讯管理'])
                ->body($this->grid());
    }

    /**
     * 新增资讯
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('新增资讯')
            ->description('新增')
            ->breadcrumb(['text'=>'新增资讯'])
            ->body($this->form());
    }

    /**
     * 编辑资讯
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function  edit($id,Content $content){
        return $content->header('编辑资讯')
               ->description('编辑')
               ->breadcrumb(['text'=>'编辑资讯'])
               ->body($this->form()->edit($id));
    }
    public function grid(){
        $grid=new Grid(new Article);
        $grid->model()->orderBy('add_time','desc');
        $grid->column('id', 'ID')->sortable();
        $grid->column('category','资讯分类')->display(function ($category){
            return $category['title'];
        });
        $grid->column('title','资讯标题')->editable();
        $grid->tags('资讯标签')->display(function($tags){
            $tags = array_map(function ($tag) {
                return "<span class='label label-info'>{$tag['tag_name']}</span>";
            }, $tags);

            return join('&nbsp;', $tags);
        });
        $grid->column('add_time','创建时间')->sortable();
        $states = [
            'on'  => ['value' => 1, 'text' => '是', 'color' => 'info'],
            'off' => ['value' => 0, 'text' => '否', 'color' => 'default'],
        ];
        $grid->column('is_top','是否置顶')->switch($states);
        $grid->column('status','状态')->using(['0'=>"<span class='label label-danger'>隐藏</span>",'1'=>"<span class='label label-success'>显示</span>"]);
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->equal('c_id','资讯分类')->select(ArticleCategory::selectOptions(null,'请选择'));
            $filter->like('title','资讯标题')->placeholder('请输入资讯标题');
            $filter->where(function ($query) {
                $query->whereHas('tags',function ($query){
                    $query->where('tag_name','like',"%{$this->input}%");
                });
            }, '资讯标签','tag_name')->placeholder('请输入资讯标签');
            $filter->equal('status','状态')->radio([
                ''   => '所有',
                0    => '隐藏',
                1    => '显示',
            ]);



        });
        $grid->actions(function ($actions) {
            $actions->disableView();
            if($actions->row->c_id==5){
                $actions->prepend(new SendNotice($actions->getKey()));
            }
        });
        $grid->disableColumnSelector();
        $grid->disableExport();
        $grid->paginate(20);
        return $grid;
    }
    protected function form()
    {
        $form = new \Encore\Admin\Form(new Article);
        $form->select('c_id', '资讯分类')->options(ArticleCategory::selectOptions(null,''))->rules('required');
        $form->text('title','资讯标题')->rules('required',[
            'required'=>'资讯标题不能为空',
        ]);
        $form->textarea('description','资讯摘要')->rows(4);
        $form->multipleSelect('tags','资讯标签')->options(ArticleTag::all()->pluck('tag_name', 'id'));
        $form->UEditor('content','资讯内容')->options(['initialFrameHeight' => 500])->rules('required');
        $form->checkbox('notice_type','价格通知类别')->options([1 => '废纸', 2 => '普报'])->help('此项仅为价格调整通知时使用，企业新闻可不设置');
        $form->datetime('execution_time','价格执行时间')->format('YYYY-MM-DD HH:mm:ss')->help('此项仅为价格调整通知时使用，企业新闻可不设置');
        $form->radio('is_top','是否置顶')->options(['0' => '否', '1'=> '是'])->default('0');
        $form->radio('status','状态')->options(['0' => '隐藏', '1'=> '显示'])->default('1');
        $form->saving(function(Form $form){
            if(!$form->model()->add_time){
                $form->model()->add_time=time();
            }
            if($form->execution_time){
                $form->execution_time=strtotime($form->execution_time);
            }
            if($form->_method='PUT'){
                if($form->is_top=='on'){
                    $form->is_top=1;
                }elseif ($form->is_top=='off'){
                    $form->is_top=0;
                }
            }
        });
        $form->tools(function (Form\Tools $tools) {
            // 去掉`查看`按钮
            $tools->disableView();
        });
        $form->footer(function ($footer) {
            // 去掉`查看`checkbox
            $footer->disableViewCheck();
            // 去掉`继续编辑`checkbox
            $footer->disableEditingCheck();
            // 去掉`继续创建`checkbox
            $footer->disableCreatingCheck();
        });
        return $form;
    }
    /**
     * 发送消息
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function send_notice($id){
        $data['status']=true;
        \App\Jobs\SendNotice::dispatch($id);
        admin_toastr('发送成功', 'success',['timeOut'=>1000]);
        return response()->json($data);
    }
}