<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2020/2/7
 * Time: 9:05 AM
 */
namespace App\Exports;
use Encore\Admin\Grid\Exporters\ExcelExporter;
use Maatwebsite\Excel\Concerns\WithMapping;
class SuppliersExports extends ExcelExporter implements WithMapping{

    protected $fileName = '供货商资料统计.xlsx';
    protected $columns = [
        'id'      => 'ID',
        'shipper_name'   => '货主姓名',
        'mobile'       =>'货主手机号',
        'bank_address' => '银行卡开户行',
        'bank_code'    =>'银行卡号',
    ];
    public function map($supplier) : array
    {
        return [
            $supplier->id,
            $supplier->shipper_name,
            $supplier->mobile,
            $supplier->bank_address,
            $supplier->bank_code."\t",
        ];
    }
}