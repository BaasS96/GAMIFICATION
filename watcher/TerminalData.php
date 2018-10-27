<?php
    include('Data.php');

    class TerminalData extends Data { 
        
        protected $terminalid;
        protected $inuse;
        protected $activated;
        protected $timeleft;
        protected $questiongroup;
        protected $question;

        function __construct($type, $path, $gamepin, $group, $terminalid) {
            parent::__construct($type, $path, $gamepin, $group);
            $this->terminalid = $terminalid;
        }

        function poll() {
            $content = file_get_contents($this->path);
            $json = json_decode($content);
            $this->inuse = $json->inuse;
            $this->activated = $json->activated;
            $this->questiongroup = $json->certificatenumber;
            $this->question = $json->qcode;
            if (isset($json->exptime)) {
                $this->timeleft = $json->exptime - time();
            }
        }

        function getTerminalID() {
            return $this->terminalid;
        }

        function getUseStatus() {
            return $this->inuse;
        }

        function getActivationStatus() {
            return $this->activated;
        }

        function getTimeLeft() {
            return $this->timeleft;
        }

        function getQGroup() {
            return $this->question;
        }

        function getQCode() {
            return $this->question;
        }
    }
?>