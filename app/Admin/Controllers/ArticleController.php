<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/6/14
 * Time: 6:29 PM
 */
namespace App\Admin\Controllers;
use App\Model\Article;
use App\Model\ArticleCategory;
use App\Model\ArticleTag;
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
               ->body($this->form()->edit($id));
    }
    public function grid(){
        $grid=new Grid(new Article);
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
        $form->radio('status','状态')->options(['0' => '隐藏', '1'=> '显示'])->default('1');
        $form->saving(function(Form $form){
            $form->model()->add_time=time();

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
}