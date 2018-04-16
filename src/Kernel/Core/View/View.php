<?php


namespace Kernel\Core\View;


use Kernel\AgileCore;
use Kernel\Core\Exception\ErrorCode;

class View
{
    protected $path;
    protected $data;

    /**
     * View constructor.
     * @param string $path
     * @param array $data
     * @throws \Exception
     */
    public function __construct(string $path, array $data)
    {
        if(!is_file($path)) {
            $config = AgileCore::getInstant()->get('config')->get('views');
            $path = $config['path'].ltrim($path,'/');
            if(!is_file($path)) {
                throw new \Exception($path.' not found', ErrorCode::FILE_NOT_FOUND);
            }
        }
        $this->path = $path;
        $this->data = $data;
    }

    public function display() : string
    {
        extract($this->data);
        ob_start();
        include($this->path);
        $content = ob_get_contents();
        @ob_end_clean();
        return $content;
    }

    public static function render(string $path, array $data = [])
    {
        return new static($path,$data);
    }
}