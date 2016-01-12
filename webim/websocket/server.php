<?php
// 基础的验证DEMO
class Server
{
    private $serv;
    
    public function __construct()
    {
        $this->serv = new swoole_websocket_server('0.0.0.0', 9501);
        
        // $this->serv->set(array(
        //     'worker_num'  => 8,
        //     'daemonize'   => false, //是否作为守护进程,此配置一般配合log_file使用
            // 'max_request' => 1000,
            // 'log_file'    => './swoole.log',
        //     'task_worker_num' => 8
        // ));

        
        $this->serv->on('Open', function($server, $req) {
            echo "连接开始: " . $req->fd . "\n";
            file_put_contents( __DIR__ . '/log.txt' , $req->fd);
            var_dump($server->connections);
            $server->push($req->fd, "连接成功");
        });
         
        $this->serv->on('Message', function($server, $frame) {
            echo "发送消息: " . $frame->data . "\n";
            // $param = [
            //     'fd' => $frame->fd,
            //     'data' => $frame->data,
            // ];
            // $server->task(json_encode($param));
            $m = file_get_contents( __DIR__ .   '/log.txt');
            for ($i=1 ; $i<= $m ; $i++) {
                echo PHP_EOL . '发送给' . $i .  '  的数据是 '.$frame->data  . '  m = ' . $m;
                $server->push($i, $frame->data );
            }
            // $server->push($frame->fd, json_encode(["hello", "world"]));
        });

        // $this->serv->on("Task", [$this, 'OnTask']);
         
        $this->serv->on('Close', function($server, $fd) {
            echo "连接关闭: " . $fd . "\n";
        });

        // $this->serv->on("Finish", [$this, 'onFinish']);

        $this->serv->start();
    }

    public function OnTask($server, $task_id, $from_id, $data)
    {
        $frame = json_decode($data, true);
        var_dump($frame);
        // $server->push($frame->fd, json_encode(['data' => $frame->data]));
    }

    public function onFinish($server, $task_id, $data) {
        echo "Task {$task_id} finish\n";
        echo "Result: {$data}\n";
    }
}


$server = new Server();