<?php

require 'function.php';
require 'shift_helper.php';

$current = getCurrentShift($conn);
$previous = getPreviousShift($current['date'], $current['shift']);

echo "<h3>Current</h3>";
echo "Tanggal : ".$current['date']."<br>";
echo "Shift : ".$current['shift']."<br><br>";

echo "<h3>Previous</h3>";
echo "Tanggal : ".$previous['date']."<br>";
echo "Shift : ".$previous['shift']."<br>";