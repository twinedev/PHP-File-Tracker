<?php
// this is a very VERY basic viewer of the JSON data file
// You should modify it to customize for your needs.

// Note, this is assuming that this file is still being called while
// the __auto_prepend.php file is still included so the the constant
// is already set, otherwise hard code in the .json file path

echo '<pre><tt>';

$data = file_get_contents( __FILE_LIST_JSON );
$data = json_decode( $data, true );

print_r( $data );
