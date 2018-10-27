<?php

class Type extends SplEnum {
    const FILE = ".json";
    const FOLDER = "/";
}

class Data {
    
    protected $type;
    protected $path;
    protected $gamepin;
    protected $group;

    protected $data;

    protected $updateavailable = false;

    function __construct($type, $path, $gamepin, $group) {
        $this->type = $type;
        $this->path = $path;
        $this->gamepin = $gamepin;
        $this->$group;
        $this->poll();
    }

    protected function poll() {
        $newcontents = file_get_contents($this->path);
        $updateavailable = $newcontents !== $this->data;
        $this->data = $newcontents;
    }

    function getGroup() {
        return $this->group;
    }

    function getGamePin() {
        return $this->gamepin;
    }
}

?>