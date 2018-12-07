<?php
    $post_data = file_get_contents('php://input');
    if (isset($post_data)) {
        header('Content-Type: application/json');
        $data = json_decode($post_data)->data;
        $creationtype = $data->creationtype;
        switch ($creationtype) {
            case 'game':
                createGame($data);
                break;
            case 'questiongroup':
                createQGroup($data);
                break;
            case 'group':
                $fail = false;
                $ids = "";
                for ($i = 0; $i < $data->groupnum; $i++) {
                    $id = createGroup($data);
                    if ($id) {
                        $ids .= $id . ', ';
                    } else {
                        $fail = true;
                        break;
                    }
                }
                if (!$fail) {
                    sendBack('success', 'Succesfully created group(s): ' . $ids);
                } else {
                    sendBack('error', 'Error(s) occurred while trying to create groups');
                }
                break;
            case 'question':
                createQuestion($data);
                break;
            case 'terminal':
                createTerminal($data);
                break;    
            default:
                sendBack('error', 'Error: Unknown creation type');
                break;
        }
    }

    function createTerminal($data) {
        $terminaldata = [
            'text' => $data->idletext,
            'activated' => false,
            'inuse' => false,
            'question' => 0,
            'questiongroup' => 0,
            'group' => 0
        ];
        $gamebasepath = 'games/' . $data->game . "/game.json";
        $qgroupbasepath = 'games/' . $data->game . "/" . $data->questiongroup . '/qgroup.json';
        $id = substr(md5(mt_rand(0, 10) . mt_rand(0, 10)), 0, 2);
        if (file_exists($gamebasepath)) {
            if (file_exists($qgroupbasepath)) {
                $gamedata = file_get_contents($gamebasepath);
                $gamedata = json_decode($gamedata);
                $qgroupdata = file_get_contents($qgroupbasepath);
                $qgroupdata = json_decode($qgroupdata);

                $questions = $qgroupdata->questions;
                if (!is_array($questions)) {
                    $questions = (array)$questions;
                }

                if (!isset($questions[$data->questionnum])) {
                    sendBack('error', "The specified question does not exist");
                    return;
                }

                $gameterminals = $gamedata->terminals;
                if (!is_array($gameterminals)) {
                    $gameterminals = (array)$gameterminals;
                }

                $qgroupterminals = $questions[$data->questionnum]->terminals;
                if (!is_array($qgroupterminals)) {
                    $qgroupterminals = (array)$qgroupterminals;
                }

                if (array_search($id, $qgroupterminals)) {
                    return createTerminal($data);
                }

                for ($i = 0; $i < count($gameterminals); $i++) {
                    if ($gameterminals[$i]->id == $id) {
                        return createTerminal($data);
                    }
                }

                $terminaldata['id'] = $id;

                array_push($gameterminals, $terminaldata);
                array_push($qgroupterminals, $id);
                $gamedata->terminals = $gameterminals;
                $questions[$data->questionnum]->terminals = $qgroupterminals;

                file_put_contents($gamebasepath, json_encode($gamedata));
                file_put_contents($qgroupbasepath, json_encode($qgroupdata));

                sendBack('success', 'Succesfully created terminal ' . $id);
            } else {    
                sendBack('error', 'The specified questiongroup does not exist!');
            }
        } else {
            sendBack('error', 'The specified game does not exist!');
        }
    }

    function createQuestion($data) {
        $answers = explode(', ', $data->answers);
        $ranswers = explode(', ', $data->ranswers);
        $code = substr(md5($data->title), 0, 2);
        $questiondata = [
            'id' => $code,
            'title' => $data->title,
            'description' => $data->description,
            'qtype' => $data->qtype,
            'q_pswd' => $data->qcode,
            'image' => $data->image,
            'question' => $data->question,
            'answers' => $answers,
            'right_answers' => $ranswers,
            'points' => $data->points,
            'exptime' => $data->exptime,
            'useterminal' => $data->useterminal,
            'terminals' => []
        ]; 
        $basepath = 'games/' . $data->game . "/" . $data->questiongroup;
        if (file_exists($basepath . '/qgroup.json')) {
            $old = file_get_contents($basepath . '/qgroup.json');
            $old = json_decode($old);
            if (isset($old->questions)) {
                if (is_array($old->questions)) {
                    if (isset($old->questions[$code])) {
                        sendBack('error', 'The specified question already exists!');
                    }
                    $old->questions[$code] = $questiondata;
                } else {
                    if (isset($old->questions->$code)) {
                        sendBack('error', 'The specified question already exists!');
                    }
                    $old->questions->$code = $questiondata;
                }
            } else {
                $a = [$code => $questiondata];
                $old->questions = $a;
            }
            $new = json_encode($old);
            file_put_contents($basepath . '/qgroup.json', $new);
            sendBack('success', 'Succesfully created question: ' . $code);
        } else {
            sendBack('error', 'The specified quesitongroup or game does not exist!');
        }
    }

    function createGroup($data) {
        $groupdata = [
            'name' => '',
            'id' => 0,
            'members' => [],
            'certificates' => [],
            'terminals' => [],
            'lastactive' => time()
        ];
        $basepath = 'games/' . $data->game . '/g_';
        $id = substr(md5(mt_rand(0, 10) . mt_rand(0, 10)), 0, 2);
        $path = $basepath . $id . ".json";
        $created = false;
        while (!$created) {
            $path = $basepath . $id . ".json";
            if (file_exists($path)) {
                $id = substr(md5(mt_rand(0, 10) . mt_rand(0, 10)), 0, 2);
            } else {
                $created = true;
            }
        }
        $groupdata['id'] = $id;
        if (file_put_contents($path, json_encode($groupdata))) {
            return $id;
        } else {
            return false;
        }
    }

    function createQGroup($data) {
        $qgroupdata = [
            'name' => $data->name,
            'longname' => $data->longname,
            'description' => $data->description,
            'image' => $data->image,
            'imagelocation' => $data->imagelocation
        ];  
        $basepath = 'games/' . $data->game . '/';
        $suffix = '';
        $suffixcounter = 0;
        $created = false;
        $id = createQGroupID($data->name, $suffix);
        $idpath = $basepath . $id;
        while (!$created) {
            if (is_dir($idpath)) {
                $id = createQGroupID($data->name, $suffix);
                $idpath = $basepath . $id;
                $suffixcounter++;
                $suffix = strval($suffixcounter);
            } else {
                mkdir($idpath);
                $created = true;
            }
        }
        $qgroupdata['id'] = $id;
        if (file_put_contents($idpath . "/qgroup.json", json_encode($qgroupdata))) {
            $oldgamejson = file_get_contents($basepath . 'game.json');
            $oldgamejson = json_decode($oldgamejson);
            $oldgamejson = (array)$oldgamejson;
            array_push($oldgamejson['qgroups'], $idpath);
            file_put_contents($basepath . 'game.json', json_encode($oldgamejson));
            sendBack('success', 'Succesfully create questiongroup: ' . $idpath);
        } else {
            sendBack('error', 'An error occurred while trying to create the questiongroup');
        }
    }

    function createQGroupID($name, $suffix) {
        $words = explode(' ', $name);
        $groupcode = "";
        for ($i = 0; $i < count($words); $i++) {
            $groupcode .= $words[$i][0];
        }
        return $groupcode . $suffix;
    }

    function createGame($data) {
        $gamedata = [
            "creator" => $data->creatorname,
            "grouptitle" => $data->grouptitle,
            "image" => $data->image,
            "imagelocation" => $data->imagelocation,
            "maxterminals" => $data->maxterminals,
            "creationtime" => time(),
            "qgroups" => []
        ];
        $newgamecode = mt_rand(0,9) . mt_rand(0,9) . mt_rand(0,9) . mt_rand(0,9);
        if (file_exists('games')) {
            try {
                if (!file_exists('games/' . $newgamecode)) {
                    mkdir('games/' . $newgamecode);
                    $gamedata['id'] = $newgamecode;
                    file_put_contents('games/' . $newgamecode . '/game.json', json_encode($gamedata));
                    sendBack('success', 'Succesfully created game: ' . $newgamecode);
                } else {
                    return createGame();
                }
            } catch (Throwable $ex) {
                var_dump($ex);  
                sendBack('error', 'An error occured while trying to create the game');
            }
        } else {
            mkdir('games');
            return createGame();
        }
    }

    function sendBack($messagetype, $message) {
        $message = [
            'type' => $messagetype,
            'message' => $message
        ];
        sleep(1);
        echo json_encode($message);
    }
?> 