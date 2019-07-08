<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/6/17
 * Time: 5:59 PM
 */
namespace App\Admin\Controllers;
use App\Model\CarLetter;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class CarletterController extends AdminController{
    /**
     * 车牌字母
     * @param Content $content
     * @return Content
     */
    public function index(Content $content){
        return $content->header('车牌字母')
            ->description(trans('admin.list'))
            ->breadcrumb(['text'=>'车牌字母'])
            ->body($this->grid());
    }

    /**
     * 新增车牌字母
     * @param Content $content
     * @return Content
     */
    public function create(Content $content){
        return $content->header('新增车牌字母')
            ->description(trans('新增'))
            ->breadcrumb(['text'=>'新增车牌字母'])
            ->body($this->form());
    }
    public function edit($id,Content $content){
        return $content->header('编辑车牌字母')
            ->description(trans('编辑'))
            ->breadcrumb(['text'=>'编辑车牌字母'])
            ->body($this->form()->edit($id));
    }
    protected function grid(){
        $grid=new Grid(new CarLetter);
        $grid->column('id',"ID")->sortable();
        $grid->column('car_letter','车牌字母')->sortable();
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
        $form=new Form(new CarLetter);
        $form->text('car_letter','车牌字母')->pattern('[A-Z]{1}')->rules(['required']);
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