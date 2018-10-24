<?php
    
    class Data {
        public $gpath;
        public $qpath;
        public $questionsanswered;
        public $numberofquestions;
        public $updaterequired = false;

        function __construct($gpath, $qpath) {
            $this->gpath = $gpath;
            $this->qpath = $qpath;
            $this->resolve();
        }   

        function resolve() {
            //to get num of questions
            $qs = scandir($this->qpath);
            $qs = count($qs);
            $this->numberofquestions = $qs - 2;
            $this->poll();
        }

        function poll() {
            $data = json_decode(file_get_contents($this->gpath));
            $certs = $data->certificates;
            $newnum = 1;
            if (gettype($certs) == "array") {
                $newnum = count($certs);
            }
            $this->updaterequired = $newnum != $this->questionsanswered;
            $this->questionsanswered = $newnum;
        }
    }

    class GameData {
        public $path;
        public $id;
        public $groups = array();
        public $questiongroups = array();

        function __construct($id, $path) {
            $this->id = $path;
            $this->path = $path;
            $this->resolve();
        }

        function resolve() {
            $questions = scandir($this->path . '/questions');
            foreach ($questions as $question) {
                if ($question != "." && $question != "..") {
                    $qs = scandir($this->path . '/questions//' . $question);
                    $questiongroup = new QuestionGroupData($question, $qs - 2);
                    array_push($this->questiongroups, $questiongroup);
                }
            }
            $groups = scandir($game->path . "/group");
            foreach ($groups as $group) {
                if ($group != "." && $group != "..") {
                   $g = new GroupData($game->path . "/group//" . $group); 
                   array_push($this->groups, $g);
                }
            }
        }
    }

    class GroupData {
        public $path;
        public $questionsanswered;

        function __construct($path) {
            $this->path= $path;
            $this->resolve();
        }

        function resolve() {    
            $data = json_decode(file_get_contents($this->path));
            $certs = $data->certificates;
            $this->questionsanswered = count($certs);
        }
    }

    class QuestionGroupData {
        public $id;
        public $numberofquestions;

        function __construct($id, $noq) {
            $this->id = $id;
            $this->numberofquestions = $noq;
        }
    }   

?>