<?php
    if (isset($_GET['game'])) {
        $game = $_GET['game'];
        $code = $_GET['code'];
        if (file_exists('../games/' . $game . '/g_' . $code . '.json')) {
            $groupdata = file_get_contents('../games/' . $game . '/g_' . $code . '.json');
            $groupdata = json_decode($groupdata);
            if ($groupdata->name == "" && count($groupdata->members) == 0) {
                $o = [
                    'success' => true
                ];
            } else {
                $o = [
                    'success' => false,
                    'alreadysetup' => true
                ];
            }
        } else {
            $o = [
                'success' => false,
                'error' => "Er is iets fout gegaan!"
            ];
        }
        echo json_encode($o);
    }
?>