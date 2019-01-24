<?php
    namespace TheRealKS\Watchdog\Logistics;

    require_once('datamanager.php');

    class GameManager extends DataManager {
        public $creator;
        public $id;
        public $grouptitle;
        public $image;
        public $imagelocation;
        public $maxterminals;
        public $creationtime;
        private $terminals;

        public function getTerminals() {
            $terminalarr = [];
            for ($i = 0; $i < count($this->terminals); $i++) {
                $terminal = new TerminalManager();
                foreach ($this->terminals[$i] as $key => $value) {
                    $terminal->updateField($key, $value);
                }
                array_push($terminalarr, $terminal);
            }
            return $terminalarr;
        }
    }
?>