<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/8/28
 * Time: 6:05 PM
 */
namespace App\Admin\Extensions\Tools;
use Encore\Admin\Grid\Tools\AbstractTool;
use Encore\Admin\Admin;
use Illuminate\Support\Facades\Request;
class SubSupplyimport extends AbstractTool{
    protected function script()
    {
        $url = Request::fullUrlWithQuery(['gender' => '_gender_']);

        return <<<EOT

EOT;
    }

    public function render()
    {
        Admin::script($this->script());
        return view('admin.tools.subimport');
    }
}