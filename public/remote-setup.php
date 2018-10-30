<?php

$output = exec('cd .. && ./websetup.sh');
header('Location: install');
exit();
