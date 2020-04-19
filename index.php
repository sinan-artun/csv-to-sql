<?php

echo '<pre>';
$re = '/(?:,|\n|^)("(?:(?:"")*[^"]*)*"|[^",\n]*|(?:\n|$))/';

$fileData = function () {
    $file = fopen(__DIR__ . '/csv/application_test.csv', 'rb');

    if (!$file) {
        die('file does not exist or cannot be opened');
    }

    while (($line = fgets($file)) !== false) {
        yield $line;
    }

    fclose($file);
};


function get_type($var)
{

    if (!is_numeric($var)) {
        return ['string', strlen($var)];

    }

    $foo = 0 + $var;
    $type = gettype($foo);
    //return $type;

    if ($type === 'integer') {
        if ($foo < 0) {
            return ['integer', 0, (strlen($var) - 1)];
            //return 'integer|negative|'.(strlen($var)-1);
        }
        return ['integer', 1, strlen($var)];
    }

    if ($type === 'double') {
        $dot_pos = strpos($var, '.');
        $len = strlen($var);
        $float_len = ($len - ($dot_pos + 1));

        if ($foo < 0) {

            return ['double', ($len - 1), $float_len];

        }

        return ['double', ($len ), $float_len];
    }


}


function prepare_sql($column_names, $col_arr)
{

    if (count($column_names) !== count($col_arr)) {
        die('column names count are not equal to column data');
    }

    $sql = 'CREATE TABLE `test` ('."\n";
    foreach ($col_arr as $k => $v) {

        $sql .= "`$column_names[$k]` ";
        if ($v['type'] === 'integer') {
            if ($v['len'] < 10) {
                //$sql .= 'int(' . $v['len'] . ') ';
                $sql .= 'int ';

            } else {
                //$sql .= 'bigint(' . $v['len'] . ') ';
                $sql .= 'bigint ';
            }

            if ($v['unsigned'] === 1) {
                $sql .= 'unsigned ';
            }

            if ($k === 0) {
                $sql .= 'NOT NULL AUTO_INCREMENT,' . "\n";
            } else {
                $sql .= 'DEFAULT NULL,' . "\n";
            }

        } else if ($v['type'] === 'double') {
            $sql .= 'DECIMAL(' . $v['total'] . ',' . $v['float'] . ') DEFAULT NULL,' . "\n";

        } else {
            if ($v['len'] < 1000) {
                $sql .= 'varchar(' . $v['len'] . ') DEFAULT NULL,' . "\n";
            } else {
                $sql .= 'text DEFAULT NULL,' . "\n";
            }

        }


    }

    $sql .= 'PRIMARY KEY (`' . $column_names[0] . '`)' . "\n";

    $sql .= ') ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;';


    echo $sql;


}


$string_t = [
    'type' => 'string',
    'len' => 10,
];

$integer_t = [
    'type' => 'integer',
    'unsigned' => 1,
    'len' => 0,
];

$double_t = [
    'type' => 'double',
    'total' => 4,
    'float' => 2,
];
$col_names = [];
$col_arr = [];
foreach ($fileData() as $k => $line) {
    // $line contains current line
    $line = trim($line);
    if ($k === 0) {
        //$col_arr = explode(',',$line);

        preg_match_all($re, $line, $matches, PREG_SET_ORDER, 0);
        $col_names = array_column($matches, '1');
        for ($i = 0, $iMax = count($col_names); $i < $iMax; $i++) {
            $col_arr[$i] = $integer_t;
        }

    } else {

        preg_match_all($re, $line, $matches, PREG_SET_ORDER, 0);
        $columns = array_column($matches, '1');

        foreach ($columns as $i => $column) {
            if ($column === '') {
                continue;
            }

            $new_type = get_type($column);
            if ($new_type[0] === 'string') {
                if ($col_arr[$i]['type'] === 'string') {
                    if ($col_arr[$i]['len'] < $new_type[1]) {
                        $col_arr[$i]['len'] = ($new_type[1] + 1);
                    }

                } else {
                    // type changed
                    // old type in not string
                    // convert old type to string
                    $col_arr[$i] = [];
                    $col_arr[$i]['type'] = 'string';
                    $col_arr[$i]['len'] = ($new_type[1] + 1);

                }


            } else if ($new_type[0] === 'double') {
                if ($col_arr[$i]['type'] === 'double') {

                    if ($new_type[1] > $col_arr[$i]['total']) {
                        $col_arr[$i]['total'] = $new_type[1];
                    }
                    if ($new_type[2] > $col_arr[$i]['float']) {
                        $col_arr[$i]['float'] = $new_type[2];
                    }


                } else if ($col_arr[$i]['type'] === 'integer') {
                    // type changed
                    // update type to double
                    $col_arr[$i] = $double_t;

                    $col_arr[$i]['total'] = $new_type[1];
                    $col_arr[$i]['float'] = $new_type[2];

                }

            } else if ($new_type[0] === 'integer') {
                if ($col_arr[$i]['type'] === 'integer') {
                    // old type also integer
                    if ($new_type[1] !== $col_arr[$i]['unsigned']) {
                        // update old unsigned
                        $col_arr[$i]['unsigned'] = 0;
                    }

                    if ($new_type[2] > $col_arr[$i]['len']) {
                        $col_arr[$i]['len'] = $new_type[2];
                    }
                } else if ($col_arr[$i]['type'] === 'double') {
                    // type changed but you can not change type from double to integer

                }


            }


        }
    }

}


//print_r($col_arr);
prepare_sql($col_names, $col_arr);
