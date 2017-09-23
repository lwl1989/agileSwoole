<?php



$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "http://dev-ss.locwebcloud.cc/sticker/android/10000/7d06efccf1a03d799d55d005a687c404a9ebe9b6/10060.png",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "authorization: Basic NDU5OjlRQ0NuUXRGQkhBMG44Skx0TmJIR2lEOFNLLTl3bTQxOWpYRUR5VHVHbFJIZjh6ajZnaERSdncyWXhFeHFOTVZIUWVSaDlQbWxBRVlsSDhY",
    "cache-control: no-cache",
    "charset: utf-8",
    "postman-token: aca2dd23-854d-25aa-fa41-a63825fe5752",
    "user-agent: LOC/1.0.0 Linux;U;Android 5.0.2;MI 2S Build/"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
