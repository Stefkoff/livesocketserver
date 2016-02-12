<?php
/**
 * Created by PhpStorm.
 * User: stefkoff
 * Date: 2/11/16
 * Time: 10:38 PM
 */

namespace MyApp;


class MessageResponse {

    const EVENT_TYPE_NOTIFICATION = 'event.notification';


    public $type;
    public $data;
    public $event;

    public function __construct($type, $event, $data){
        $this->type = $type;
        $this->data = $data;
    }


}