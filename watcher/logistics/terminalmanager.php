<?php
    namespace TheRealKS\Watchdog\Logistics;

    require_once('datamanager.php');

    class TerminalManager extends DataManager {
        public $text;
        public $questiongroup;
        public $question;
        public $group;
        public $activated;
        public $inuse;
        public $id;
    }
?>