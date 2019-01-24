<?php
    namespace TheRealKS\Watchdog\Logistics;

    require_once('datamanager.php');

    class GroupManager extends DataManager {
        public $name;
        public $id;
        public $members;
        public $certificates;
        public $terminals;
        public $lastactive;
    }
?>