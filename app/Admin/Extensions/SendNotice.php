<?php

namespace App\Admin\Extensions;

use Encore\Admin\Admin;

class SendNotice
{
    protected $id;
    public function __construct($id)
    {
        $this->id = $id;
    }
    protected function script()
    {
        return <<<SCRIPT
        $('.grid-notice-button').on('click', function () {
            // Your code.
            var id=$(this).data('id');
            $.ajax({
                method: 'get',
                url: '/admin/article/send/' + id,
                success: function (data) {
                    if(data.status){
                        $.pjax.reload('#pjax-container');
                    }
                    
                }
            });
        });

SCRIPT;
    }
    protected function render(){
        Admin::script($this->script());
        return "<a class='btn btn-xs btn-info  grid-notice-button' style='margin-right: 5px;' data-id='{$this->id}'>发送消息</a>";
    }
    public function __toString()
    {
        return $this->render();
    }
}
