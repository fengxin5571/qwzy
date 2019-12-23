<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/9/9
 * Time: 1:20 PM
 */
namespace App\Admin\Controllers;
use App\Admin\Extensions\QueueButton;
use App\Model\TruckQueue;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;

class  QueueController extends AdminController{
    public function index(Content $content){
        return $content
               ->header('排队看板')
               ->breadcrumb(['text'=>'排队看板'])
               ->body($this->grid());
    }
    /**
     * 排队调整
     * @param $id
     * @param Request $request
     * @return array
     */
    public function modify($id,Request $request){
        $sortable=$request->input('sortable');
        TruckQueue::find($id)->move($sortable);
        return ['status' => 1, 'message' => '操作成功'];
    }
    protected function grid(){
        $grid=new Grid(new TruckQueue());
        $grid->model()->orderBy('sequence');
        $grid->column('id','ID');
        $grid->column('sequence','排名')->label('info');
        $grid->column('truckname','车牌')->label('primary');
        $grid->column('driver_name','司机姓名');
        $grid->column('Id_goods','供货货品');
        $grid->column('status','排队状态')->using(['1'=>'等待过磅','2'=>'等待上磅','3'=>'过磅','4'=>'出厂','5'=>'超时等待处理']);
        $grid->actions(function ($actions) {
            // append一个操作
            if($actions->row->status==1||$actions->row->status==5){
                $actions->prepend(new QueueButton($actions->getKey()));
            };

            // 去掉删除
            $actions->disableDelete();
            // 去掉编辑
            $actions->disableEdit();
            // 去掉查看
            $actions->disableView();
        });
        //去掉多选框
        $grid->disableRowSelector();
        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                //去掉批量删除
                $batch->disableDelete();
            });
        });
        // 全部关闭
        $grid->disableCreateButton();
        $grid->disablePagination();
        return $grid;
    }

}