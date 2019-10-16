<?php


namespace Kernel\Swoole\Event\Http;

use Kernel\AgileCore as Core;
use Kernel\Core\View\View;
use Kernel\Swoole\Event\Event;
use Kernel\Swoole\Event\EventTrait;
use Kernel\Swoole\SwooleHttpServer;
use Swoole\Http\Response;
use Swoole\Http\Request as SRequest;

class Request implements Event
{
    use EventTrait;
    /* @var  \swoole_http_server $server */

    protected $server;
    protected $request;
    protected $response;

    public function __construct( $server)
    {
        $this->server = $server;
    }

    /**
     * @param SRequest $request
     * @param Response $response
     */
    public function doEvent(SRequest $request, Response $response)
    {
        $request_uri = $request->server['request_uri'];
        if ($request_uri === '/favicon.ico' || strpos($request_uri, '/static/') === 0) {
            // 处理静态文件
            Statics::exec($request_uri, $request, $response);
        } else {
            // 动态文件交由Yaf处理
            if(SwooleHttpServer::getAppType() === 'yaf') {
               $this->yaf($request, $response, $request_uri);
            }else{
                $this->normal($request, $response);
            }

        }

    }

    /**
     * yaf actions
     * @param SRequest $request
     * @param Response $response
     * @param string   $request_uri
     */
    protected function yaf(SRequest $request, Response $response, string $request_uri)
    {
        $application = SwooleHttpServer::getApplication();
        try {
            $yaf_request = new \Yaf_Request_Http($request_uri);
            $yaf_request->setParam('request', $request);
            $yaf_request->setParam('response', $response);
            $application->getDispatcher()->dispatch($yaf_request);
        } catch (\Exception $exception) {
            $data = ['code' => $exception->getCode() > 0 ? $exception->getCode() : 1, 'response' => $exception->getMessage()];
            $response->end(json_encode($data));
        }
    }

    /**
     * normal actions
     * @param SRequest $request
     * @param Response $response
     */
    protected function normal(SRequest $request, Response $response)
    {
        if (isset($request->server['request_uri']) and $request->server['request_uri'] == '/favicon.ico') {
            $response->end(json_encode(['code' => 0]));
            return;
        }
        $rawData = json_decode($request->rawContent(), true);
        $post = [];
        if (is_string($request->post)) {
            parse_str($request->post, $post);
        } else if (is_array($request->post)) {
            $post = $request->post;
        }
        if (is_array($rawData)) {
            if (!is_array($post)) {
                $post = [];
            }
            $_POST = array_merge($post, $rawData);
        } else {
            $_POST = $post;
        }

        $this->dispatch($request, $response);
    }

    //dispatch router to response
    public function dispatch(SRequest $request, Response $response)
    {
        try {

            $data = ['code' => 0, 'response' => Core::getInstance()->get('route')->dispatch(
                $request->server['request_uri'], strtolower($request->server['request_method'])
            )];
            if(is_array($data['response']) and isset($data['response']['code'])) {
                $data['code'] = $data['response']['code'];
                unset($data['response']['code']);
            }
        } catch (\Exception $exception) {
            //code is Exception code
            $data = ['code' => $exception->getCode() > 0 ? $exception->getCode() : 1, 'response' => $exception->getMessage()];
        }

        if($data['response'] instanceof View) {
            $content = $data['response']->display();
            unset($data['response']);
        }else if (isset($data['response']['view'])) {
            extract($data['response']);
            ob_start();
            include($data['response']['view']); // PHP will be processed
            $content = ob_get_contents();
            ob_end_clean();
        } else {
            $content = json_encode($data);
        }
        $response->end($content);
    }

    //call back if exists
    public function doClosure() : array
    {
        if ($this->callback != null) {
            $this->params = [$this->request, $this->response];
            return call_user_func_array($this->callback, $this->params);
        }
        return ['code' => 0];
    }
}