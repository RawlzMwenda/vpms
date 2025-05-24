<?php
$output = shell_exec('python3 stkpush.py');
$response = json_decode($output, true);

echo "<pre>";
print_r($output);
echo "</pre>";