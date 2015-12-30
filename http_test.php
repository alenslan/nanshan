<?php

class HttpServer 
{
    private $serv;
    public function __construct() 
    {
        $this->serv = new swoole_http_server("127.0.0.1", 9502);

        $this->serv->on('Request', [$this, 'onRequest']);
        $this->serv->start();
    }

    public function onRequest($request, $response)
    {
        var_dump($request);
        // var_dump($request->post);
        
        $response->cookie("User", "Swoole");
        $response->header("X-Server", "Swoole");
        $response->end("<h1>Hello Swoole!</h1>");
    }
}

class WebSocketServer
{
    private $serv;
    public function __construct()
    {
        $this->serv = new swoole_websocket_server('127.0.0.1', 9503);
        $this->serv->on('Open', function($server, $req) {
            echo "connection open: ".$req->fd;
        });
         
        $this->serv->on('Message', function($server, $frame) {
            echo "message: ".$frame->data;
            $server->push($frame->fd, json_encode(["hello", "world"]));
        });
         
        $this->serv->on('Close', function($server, $fd) {
            echo "connection close: ".$fd;
        });

        $this->serv->start();
    }
}

// $server = new HttpServer();
$server = new WebSocketServer();