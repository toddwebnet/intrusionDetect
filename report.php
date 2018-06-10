<?php
$ipsFolder = realpath(__DIR__ . "/storage/ips");

$list = [];
foreach (scandir($ipsFolder) as $file) {
    if (in_array($file, ['.', '..'])) {
        continue;
    }
    $contents = file_get_contents($ipsFolder . '/' . $file);
    $data = json_decode($contents);
    $list[$data->ip] = $data;
}
asort($list);
$newList = [];
foreach ($list as $item) {
    $item->ts = date("Y-m-d H:m:i", $item->ts);
     $newList[] = (array)$item;
}
print_r($newList);


