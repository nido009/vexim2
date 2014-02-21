<?php

$language = 'de_DE';
putenv ("LANG=$language");
setlocale(LC_ALL, "");
bindtextdomain('messages', './locale');
textdomain('messages');

?>
