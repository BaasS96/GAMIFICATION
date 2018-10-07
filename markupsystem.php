<?php
function musdecodeimg($input) {
    if (is_array($input)) {
        $url = htmlspecialchars_decode($input[1]);
        $input = '<img src="' . $url . '" alt="' . $input[2] . '" title="' . $input[3] . '" class="u_image" />';
    }
    return $input;
}

function musdecodeanchor($input) {
    if (is_array($input)) {
        $url = htmlspecialchars_decode($input[1]);
        $input = '<a href="' . $url . '" class="u_anchor" title="' . $input[2] . '" target="_blank" />' . $input[3] . '</a>';
    }
    return $input;
}

function musdecode($input) {
    //PARAGRAPH
    $input = str_ireplace("[p]","<p>",$input);
    $input = str_ireplace("[/p]","</p>",$input);
    //BOLD
    $input = str_ireplace("[b]","<b>",$input);
    $input = str_ireplace("[/b]","</b>",$input);
    //ITALICS
    $input = str_ireplace("[i]","<i>",$input);
    $input = str_ireplace("[/i]","</i>",$input);
    //UNDERLINE
    $input = str_ireplace("[u]","<u>",$input);
    $input = str_ireplace("[/u]","</u>",$input);
    //NEW LINE
    $input = str_ireplace("\r\n","<br />",$input);
    //image
    $input = preg_replace_callback('/\[IMG\((.+?)\)\((.+?)\)\((.+?)\)\]/im', 'musdecodeimg', $input);
    //anchor
    $input = preg_replace_callback('/\[A\((.+?)\)\((.+?)\)\](.+?)\[\/a\]/im', 'musdecodeanchor', $input);
    $input = str_ireplace("[/a]","</a>",$input);
    return $input;
}
?>