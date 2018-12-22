<?php
    namespace TheRealKS\Watchdog\Data;

    require_once("structures.php");

    use TheRealKS\Watchdog\Data;

    require_once("../../vendor/autoload.php");

    use Karriere\JsonDecoder\JsonDecoder;

    class PollResult {
        public $group;
        public $qgroup;
        public $game;
    }

    class Poller {
        private $path = "../../games/";

        private $files = [];
        private $previoushashes = [];

        function __construct($game) {
            $this->path .= $game . "/";
        }

        function addFile($file) {
            array_push($this->files, $path . $file);
            $hash = sha1_file($path . $file);
            $this->previoushashes[$path . $file] = $hash;
        }

        function poll() {
            $result = [];
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
                    }
                } else {
                    //Wait till the next round
                    $hash = sha1_file($file);
                    $this->previoushashes[$file] = $hash;
                }
            }
            return $result;
        }

        function determineClass($file) {
            if (str_pos($file, 'qgroup') !== false) {
                return Data\QGroup::class;
            } else if (str_pos($file, 'g_') !== false) {
                return Data\Group::class;
            } else if (str_pos($file, 'game') !== false) {
                return Data\Game::class;
            }
            return NULL;
        }
    }
?>