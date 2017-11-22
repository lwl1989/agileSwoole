<?php

namespace Controller;


class Process
{
        public function before()
        {
                echo 'this process before';
        }

        public function run()
        {
                $i = 0;
                swoole_timer_after(10000,function (){
                        echo 'sync over';
                });
        }

        public function after()
        {
                echo 'this process after';
        }
}