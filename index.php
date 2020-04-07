<?php
phpinfo();
//$csv = file_get_contents('csv/application_test.csv');
//
//$file = new SplFileObject('csv/application_test.csv');
//
//// Loop until we reach the end of the file.
//while (!$file->eof()) {
//    // Echo one line from the file.
//    echo $file->fgets();
//}
//
//// Unset the file to call __destruct(), closing the file handle.
//$file = null;
//echo '<pre>';


//$fileData = function() {
//    $file = fopen(__DIR__ . '/csv/application_test.csv', 'r');
//
//    if (!$file)
//        die('file does not exist or cannot be opened');
//
//    while (($line = fgets($file)) !== false) {
//        yield $line;
//    }
//
//    fclose($file);
//};
//
//
//function get_type($var){
//
//    $is_numeric = is_numeric($var);
//    if($is_numeric){
//
//        $nd = (float)$is_numeric;
//        $ii = is_int($nd);
//    }
//
//    $if = is_float($var);
//    $ii = is_int($var);
//    $in = is_numeric($var);
//    $var_len = strlen($var);
//
//    $double = (double) $var;
//
//    $int_len = strlen((int)$var);
//    $float_len = strlen((float)$var);
//    $double_len = strlen(settype($double,'string'));
//
//    return $var_len.'|'.$int_len.'|'.$float_len.'|'.$double_len;
//
//
//
//
//}
//
//
//foreach ($fileData() as $k => $line) {
//    // $line contains current line
//    if($k==1){
//        $columns  = explode(',',$line);
//        foreach($columns as $column){
//            echo $column . ' -> ' .get_type($column)."\n";
//        }
//    }elseif($k<2){
//        continue;
//    }
//    else{
//        break;
//    }
//
//}




