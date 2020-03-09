<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2020/3/9
 * Time: 6:48 PM
 */
namespace App\Exports;
use App\Model\Supplier;
use Encore\Admin\Grid\Exporters\ExcelExporter;
use Maatwebsite\Excel\Concerns\WithMapping;
class SupSupplyExports extends ExcelExporter implements WithMapping{
    protected $fileName = '供货商供货记录统计.xlsx';
    protected $columns = [
        'id'      => 'ID',
        'order_sn'   => '磅单号',
        'supplier_id'=>'供货商姓名',
        'car_number' =>'车牌号',
        'driver_name'=>'司机姓名',
        'mobile'     =>'司机手机号',
        'goods_level'=>'级别',
        'goods_name' =>'货品名称',
        'direction'  =>'运输方向',
        'weight'     =>'货物总重',
        'price'      =>'单价',
        'Total'      =>'总额',
        'pct_other'  =>'扣杂',
        'pct_water'  =>'扣水',
        'add_time'   =>'供货时间',

    ];
    public function map($supplier) : array
    {
        return [
            $supplier->id,
            $supplier->order_sn,
            Supplier::where('id',$supplier->supplier_id)->value('shipper_name'),
            $supplier->car_number,
            $supplier->driver_name,
            $supplier->mobile."\t",
            $supplier->goods_level,
            $supplier->goods_name,
            $supplier->direction,
            $supplier->weight,
            $supplier->price,
            $supplier->Total,
            $supplier->pct_other,
            $supplier->pct_water,
            $supplier->add_time
        ];
    }
}