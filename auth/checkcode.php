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
                    'error' => "Die groep is al in gebruik!"
                ];
            }
        } else {
            $o = [
                'success' => false
            ];
        }
        echo json_encode($o);
    }
?>