<?php

namespace App\Exports;

use App\SubscribeSupply;
use Maatwebsite\Excel\Concerns\FromCollection;

class SubscribeSupplyExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return SubscribeSupply::all();
    }
}
