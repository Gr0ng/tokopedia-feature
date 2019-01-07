<?php
require_once("sdata-modules.php");
/**
 * @Author: Eka Syahwan
 * @Date:   2017-12-11 17:01:26
 * @Last Modified by:   Eka Syahwan
 * @Last Modified time: 2017-12-11 17:15:02
*/
$a 		= range("a", "z");
$break 	= false;
foreach ($a as $key => $dataz) {
	for ($i=60; $i <20000; $i+=60) { 
		
		echo "[+] Start ".$i." in page ".$dataz." \r\n";

		$url[] = array(
			'url' => 'https://ace.tokopedia.com/search/v1/shop?scheme=https&device=desktop&related=true&source=search&st=shop&rows=60&start='.$i.'&q='.$dataz.'&unique_id=aa0fcf58ab1c4120b4e4820aee560655', 
		);
		$head[] = array(
			'header' => array(
			    "cache-control: no-cache",
			    "referer: https://www.tokopedia.com/p/kategori-fashion-wanita?page=4",
			    "user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36"
			  ), 
		);
		$respons 	= $sdata->sdata($url , $head); unset($url);unset($head);
		if(preg_match("/Search Error/", $respons[0]['respons'])){
			break;
		}
		foreach ($respons as $key => $exdata) {
			$json = json_decode($exdata['respons'],true);
			foreach ($json['data'] as $key => $dataToko) {
				$url[] = array(
					'url' =>'https://m.tokopedia.com/'.str_replace("https://www.tokopedia.com/", "", $dataToko[uri])."/info", 
				);
			}
			$fopn = fopen("address.xls", "a+");
			file_put_contents("log.txt", $i);

			$respons = $sdata->sdata($url , $head ); unset($url);unset($head);
			foreach ($respons as $key => $dataex) {
				preg_match_all('/"location_email":"(.*?)"/m', $dataex['respons'], $email);
				preg_match_all('/"location_phone":"(.*?)"/m', $dataex['respons'], $phone);
				preg_match_all('/"location_address":"(.*?)"/m', $dataex['respons'], $alamat);
				preg_match_all('/"location_area":"(.*?)"/m', $dataex['respons'], $area);
				
				if(!empty($email[1][0])){
					
					$echo .= "[+] Informasi : ".$dataex[info][url]."\r\n";
					$echo .= "[+] Alamat    : ".$alamat[1][0]."\r\n";
					$echo .= "[+] Kota      : ".$area[1][0]."\r\n";
					$echo .= "[+] Nomor Hp  : ".$phone[1][0]."\r\n";
					$echo .= "[+] Email     : ".$email[1][0]."\r\n";

					fwrite($fopn, $dataex[info][url].",".$phone[1][0].",".$email[1][0].",".$alamat[1][0].",".$area[1][0]."\r\n");

					echo $echo;
					unset($echo);
					echo "\r\n";

				}
			}
		}
	}
}