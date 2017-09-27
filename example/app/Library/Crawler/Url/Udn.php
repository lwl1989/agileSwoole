<?php


namespace Library\Crawler\Url;

use Kernel\Core;
use Kernel\Core\Cache\Redis;
use Kernel\Core\Cache\Type\Set;
use Model\CrawlerModel;

class Udn
{
        /* @var $urls Set */
        protected $urls;
        /* @var $got Set */
        protected $got;
        protected $db;
        protected $host;
        protected $dbName;
        protected $dbPrefix;
        const MAX_URL_LENGTH = 1000;
        const BOUNDARY_LENGTH = 500;
        public function __construct(string $host, string $dbName = '')
        {
                $core = Core::getInstant();
                /** @var CrawlerModel $db*/
                $db =  $core->get(CrawlerModel::class);
                $this->db = $db;
                $this->setHost($host);
                if(!empty($dbName)) {
                        $this->setDbName($dbName);
                }else{
                        $dbName = $host;
                }
                $this->urls = $this->getSet($dbName.':'.date('ymd').':urls');
                $this->got = $this->getSet($dbName.':'.date('ymd').':got');
                $this->clear();
        }

        public function setHost(string $host)
        {
                $this->host = $host;
                $domain = explode('.', $host);

                if(count($domain)>2) {
                        unset($domain[0]);
                }
                $this->dbName = implode('_', $domain);
                if(!empty($this->dbPrefix)) {
                        $this->dbName = $this->dbPrefix.'_'.$this->dbName;
                }

                $this->_fixDbName();

        }

        private function _fixDbName()
        {
                $this->dbName = str_replace('.','_', $this->dbName);
        }

        public function setDbName(string $name)
        {
                $this->dbName = $name;
        }

        public function addUrls(array $urls)
        {
              $got = $this->got->getAll();
              if(is_array($got)) {
	              $urls = array_diff($urls, $got);
              }

              if(!empty($urls) and self::BOUNDARY_LENGTH > $this->urls->getLength()) {
	              $this->urls->addValues($urls);
              }
        }

        public function getOne()
        {
                if($this->urls->getLength() < 1) {
                        return '';
                }
                $get = $this->urls->get();
                $this->got->addValue($get);
                return $get;
        }

        public function setContent(string $url, array $content)
        {
               $table = 'crawler.'.$this->dbName.'_'.date('ymd');
               $exists = $this->db->select('_id')->from($table)->where('url=?',[$url])->fetch(false);
               $content = array_merge(['url'=>$url], $content);
               if(!empty($exists)) {
                       $this->db->update($content)->from($table)->where('_id=?',[$exists['_id']])->execute();
               } else {
                       $this->db->insert(array_merge(['url' => $url], $content))->from($table)->execute();
               }
        }

        public function getGotLen()
        {
                return $this->got->getLength();
        }

        public function clear()
        {
                $this->got->del();
                $this->urls->del();
        }


        private function getSet(string $key)
        {
                $class = new Set(new Redis(Core::getInstant()->get('config'), false));
                $class->setKey($key);
                return $class;
        }

        public function __destruct()
        {
                $this->got = null;
                $this->urls = null;
        }

}