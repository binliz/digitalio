<?php

trait DbImplements
{
    public $dbService;
    public function setDb(ConnectionInterface $dbService){
        $this->dbService = $dbService;
    }
}
