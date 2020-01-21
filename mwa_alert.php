<?php
$mwa_station_id = 'WQ30_21';
$mwa_station_name = 'สุวรรณภูมิ';
$line_notify_access_token = 'line_notify_access_token';

$source = file_get_contents("https://twqonline.mwa.co.th/map_popup.php?stat={$mwa_station_id}");

preg_match('#<table id="show_value">(.+?)</table>#ism', $source, $match);
$data = $match[1];
$data = preg_replace('#<\?.+?\?>#', '', $data);
preg_match_all('#<tr[^>]*>(.+?)</tr>#ism', $data, $matches);
$msg = ["MWA {$mwa_station_name}\r\n"];
foreach ($matches[1] as $item) {
    $item = strip_tags($item);
    $item = preg_replace('#\s+#ism', ' ', $item);
    $item = trim($item);
    if(!empty($item)) {
        $msg[] = $item;
    }
}
$msg = join("\r\n", $msg);

$ch = curl_init('https://notify-api.line.me/api/notify');
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer {$line_notify_access_token}"]);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['message'=>$msg]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);

var_dump($msg, curl_getinfo($ch));
