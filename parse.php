<?php

require_once('simple_html_dom.php');

$url = 'https://arbiscan.io/token/0x078f358208685046a11c85e8ad32895ded33a249?a=0x09a613F9D29E2C14238b219Bd0B78c61ad7D40C9';
$html = file_get_contents($url);

$dom = new simple_html_dom();
$dom->load($html);

$balance = $dom->find('#ContentPlaceHolder1_divFilteredHolderBalance', 0)->plaintext;
preg_match('/Balance (.*) aArbWBTC/', $balance, $match);
$balance = $match[1];
$balance = trim($balance);
sleep(1);

$url = 'https://arbiscan.io/token/generic-tokenholders2?m=light&a=0x7f1dd51843d8c4106213d0a4c3a7e96306c5d86f';
$html = file_get_contents($url);

$dom = new simple_html_dom();
$dom->load($html);

$trs = $dom->find('#maintable tbody tr');

$total = 0;
foreach ($trs as $key => $tr) {
	if ($key === 0) {
		continue;
	}
	$quantity = $tr->find('td', 2)->plaintext;
	$quantity = preg_replace('/,/', '', $quantity);
	$quantity = trim($quantity);
	$total += $quantity;
}

$result = 0;
if (($total) && ($balance)) {
	$result = $balance / $total;
	$result = json_encode(array("result"=>$result));
	file_put_contents('result.json', $result);
	header("Access-Control-Allow-Origin: *");
	echo $result;
}
?>