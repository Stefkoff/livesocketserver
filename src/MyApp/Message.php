<?php
/**
 * Created by PhpStorm.
 * User: stefkoff
 * Date: 2/11/16
 * Time: 7:26 PM
 */

namespace MyApp;

class Message {

    const MESSAGE_TYPE_SUBSCRIBE = 'subscribe';
    const MESSAGE_TYPE_PUBLISH = 'publish';
    const MESSAGE_TYPE_COMMENT = 'comment';

    public $message;
    public $topic;
    public $type;

    protected $eventId;
    protected $userId;
    protected $userName;


    public $isValid;

    public function __construct($msg){
        $msg = trim($msg);
        $message = json_decode($msg, true);

        $this->isValid = false;

        if(is_array($message) && isset($message['type'])){
            $this->init($message);
        }
    }

    public function init($message){
        $this->type = $message['type'];

        switch($this->type){
            case self::MESSAGE_TYPE_SUBSCRIBE:
                if(isset($message['topic'])){
                    $this->topic = $message['topic'];
                    $this->isValid = true;
                }
                break;
            case self::MESSAGE_TYPE_PUBLISH:
                if(isset($message['topic']) && isset($message['message'])){
                    $this->topic = $message['topic'];
                    $this->message = $message['message'];
                    $this->isValid = true;
                }
                break;
            case self::MESSAGE_TYPE_COMMENT:
                if(isset($message['comment']) && isset($message['eventId']) && isset($message['userId']) && isset($message['userName'])){
                    $this->isValid = true;
                    $this->message = $message['comment'];
                    $this->eventId = $message['eventId'];
                    $this->userId = $message['userId'];
                    $this->userId = $message['userName'];
                }
                break;
            default:
                break;
        }
    }

    public function encodeCommentData(){
        return json_encode(array(
            'type' => self::MESSAGE_TYPE_COMMENT,
            'eventId' => $this->eventId,
            'userId' => $this->userId,
            'userName' => $this->userName,
            'message' => $this->message
        ));
    }

    public function validate(){
        return $this->isValid;
    }

}