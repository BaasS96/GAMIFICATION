<?php
    namespace TheRealKS\Watchdog\Logistics;

    class Update implements \JsonSerializable {
        function jsonSerialize() {
            return [];
        }
    }

    class StatusBarUpdate extends Update implements \JsonSerializable {
        private $qa;
        private $tq;

        function __construct($qa, $tq) {
            $this->qa = $qa;
            $this->tq = $tq;
        }

        function jsonSerialize() {
            return [
                "questionsanswered" => $this->qa,
                "totalquestions" => $this->tq
            ];
        }
    }
?>