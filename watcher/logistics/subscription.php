<?php
    namespace TheRealKS\Watchdog\Logistics;

    require_once('../data/poller.php');
    require_once('update.php');

    use TheRealKS\Watchdog\Data\Poller;

    require_once("../../vendor/autoload.php");

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

        private $previousupdate;

        function __construct($jsondata) {
            $this->jsondata = $jsondata;
        }

        function ParseJson() {
            $dec = new JsonDecoder();
            $this->data = $dec->decode($this->jsondata, SubscriptionData::class);
            if (isset($this->data->params)) {
                if (!is_object($this->data->params)) {
                    $this->data->params = (object)$this->data->params;
                }
            }
            //var_dump($this->data);
        }

        function setupPoller() {
            $this->poller = new Poller($this->data->params->game);
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
            $this->poller->poll();
        }

        function createUpdate($updates) {
            if ($this->data->type == 'statusbar') {
                $update;
                if (count($updates) > 1) {
                    //Both are updated
                    
                } else {
                    //Only one
                    if (str_pos($updates[0], 'qgroup')) {
                        //Qgroup
                    } else {
                        //Group

                    }
                }
            } else {

            }
        }
    }

    $d = new Subscription('{"type": "watcher", "modules": "*", "params": {"game": "5660"}}');
    $d->ParseJson();
?>