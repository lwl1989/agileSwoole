<?php


namespace Library\Crawler\Download;


use Library\Exception\DataException;

trait DownloadTrait
{
        protected $url;
        protected $urlInfo;

        public function setUrl(string $url)
        {
                $this->url = $url;
                return $this;
        }

        public function getUrl() : string
        {
                if(empty($this->url)) {
                        throw new DataException('set Url before this action');
                }

                return $this->url;
        }

        public function getUrlInfo(string $name = '')
        {
                if(empty($this->url)) {
                        throw new DataException('set Url before this action');
                }

                if(empty($this->urlInfo)) {
                        $this->urlInfo = parse_url($this->url);
                }

                if('' != $name) {
                        return $this->urlInfo[$name] ?? '';
                }
                return $this->urlInfo;
        }
}