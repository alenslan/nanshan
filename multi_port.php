<?php
// 多端口监听服务

class Server
{
    private $serv;

    public function __construct()
    {
        $this->serv = new swoole_server('127.0.0.1', 9501);
        $this->serv->on('Start', [$this, 'onStart']);
        $this->serv->on('Connect', [$this, 'onConnect']);
        $this->serv->on('Close', [$this, 'onClose']);
        $this->serv->on('Receive', [$this, 'onReceive']);
        // 添加监听
        $this->serv->addlistener('127.0.0.1', 9502, SWOOLE_TCP);
        // 开启服务
        $this->serv->start();
    }

    public function _set()
    {
        $this->serv->set([
            'worker_num' => 8,
            'daemonize' => false,
            'max_request' => 10000,
            'dispatch_mode' => 2,
            'debug_mode'=> 1,
        ]);
    }

    public function onStart( $serv ) {
        echo "Start\n";
    }
    public function onConnect( $serv, $fd, $from_id ) {
        echo "Client {$fd} connect\n";
    }

    public function onClose( $serv, $fd, $from_id ) {
        echo "Client {$fd} close connection\n";
    }

    public function onReceive(swoole_server $serv, $fd, $from_id, $data)
    {
        $info = $serv->connection_info($fd, $from_id);
        if ($info['server_port'] == 9502) {
            $serv->send($fd, "welcom Admin \n");
        } else {
            $serv->send($fd, 'Swoole:' . $data);
        }
    }
}

new Server();