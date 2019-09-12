<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/8/28
 * Time: 9:19 AM
 */
namespace App\Admin\Controllers;
use App\Model\QueueSetting;
use App\Model\SubscribeGoods;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Table;

class QueueSettingController extends AdminController{
    /**
     * 排队规则设置
     * @param Content $content
     * @return Content
     */
    public function index(Content $content){
        return $content
            ->header('排队设置')
            ->description('设置')
            ->breadcrumb(['text'=>'排队设置'])
            ->body($this->grid());
    }

    /**
     * 新增规则
     * @param Content $content
     * @return Content
     */
    public function create(Content $content){
        return $content
            ->header('新增排队设置')
            ->description('新增')
            ->breadcrumb(['text'=>'新增排队设置'])
            ->body($this->form());
    }
    public function edit($id,Content $content){
        return $content
            ->header('编辑排队设置')
            ->description('编辑')
            ->breadcrumb(['text'=>'编辑排队设置'])
            ->body($this->form()->edit($id));
    }
    protected function grid(){
        $grid=new Grid(new QueueSetting());
        $grid->column('id',"ID")->sortable();
        $grid->column('alias','配置别名')->editable();
        $grid->column('car_num','放行车辆数')->editable();
        $grid->column('goods','货品组合')->display(function ($goods){
            $goods = array_map(function ($good) {
                return <<<EOT
             <div class="layui-table-cell laytable-cell-1-0-2">   
                
                     <span class="label label-info">{$good['goods_name']} </span> 
                
             </div>   
EOT;
            }, $goods);

            return join('&nbsp;', $goods);
        });
        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableEdit();
        });
        return $grid;
    }
    protected function form(){
        $form=new Form(new QueueSetting());
        $form->text('alias','配置别名')->required()->help('用于排队看板的货品分类筛选');
        $form->number('car_num','可放行车辆数')->min(0)->required()->help('设置N辆车之后进入到排队等待')->placeholder('车辆数')->default(0);
        $form->listbox('goods','货品组合')->required()->options(SubscribeGoods::doesntHave('queueSetting')->pluck('goods_name','id'));
        return $form;
    }
}