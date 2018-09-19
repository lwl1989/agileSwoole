<?php
/**
 * Created by weibo.com
 * User: wenlong11
 * Date: 2018/9/19
 * Time: 上午11:08
 */

namespace Kernel\Swoole\Event\Http;


use Kernel\Core\Mime\Response;

class Statics {

    /**
     * 执行处理一个静态文件
     *
     * @param string $request_uri
     * @param \Swoole\Http\Request $request
     * @param \Swoole\Http\Response $response
     */
    public static function exec($request_uri, \Swoole\Http\Request $request, \Swoole\Http\Response $response) {

        $filename = APP_PATH . "/public{$request_uri}";
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        // 发送Mime
        $comm_response = new \Comm\Response($response);
        $content_type = $comm_response->contentType($extension);
        if ($content_type) {
            $response->header('Content-Type', $content_type);
        }

        $config = \Comm\Config::load('statics');
        if (isset($config[$extension])) {
            $extension_config = array_merge($config['_default_'], $config[$extension]);
        } else {
            $extension_config = $config['_default_'];
        }

        // 浏览器缓存
//        if (!empty($extension_config['expire']) && \Comm\Misc::isProEnv()) {
//            $response->header('Cache-control', "max-age={$extension_config['expire']}");
//        }

        // 内容压缩（旧版本GZIP不存在，不能压缩）
        //!empty($extension_config['compress']) &&
        if (method_exists($response, 'gzip')) {
            $response->gzip(1);
        }

        if ($extension === 'js') {
            // JS需要合并
            self::_sendfile($filename, $response, function($content) use ($request, $response) {
                // 合并JS
                $callbacks = new \Comm\Async\Callbacks($request, $response, true);
                preg_match_all('#\$Import\("([^"]+)"\);?\r?\n#i', $content, $regex);

                foreach ($regex[1] as $key => $value) {
                    $import_filename = ROOTPATH . "/public/static/{$value}.js";
                    if (is_file($import_filename)) {
                        \Swoole\Async::readFile($import_filename, $callbacks->push($key));
                    }
                }

                $callbacks->exec(function($datas) use ($content, $regex, $response) {
                    $searchs = $replaces = array();
                    foreach ($regex[0] as $key => $value) {
                        if (isset($datas[$key][1])) {
                            $replace = $datas[$key][1];
                        } else {
                            $replace = "alert('{$regex[1][$key]} not found');";
                        }
                        $searchs[] = $value;
                        $replaces[] = $replace . "\n";
                    }
                    $content = str_replace($searchs, $replaces, $content);

                    // 压缩JS
//                    if (\Comm\Misc::isProEnv()) {
//                        $content = \Comm\Jsmin::minify($content);
//                    }
                    $response->end($content);

                });
            });
        } else {
            self::_sendfile($filename, $response);
        }
    }

    /**
     * 发送静态文件内容
     *
     * @param string                $filename 文件内容
     * @param \Swoole\Http\Response $response Swoole响应
     * @param callable              $callback 自定义回调返回内容
     */
    protected static function _sendfile($filename, $response, callable $callback = null) {
        if (is_file($filename)) {
            \Swoole\Async::readFile($filename, function($filename, $content) use ($response, $callback) {
                if ($callback && is_callable($callback)) {
                    $content = call_user_func($callback, $content);
                }
                $response->end($content);
            });
        } else {
            // 页面没找到
            $response->status(404);
            $comm_response = new Response($response);
            $response->header('Content-Type', $comm_response->contentType('html'));
            $response->end('<h1>404 NOT FOUND</h1>');
        }
    }
}