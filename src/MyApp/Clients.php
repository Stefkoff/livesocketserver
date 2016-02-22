<?php
/**
 * Created by PhpStorm.
 * User: stefkoff
 * Date: 2/11/16
 * Time: 7:05 PM
 */

namespace MyApp;

use Ratchet\ConnectionInterface;


class Clients{

    public $clients;

    private $redis;

    public function __construct()
    {
        $this->clients = array();
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
            }
        }
    }

    public function getClientsNum(){
        return count($this->clients);
    }

    public function log(){
        $result = [];
        foreach($this->clients as $key => $client){
            $result = array_merge($result, $client->log());
        }

        return $result;
    }

}