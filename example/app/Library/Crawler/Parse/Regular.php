<?php


namespace Library\Crawler\Parse;


class Regular
{
        public static function getMeta(string $data, string $url)
        {
                if(empty($data)) {
                        return;
                }
                $meta = [];
                preg_match('/<TITLE>([\w\W]*?)<\/TITLE>/si', $data, $matches);
                if (!empty($matches[1])) {
                        $meta['title'] = $matches[1];
                }
                if(preg_match("/<[meta|META].+?[name|NAME]=['|\"].+?([\w\W]*?)['|\"]/si", $data)) {
                        preg_match_all("/<[meta|META].+?[name|NAME]=['|\"]([\w\W]*?)['|\"].+?[content|CONTENT]=['|\"]([\w\W]*?)['|\"]/si", $data, $matches);
                        if(isset($matches[1]) and isset($matches[2])) {
                                $keys   = $matches[1];
                                $values = $matches[2];
                                $count = count($keys);
                                if($count == count($values)) {
                                        for ($i=0;$i<$count;$i++) {
                                                $meta[$keys[$i]] = $values[$i];
                                        }
                                }
                        }
                }

                if(preg_match("/<[meta|META].+?[property|Property]=['|\"].+?([\w\W]*?)['|\"]/si", $data)) {
                        preg_match_all("/<[meta|META].+?[property|Property]=['|\"]([\w\W]*?)['|\"].+?[content|CONTENT]=['|\"]([\w\W]*?)['|\"]/si", $data, $matches);
                        if(isset($matches[1]) and isset($matches[2])) {
                                $keys   = $matches[1];
                                $values = $matches[2];
                                $count = count($keys);
                                if($count == count($values)) {
                                        for ($i=0;$i<$count;$i++) {
                                                $key = $keys[$i];
                                                if(strpos($key,':')!==false){
                                                        $arr = explode(':', $key);
                                                        if(count($arr)==2) {
                                                                $meta[$arr[1]] = $values[$i];
                                                        }
                                                }
                                        }
                                }
                        }
                }
                $meta = [
                        'title'         =>      $meta['title'] ?? '',
                        'description'   =>      $meta['description'] ?? '',
                        'image'         =>      $meta['image'] ?? self::getImage($data),
                        'author'        =>      $meta['author'] ?? '',
                        'site_name'     =>      $meta['site_name'] ?? '',
                        'url'           =>      $meta['url'] ?? $url
                ];
                return $meta;
        }

        public static function getUrls(string $data, string $scheme = '', string $host = '', string $path = '')
        {
                $pattern = '/<a\b[^>]+\bhref="([^"]*)"[^>]*>([\s\S]*?)<\/a>/';
                //       $pattern = '#(http|ftp|https)://?([a-z0-9_-]+\.)+(com|net|cn|org){1}(\/[a-z0-9_-]+)*\.?(?!:jpg|jpeg|gif|png|bmp)(?:")#i';
                preg_match_all($pattern, $data, $matched);
                $urls = [];
                foreach ($matched[1] as $url) {
                        $url = self::getUrl($url, $scheme, $host);
                        $urls[] = $url;

                }
                $uri = [];
                $path = str_replace(['*','/'],['([\s\S]*?)','\/'], $path);
                $pathPattern = '/'.$path.'/';
                foreach ($urls as $url) {
                        if(preg_match($pathPattern, $url)) {
                                $uri[] = $url;
                        }
                }
                return $uri;
        }

        public static function getImage(string $data)
        {
                $pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/";
                preg_match_all($pattern,$data,$matches);
                return isset($matches[1][0]) ? $matches[1][0] : '';
        }

        public static function getUrl(string $url, string $scheme = '', string $host = '')
        {
                if($url == '') {
                        return '';
                }
                if($url[0] == '#')
                {
                        return '';
                }
                $except = [
                        'javascript:',
                        '(',
                        ')'
                ];
                foreach ($except as $value) {
                        if(strpos($url, $value) !== false) {
                                return '';
                        }
                }
                $urlInfo = parse_url($url);

                if(!isset($urlInfo['scheme'])) {
                        $urlInfo['scheme'] =  $scheme;
                }
                if(!isset($urlInfo['host'])) {
                        $urlInfo['host'] = $host;
                }else{
                        if($urlInfo['host']!=$host) {
                                return '';
                        }
                }

                if(!isset($urlInfo['path'])) {
                        $urlInfo['path'] = '/';
                }


                $url = $urlInfo['scheme'].'://'.trim($urlInfo['host'],'/ ').'/'.trim($urlInfo['path'], '/ ') ;
                if(isset($urlInfo['query'])) {
                        $url .= '?'.$urlInfo['query'];
                }

                $url = str_replace('&amp;', '&', $url);

                return $url;
        }
}