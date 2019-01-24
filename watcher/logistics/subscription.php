<?php
    namespace TheRealKS\Watchdog\Logistics;

    require_once('data/poller.php');
    require_once('update.php');

    use TheRealKS\Watchdog\Data\Poller;

    require_once("../vendor/autoload.php");

    use Karriere\JsonDecoder\JsonDecoder;

    class SubscriptionData {
        public $type;
        public $modules;
        public $params;
    }

    class Subscription {

        private $jsondata;

        private $data;

        private $poller;
        private $gamemanager;
        private $groups = [];
        private $terminals = [];

        private $previousupdate;
        private $lastupdate;
        private $updateAvailable = false;
        private $numofupdates = 0;

        function __construct($jsondata) {
            $this->jsondata = $jsondata;
            $this->ParseJson();
            $this->setupPoller();
        }

        function ParseJson() {
            $dec = new JsonDecoder();
            $this->data = $dec->decode($this->jsondata, SubscriptionData::class);
            if (isset($this->data->params)) {
                if (!is_object($this->data->params)) {
                    $this->data->params = (object)$this->data->params;
                }
            }
        }

        function setupPoller() {
            $this->poller = new Poller($this->data->params->game, $this);
            switch ($this->data->type) {
                case 'statusbar':
                    //We will only need the files for the statusbar, the questiongroup file and the group file
                    $this->poller->addFile($this->data->params->qgroup . "/qgroup.json");
                    $this->poller->addFile("g_" . $this->data->params->group . ".json");
                    break;
                default:
                    # code...
                    break;
            }
        }

        function poll() {
            $res = $this->poller->poll();
            if (count($res) > 0) {
                $this->previousupdate = $this->lastupdate;
                $this->lastupdate = $this->createUpdate($res);
                $this->updateAvailable = true;
                $this->numofupdates++;
            } else {
                $this->updateAvailable = false;
            }
        }

        function createUpdate($updates) {
            $obj = [];
            foreach($updates as $u) {
                array_push($obj, $u);
            }
            return $obj;
        }

        function getUpdate() {
            return $this->lastupdate;
        }

        function hasUpdate() {
            return $this->updateAvailable;
        }

        function getUpdateNum() {
            return $this->numofupdates;
        }
    }
?>