<?php
namespace MyApp;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Predis\Client as RedisClient;

class Server implements MessageComponentInterface {

	const SUBSCRIBE_TYPE_ACTIVE_USERS = 'activeUsers';

	/**
	 * @var Clients
	 */
	protected $clients;

	/**
	 * @var RedisClient
	 */
	protected $redisClient;

	public function __construct(){
		$this->clients = new Clients();
	}

	public function onOpen(ConnectionInterface $conn) {
		$this->clients->addClient($conn);
		echo "New connection! ({$conn->resourceId})\n";
		$this->publishActiveUsers();
	}

	public function publishActiveUsers(){
		$this->publish('activeUsers', json_encode([
			'topic' => self::SUBSCRIBE_TYPE_ACTIVE_USERS,
			'data' => $this->clients->getClientsNum()
		]));
	}

	public function publish($topic, $message){
		foreach($this->clients->getClients() as $key => $client){
			/**
			 * @var $client Client
			 */
			if($client->getTopic() === $topic){
				$client->send($message);
			}
		}
	}

	public function publishComment(Message $message){
		foreach($this->clients->getClients() as $key => $client){
			/**
			 * @var $client Client
			 */
			$client->send($message->encodeCommentData());
		}
	}

	public function onMessage(ConnectionInterface $from, $msg) {
		$message = new Message($msg);

		if($message->validate()){
			switch($message->type){
				case Message::MESSAGE_TYPE_SUBSCRIBE:
					$client = $this->clients->find($from);

					if($client !== false){
						$client->setTopic($message->topic);
					}

					if($message->topic == self::SUBSCRIBE_TYPE_ACTIVE_USERS){
						$this->publishActiveUsers();
					}

					break;
				case Message::MESSAGE_TYPE_PUBLISH:
					$this->publish($message->topic, $message->message);
					break;
				case Message::MESSAGE_TYPE_COMMENT:
					$this->publishComment($message);
					break;
				default:
					break;
			}
		}
    }

    public function onClose(ConnectionInterface $conn) {
		$this->clients->removeClient($conn);
		echo "Connection {$conn->resourceId} has disconect\n";
		$this->publishActiveUsers();
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
		echo "An error has occured: {$e->getMessage()}\n";
		$client = $this->clients->find($conn);
		$this->clients->removeClient($conn);
		$client->close();
		$this->publishActiveUsers();
    }
}
