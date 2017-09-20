<?php


namespace Library\Crawler\Download;


interface Downloader
{
        public function download(\Closure $callback = null);
        public function getContent() : string;
        public function setUrl(string $url);
        public function getUrlInfo(string $name = '');
}