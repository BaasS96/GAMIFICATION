<?php
    namespace TheRealKS\Watchdog\Data;

    require_once("logistics/datamanager.php");
    require_once("logistics/gamemanager.php");
    require_once("logistics/groupmanager.php");
    require_once("logistics/terminalmanager.php");

    use TheRealKS\Watchdog\Logistics;

    require_once("../vendor/autoload.php");

    use Karriere\JsonDecoder\JsonDecoder;

    class PollResult {
        public $group;
        public $qgroup;
        public $game;
    }

    class Poller {
        private $subscription;

        private $path = "../games/";

        private $files = [];
        private $previoushashes = [];

        function __construct($game, $subscription) {
            $this->path .= $game . "/";
            $this->subscription = $subscription;
        }

        function addFile($file) {
            array_push($this->files, $this->path . $file);
            $hash = sha1_file($this->path . $file);
            $this->previoushashes[$this->path . $file] = $hash;
        }

        function poll() {
            $update = [];

            $result = [];
            for ($i = 0; $i < count($this->files); $i++) {
                $file = $this->files[$i];
                if (isset($this->previoushashes[$file])) {
                    $newhash = sha1_file($file);
                    if ($this->previoushashes[$file] != $newhash) {
                        $this->previoushashes[$file] = $newhash;
                        //Update required
                        $f = file_get_contents($file);
                        $dec = new JsonDecoder();
                        $result[$file] = $dec->decode($f, $this->determineClass($file));
                        if (is_a($result[$file], 'GameManager')) {
                            //Also update terminal?
                            $terminals = $result[$file]->getTerminals();
                            for ($i = 0; $i < count($terminals); $i++) {
                                if (!$terminals[$i]->compareHash($this->subscription->terminals[$i]->getHash())) {
                                    array_push($update, $terminals[$i]);
                                }
                            }
                        } else {
                            array_push($update, $result[$file]);
                        }
                    }
                } else {
                    //Wait till the next round
                    $hash = sha1_file($file);
                    $this->previoushashes[$file] = $hash;
                }
            }

            return $update;
        }

        function determineClass($file) {
            if (strpos($file, 'g_') !== FALSE) {
                return Logistics\GroupManager::class;
            } else if (strpos($file, 'game') !== FALSE) {
                return Data\GameManager::class;
            }
            return NULL;
        }
    }
?>