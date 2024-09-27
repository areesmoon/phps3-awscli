<?php
require_once("phps3awscli.php");
$phps3 = new Phps3awscli('access_key_id', 'secret_access_key', 'endpoint_url');

echo "<pre>";

# LISTING BUCKET
$output = $phps3->list_buckets();
echo "LIST BUCKET:\n";
print_r($output);

# CHECKING BUCKET
$bucket = 'test';
$output = $phps3->check_bucket($bucket);
echo "\n\nCHECK BUCKET: \nNAME '$bucket': ".$output;

# PUSH A FILE
$ar_output = $phps3->push('test', '/var/www/aws/images/simple.jpeg', 'images/simple.jpg');
echo "\n\nPUSH:\n";
echo $ar_output;

# PULL A FILE
$ar_output = $phps3->pull('test', 'images/simple.jpg', '/var/www/aws/images/simple.jpeg');
echo "\n\nPULL:\n";
print_r($ar_output);

# PUSH A FOLDER RECURSIVELY
$ar_output = $phps3->push_all('test', '/var/www/aws/', 'aws/');
echo "\n\nPUSH ALL:\n";
print_r($ar_output);

# PULL A FOLDER RECURSIVELY
$ar_output = $phps3->pull_all('test', 'aws/', '/var/www/aws/');
echo "\n\nPULL ALL:\n";
print_r($ar_output);

echo "</pre>";
?>