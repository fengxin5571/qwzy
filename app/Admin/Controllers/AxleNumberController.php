<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/8/26
 * Time: 9:42 AM
 */
namespace App\Admin\Controllers;
use App\Model\AxleNumber;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class AxleNumberController extends AdminController{
    /**
     * 车轴数管理
     * @param Content $content
     * @return Content
     */
    public function index(Content $content){
        return $content
            ->header('车轴数管理')
            ->description(trans('admin.list'))
            ->breadcrumb(['text'=>'车轴数管理'])
            ->body($this->grid());
    }
    /**
     * 新增车轴数
     * @param Content $content
     * @return Content
     */
    public function create(Content $content){
        return $content
            ->header('新增车轴数')
            ->description('新增')
            ->breadcrumb(['text'=>'新增车轴数'])
            ->body($this->form());
    }
    /**
     * 编辑车轴数
     * @param Content $content
     * @param Content $id
     * @return Content
     */
    public function edit($id,Content $content){
        return $content
            ->header('编辑车轴数')
            ->description('编辑')
            ->breadcrumb(['text'=>'编辑车轴数'])
            ->body($this->form()->edit($id));
    }
    protected function grid(){
        $grid=new Grid(new AxleNumber());
        $grid->column('id','ID')->sortable();
        $grid->column('axle_number','车轴数')->editable();
        $grid->column('add_time','添加时间')->sortable();
        return $grid;
    }
    protected function form(){
        $form=new Form(new AxleNumber());
        $form->number('axle_number','车轴数')->required()->default('1');
        $form->saving(function(Form $form){
            $form->model()->add_time=time();

        });
        return $form;
    }
}