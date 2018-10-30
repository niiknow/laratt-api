<?php

if (!file_exists("../.env")) {
    $output = exec('cd .. && ./webinit.sh');
}

header('Location: install');
exit();
