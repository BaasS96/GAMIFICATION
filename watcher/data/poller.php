<?php
    namespace TheRealKS\Watchdog\Data;

    require_once("../logistics/datamanager.php");
    require_once("../logistics/gamemanager.php");
    require_once("../logistics/groupmanager.php");
    require_once("../logistics/terminalmanager.php");

    use TheRealKS\Watchdog\Logistics;

    require_once("../../vendor/autoload.php");

    use Karriere\JsonDecoder\JsonDecoder;

    class PollResult {
        public $group;
        public $qgroup;
        public $game;
    }

    class Poller {
        private $subscription;

        private $path = "../../games/";

        private $files = [];
        private $previoushashes = [];

        function __construct($game, $subscription) {
            $this->path .= $game . "/";
            $this->subscription = $subscription;
        }

        function addFile($file) {
            array_push($this->files, $path . $file);
            $hash = sha1_file($path . $file);
            $this->previoushashes[$path . $file] = $hash;
        }

        function poll() {
            for ($i = 0; $i < count($files); $i++) {
                $file = $this->files[$i];
                if (isset($this->previoushashes[$file])) {
                    $newhash = sha1_file($file);
                    if ($this->previoushashes[$file] != $newhash) {
                        $this->previoushashes[$file] = $newhash;
                        //Update required
                        $f = file_get_contents($file);
                        $dec = new JsonDecoder();
                        $result[$file] = $dec->decode($f, determineClass($file));
                        if (str_post($file, 'game') !== false) {
                            //Also update terminal?
                            $result[$file]
                        }
                    }
                } else {
                    //Wait till the next round
                    $hash = sha1_file($file);
                    $this->previoushashes[$file] = $hash;
                }
            }
            return $result;
        }

        function initialize() {

        }

        function determineClass($file) {
            if (str_pos($file, 'g_') !== false) {
                return Logistics\GroupManager::class;
            } else if (str_pos($file, 'game') !== false) {
                return Data\GameManager::class;
            }
            return NULL;
        }
    }
?>