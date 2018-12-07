<?php
    if (isset($_GET['game'])) {
        $game = $_GET['game'];
        $qgroup = $_GET['qgroup'];
        $question = $_GET['q'];

        $path = '../games/' . $game . '/' . $qgroup . '/qgroup.json';

        $data = file_get_contents($path);
        $data = json_decode($data);

        $questiondata = $data->questions->$question;

        echo json_encode($questiondata);
    }
?>