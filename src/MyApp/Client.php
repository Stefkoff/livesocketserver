<?php
/**
 * Created by PhpStorm.
 * User: stefkoff
 * Date: 2/11/16
 * Time: 7:08 PM
 */

namespace MyApp;

use Ratchet\ConnectionInterface;

class Client extends \SplObjectStorage {

    public $topic;

    public $id;

    /**
     * @var ConnectionInterface
     */
    private $connection;

    public function __construct(ConnectionInterface $conn){
        $this->connection = $conn;
        $this->id = md5(uniqid(mt_rand(), true));
    }

    public function isSameClient(ConnectionInterface $conn){
        if($conn === $this->connection){
            return true;
        }

        return false;
    }

    public function send($msg){
        $this->connection->send($msg);
    }

    public function close(){
        $this->connection->close();
    }

    public function getTopic(){
        return $this->topic;
    }

    public function setTopic($topic){
        $this->topic = $topic;
    }

    public function getConnection(){
        return $this->connection;
    }
}