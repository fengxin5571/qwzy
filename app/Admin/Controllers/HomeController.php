<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Model\SubscribeSupply;
use App\Model\Supplier;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\InfoBox;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->header('控制台')
            ->row(function (Row $row) {
                $sub_temp_count=SubscribeSupply::where(['sub_type'=>1])->whereBetween('sub_time',[strtotime(date("Y-m-d"),time()),strtotime(date("Y-m-d"),time())+60*60*24])->count();
                $sub_temp_count_status0=SubscribeSupply::where(['sub_type'=>1,'status'=>0])->whereBetween('sub_time',[strtotime(date("Y-m-d"),time()),strtotime(date("Y-m-d"),time())+60*60*24])->count();
                $sub_temp_count_status2=SubscribeSupply::where(['sub_type'=>1,'status'=>2])->whereBetween('sub_time',[strtotime(date("Y-m-d"),time()),strtotime(date("Y-m-d"),time())+60*60*24])->count();
                $sub_temp_count_status3=SubscribeSupply::where(['sub_type'=>1,'status'=>3])->whereBetween('sub_time',[strtotime(date("Y-m-d"),time()),strtotime(date("Y-m-d"),time())+60*60*24])->count();
                $sub_sup_count=SubscribeSupply::where(['sub_type'=>2])->whereBetween('sub_time',[strtotime(date("Y-m-d"),time()),strtotime(date("Y-m-d"),time())+60*60*24])->count();
                $sub_sup_count_status0=SubscribeSupply::where(['sub_type'=>2,'status'=>0])->whereBetween('sub_time',[strtotime(date("Y-m-d"),time()),strtotime(date("Y-m-d"),time())+60*60*24])->count();
                $sub_sup_count_status2=SubscribeSupply::where(['sub_type'=>2,'status'=>2])->whereBetween('sub_time',[strtotime(date("Y-m-d"),time()),strtotime(date("Y-m-d"),time())+60*60*24])->count();
                $sub_sup_count_status3=SubscribeSupply::where(['sub_type'=>2,'status'=>3])->whereBetween('sub_time',[strtotime(date("Y-m-d"),time()),strtotime(date("Y-m-d"),time())+60*60*24])->count();
                $sup_count=Supplier::where('status',0)->count();
                $sub_temp=new InfoBox('今日临时预约供货总数', 'calendar', 'aqua', '/admin/subscribe/list?&sub_type=1', $sub_temp_count);
                $sub_temp0=new InfoBox('今日临时预约供货未取卡数', 'calendar', 'red', '/admin/subscribe/list?&sub_type=1&status=0', $sub_temp_count_status0);
                $sub_temp2=new InfoBox('今日临时预约供货已取卡数', 'calendar', 'green', '/admin/subscribe/list?&sub_type=1&status=2', $sub_temp_count_status2);
                $sub_temp3=new InfoBox('今日临时预约供货已过磅数', 'calendar', 'green', '/admin/subscribe/list?&sub_type=1&status=3', $sub_temp_count_status3);
                $sub_supplier=new InfoBox('今日供货商预约供货总数', 'calendar', 'aqua', '/admin/subscribe/list?&sub_type=2', $sub_sup_count);
                $sub_supplier0=new InfoBox('今日供货商预约供货未取卡数', 'calendar', 'red', '/admin/subscribe/list?&sub_type=2&status=0', $sub_sup_count_status0);
                $sub_supplier2=new InfoBox('今日供货商预约供货已取卡数', 'calendar', 'green', '/admin/subscribe/list?&sub_type=2&status=2', $sub_sup_count_status2);
                $sub_supplier3=new InfoBox('今日供货商预约供货已过磅数', 'calendar', 'green', '/admin/subscribe/list?&sub_type=2&status=3', $sub_sup_count_status3);
                $sup=new InfoBox('待审核供货商总数', 'users', 'red', '/admin/suppliers?&status=0', $sup_count);
                $row->column(3,$sub_temp->render());
                $row->column(3,$sub_temp0->render());
                $row->column(3,$sub_temp2->render());
                $row->column(3,$sub_temp3->render());
                $row->column(3,$sub_supplier->render());
                $row->column(3,$sub_supplier0->render());
                $row->column(3,$sub_supplier2->render());
                $row->column(3,$sub_supplier3->render());
                $row->column(3, $sup->render());
            })
           ->row(Dashboard::environment());
    }
}
