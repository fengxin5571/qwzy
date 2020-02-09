<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2020/2/8
 * Time: 4:02 PM
 */
namespace App\Exports;
use Encore\Admin\Grid\Exporters\ExcelExporter;
use Maatwebsite\Excel\Concerns\WithMapping;
class SubSupplyExports extends ExcelExporter implements WithMapping{
    protected $fileName = '预约记录统计.xlsx';
    protected $columns = [
        'id'      => 'ID',
        'car_number'   => '车牌号',
        'driver_name'       =>'司机姓名',
        'shipper_name' => '货主名称',
        'mobile'    =>'司机手机号',
        'axle_number'    =>'车轴数',
        'goods_name'    =>'货物名称',
        'paper_number'    =>'废纸件数',
        'channel'    =>'发货地',
        'unit_transport'    =>'运输单位',
        'sub_type'    =>'预约类型',
        'sub_time'    =>'预约时间',
        'status'    =>'预约状态',



    ];
    public function map($supplier) : array
    {
        $status=['未取卡','已过期','已取卡','已过磅','已超时'];
        return [
            $supplier->id,
            $supplier->car_number,
            $supplier->driver_name,
            $supplier->shipper_name,
            $supplier->mobile."\t",
            $supplier->axle_number?:0,
            $supplier->goods_name,
            $supplier->paper_number?:0,
            $supplier->channel?:'',
            $supplier->unit_transport?:'',
            $supplier->sub_type==1?'临时':'供货商',
            $supplier->sub_time,
            $status[$supplier->status],

        ];
    }
}
