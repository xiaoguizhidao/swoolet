<?php

if (!$env = &$argv[1]) {
    echo 'Please input ENV' . PHP_EOL;
    return;
}

include \dirname(__DIR__) . '/Swoolet/App.php';

class Server extends \Swoolet\Http
{
    static public $msg;
    /**
     * @var \Live\Lib\Conn
     */
    static public $conn;

    public function onWorkerStart($sw, $worker_id)
    {
        Server::$conn = new \Live\Lib\Conn();

        parent::onWorkerStart($sw, $worker_id);
    }

    public function onRequest($request, $response)
    {
        $this->resp = $response;

        $_POST = isset($request->post) ? $request->post : array();

        $this->callRequest($request->server['path_info'], $request);

        $this->response($request->fd, self::$msg);
    }
}

$app = Server::createServer('Live', $env);

$app->run(':8090');