<?php
    namespace TheRealKS\Watchdog\Logistics;

    class DataManager {
        private $currenthash;
        private $datatype;
        private $updated = false;

        public function compareHashes($hash) {
            return $hash == $this->currenthash;
        } 

        public function isUpdated() {
            return $this->updated;
        }

        public function updateHash($newhash = "") {
            if ($newhash = "") {
                $this->updateHash(sha1($this));
            } else {
                $this->currenthash = $newhash;
            }
        }

        public function updateField($name, $newvalue) {
            $this->$$name = $newvalue;
        }
    }
?>