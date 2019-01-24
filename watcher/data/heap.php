<?php
    namespace TheRealKS\Watchdog\Data;

    class Heap {
        protected $instances = array();

        function __construct($instances = array()) {
            $this->instances = $instances;
        }

        function getUpdated() {
            $updates = array();
            for ($i = 0; $i < count($this->instances); $i++) {
                $instance = $this->instances[$i];
                if ($instance->isUpdated()) {
                    array_push($updates, $instance);
                }
            }
            return $updates;
        }
    }
?>