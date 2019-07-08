<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/6/16
 * Time: 8:49 AM
 */
namespace App\Admin\Controllers;
use App\Model\ArticleTag;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class ArticleTagController extends AdminController{
    /**
     * 标签列表
     * @param Content $content
     * @return Content
     */
    public function index(Content $content){
        return $content->header('资讯标签')
               ->description('列表')
               ->breadcrumb(['text'=>'资讯标签'])
               ->body($this->grid());
    }

    /**
     * 标签编辑
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content->header('标签编辑')
               ->description('编辑')
               ->breadcrumb(['text'=>'标签编辑'])
               ->body($this->form()->edit($id));
    }
    protected function grid(){
        $grid=new Grid(new ArticleTag);
        $grid->column('id','ID')->sortable();
        $grid->column('tag_name','标签名称')->editable();
        $grid->actions(function ($actions) {
            $actions->disableView();
        });
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
        });
        $grid->disableColumnSelector();
        $grid->disableExport();
        $grid->paginate(20);
        return $grid;
    }
    protected function form(){
        $form=new Form(new ArticleTag);
        $form->display('id','ID');
        $form->text('tag_name','标签名称')->rules(['required']);
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