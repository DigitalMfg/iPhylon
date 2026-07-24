<?php

require 'function.php';
require 'shift_helper.php';

$current = getCurrentShift($conn);

echo "Current : ";
print_r($current);

$previous = getPreviousShift($current['date'],$current['shift']);

echo "<br><br>Previous : ";
print_r($previous);