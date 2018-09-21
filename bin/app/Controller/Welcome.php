<?php

namespace Controller;


use Model\User;

class Welcome
{
        public function index()
        {
            $this->userInsert('龙龙');
            $this->userInsert('萌萌');
                return [
                        'code'  =>      0,
                        'view'  =>      realpath(__DIR__.'/../View/index.php')
                ];
        }

        public function userInsert(string $name)
        {
                $user = new User();
                $id = $user->insert(['name'=>$name])->execute();
                return ['id'=>$id];
        }
}