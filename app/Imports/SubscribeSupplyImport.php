<?php

namespace App\Imports;

use App\Model\SubscribeSupply;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
class SubscribeSupplyImport implements ToCollection,WithHeadingRow,WithBatchInserts,WithChunkReading
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row)
        {
            $existsData=SubscribeSupply::where([
                'mobile'=>(int)$row['司机手机号'],
                'status'=>0,
                'card_id'=>$row['司机身份证号']
            ])->count();
            if($existsData){
                return null;
            }
            SubscribeSupply::create([
                'shipper_name'=>$row['货主名称']?:"",
                'bank_address'=>$row['银行开户行']?:"",
                'bank_code'   =>$row['银行卡号']?:"",
                'car_number'  =>$row['车牌'],
                'driver_name' =>$row['司机姓名'],
                'mobile'      =>(int)$row['司机手机号'],
                'card_id'     =>$row['司机身份证号'],
                'goods_name'  =>$row['货品名称'],
                'axle_number' =>$row['车轴数'],
                'load_weight' =>$row['荷载重量'],
                'channel'     =>$row['运输来源'],
                'unit_name'   =>$row['供货单位'],
                'unit_transport'=>$row['运输单位'],
                'paper_number'  =>$row['废纸件数'],
                'sub_type'      =>$row['预约类型']=='临时'?1:2,
                'supplier_id'   =>$row['供货商id']?:0,
                'sub_time'      =>time(),
                'sub_code'      =>$this->makeRandCode(),
                'expire_time'   =>time()+config('expire_time')*60*60
            ]);
        }
    }
//    public function model(array $row)
//    {
//        $supply= new SubscribeSupply([
//            'shipper_name'=>$row['货主名称']?:"",
//            'bank_address'=>$row['银行开户行']?:"",
//            'bank_code'   =>$row['银行卡号']?:"",
//            'car_number'  =>$row['车牌'],
//            'driver_name' =>$row['司机姓名'],
//            'mobile'      =>(int)$row['司机手机号'],
//            'card_id'     =>$row['司机身份证号'],
//            'goods_name'  =>$row['货品名称'],
//            'axle_number' =>$row['车轴数'],
//            'load_weight' =>$row['荷载重量'],
//            'channel'     =>$row['运输来源'],
//            'unit_name'   =>$row['供货单位'],
//            'unit_transport'=>$row['运输单位'],
//            'paper_number'  =>$row['废纸件数'],
//            'sub_type'      =>$row['预约类型']=='临时'?1:2,
//            'supplier_id'   =>$row['供货商id']?:0,
//            'sub_time'      =>time(),
//            'sub_code'      =>$this->makeRandCode(),
//            'expire_time'   =>time()+config('expire_time')*60*60
//        ]);
//        return $supply;
//    }


    public function batchSize(): int
    {
        return 300;
    }
    public function chunkSize(): int
    {
        return 1000;
    }
    //随机生成短信验证码
    protected function makeRandCode()
    {
        // 生成4位随机数，左侧补0
        return random_int(1000,9999);
    }
}
