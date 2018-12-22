<?php
    namespace TheRealKS\Watchdog\Data;

    class Game {
        public $creator;
        public $id;
        public $grouptitle;
        public $image;
        public $imagelocation;
        public $maxterminals;
        public $creationtime;
        public $terminals;
    }

    class Terminal {
        public $text;
        public $questiongroup;
        public $question;
        public $group;
        public $activated;
        public $inuse;
        public $id;
    }

    class Group {
        public $name;
        public $id;
        public $members;
        public $certificates;
        public $terminals;
        public $lastactive;
    }

    class QGroup {
        public $name;
        public $longname;
        public $id;
        public $description;
        public $image;
        public $imagelocation;
        public $questions;
        public $imgurl;
    }

    class Question {
        public $id;
        public $title;
        public $description;
        public $qtype;
        public $q_pswd;
        public $image;
        public $question;
        public $answers;
        public $right_answers;
        public $points;
        public $exptime;
        public $useterminal;
        public $terminals;
    }
?>