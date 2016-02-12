<?php
/**
 * Created by PhpStorm.
 * User: stefkoff
 * Date: 2/11/16
 * Time: 7:05 PM
 */

namespace MyApp;

use Ratchet\ConnectionInterface;
use Predis\Client as PredisClient;


class Clients{

    public $clients;

    private $redis;

    public function __construct()
    {
        $this->clients = array();

        $this->redis = new PredisClient([
            'scheme' => 'tcp',
            'host' => 'localhost',
            'port' => '6379'
        ]);
    }

    public function find(ConnectionInterface $conn){
        foreach($this->clients as $client){
            /**
             * @var $client Client
             */
            if($client->isSameClient($conn)){
                return $client;
            }
        }

        return false;
    }

    /**
     * Adding new Client to the collection
     *
     * @param ConnectionInterface $conn
     */
    public function addClient(ConnectionInterface $conn){
        $newClient = new Client($conn);
        $this->clients[] = $newClient;
        $this->redis->lpush('users', $newClient->id);
    }

    public function getClients(){
        return $this->clients;
    }

    public function removeClient($conn){
        foreach($this->clients as $key => $client){
            /**
             * @var $client Client
             */

            if($client->isSameClient($conn)){
                unset($this->clients[$key]);
                $this->redis->lRem('users', 1, $client->id);
                break;
            }
        }
    }

    public function getClientsNum(){
        return count($this->clients);
    }

}