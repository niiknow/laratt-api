<?php

$output = exec('cd .. && ./webinit.sh');
header('Location: install');
exit();
