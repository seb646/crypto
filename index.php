<!--------------------------------------------------------------
| CRYPTOCURRENCY PORTFOLIO MONITOR
|---------------------------------------------------------------
|
| Author: Sebastian Rodriguez
| Version: 1.0.0
| GitHub: github.com/seb646/crypto
|
| Copyright (c) 2021, Sebastian Rodriguez. All rights reserved.
| 
| Redistribution and use in source and binary forms, with or 
| without modification, are permitted provided that the following 
| conditions are met:
| 
|   1. Redistributions of source code must retain the above 
|      copyright notice, this list of conditions and the 
|      following disclaimer.
| 
|   2. Redistributions in binary form must reproduce the above 
|      copyright notice, this list of conditions and the 
|      following disclaimer in the documentation and/or other 
|      materials provided with the distribution.
| 
|   3. Neither the name of the copyright holder nor the names 
|      of its contributors may be used to endorse or promote 
|      products derived from this software without specific 
|      prior written permission.
| 
| THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND 
| CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, 
| INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF 
| MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE 
| DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR 
| CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, 
| SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT 
| NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; 
| LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) 
| HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,WHETHER IN 
| CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR 
| OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, 
| EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
|
|-------------------------------------------------------------->

<?php 
/*
|---------------------------------------------------------------
| CONFIG SETTINGS
|---------------------------------------------------------------
*/

// Grab the Nomics API
$url = 'https://api.nomics.com/v1';

// Set your Nomics API key: https://p.nomics.com/cryptocurrency-bitcoin-api
$api_key = [
    'key' => 'YOUR_KEY_HERE',
];

// Set the currency conversion (crypto or fiat). Fiat examples: USD, GBP, EUR, CAD; Crypto examples: USDT, BTC
$currnecy = 'USD';

// Define your personal crypto assets using currency tickers
$assets = [
    'BTC' => [
        'coins' => 0,
        'invested' => 0,
    ],
    'ETH' => [
        'coins' => 0,
        'invested' => 0,
    ], 
    'DOGE' => [
        'coins' => 0,
        'invested' => 0,
    ], 
    'ADA' => [
        'coins' => 0,
        'invested' => 0,
    ], 
    'LTC' => [
        'coins' => 0,
        'invested' => 0,
    ], 
    'XRP' => [
        'coins' => 0,
        'invested' => 0,
    ],   
];

$coin_ids = '';
foreach($assets as $key => $value){
    $coin_ids .= $key.',';
}
$coin_ids = substr($coin_ids, 0, -1);

/*
|---------------------------------------------------------------
| API – CURRENCIES/TICKER
|---------------------------------------------------------------
*/

// Set the endpoint; see docs: https://nomics.com/docs
$endpoint = '/currencies/ticker';

// Join the URL with the endpoints
$url_endpoint = $url.$endpoint;

// Set the parameters for the API ticker endpoint
$parameters = [
    // Set the coin tickers 
    'ids' => $coin_ids,
    
    // Set the currency
    'convert' => $currnecy,
    
    // Set the history interval
    'interval' => '1h,1d,7d,30d,365d',
];
$parameters = array_merge($api_key,$parameters); 

// query string encode the parameters
$qs = http_build_query($parameters);

// create the request URL
$request = "{$url_endpoint}?{$qs}";

// Ask API for JSON format
$headers = [
    'Accepts: application/json',
];

// Get cURL resource
$curl = curl_init();

// Set cURL options
curl_setopt_array($curl, array(
    // set the request URL
    CURLOPT_URL => $request,
    
    // set the headers 
    CURLOPT_HTTPHEADER => $headers,
    
    // ask for raw response instead of bool
    CURLOPT_RETURNTRANSFER => 1
));

// Send the request, save the response
$response = curl_exec($curl); 

// Decode the response 
$response = json_decode($response);

// Close request
curl_close($curl);

// Define the usable array
$crypto = [];

// Iterate through the raw JSON 
foreach($response as $c){
    
    // Set the history stats
    $timeline = [];
    foreach($c as $key => $value){
        if($key == '1d'){
            $timeline['1d'] = $value->price_change_pct*100;
        }
    }
    
    // Add the currency to a usable array
    $crypto[$c->symbol] = [
        'name' => $c->name,
        'ticker' => $c->symbol,
        'price' => $c->price,
        'logo' => $c->logo_url,
        'percent_change_24h' => $timeline['1d'],
        'market_cap' => $c->market_cap,
        'circulation' => $c->circulating_supply,
    ];
}

/*
|---------------------------------------------------------------
| API – CURRENCIES/SPARKLINE
|---------------------------------------------------------------
*/

// DO NOT REMOVE. Pause 1 second for API rate limit
sleep(1);

// Set the endpoint; see docs: https://nomics.com/docs
$endpoint = '/currencies/sparkline';

// Join the URL with the endpoints
$url_endpoint = $url.$endpoint;

// Set the time variable for the past 24hrs and format
date_default_timezone_set('America/New_York');
$date = (new \DateTime())->modify('-1 month');
$date = $date->format(\DateTime::RFC3339);

// Set the parameters for the API ticker endpoint
$parameters = [
    // Set the coin tickers 
    'ids' => $coin_ids,
    
    // Set the currency
    'convert' => $currnecy,
    
    // Set the state date
    'start' => $date,
];
$parameters = array_merge($api_key,$parameters); 

// query string encode the parameters
$qs = http_build_query($parameters);

// create the request URL
$request = "{$url_endpoint}?{$qs}";

// Ask API for JSON format
$headers = [
    'Accepts: application/json',
];

// Get cURL resource
$curl = curl_init();

// Set cURL options
curl_setopt_array($curl, array(
    // set the request URL
    CURLOPT_URL => $request,
    
    // set the headers 
    CURLOPT_HTTPHEADER => $headers,
    
    // ask for raw response instead of bool
    CURLOPT_RETURNTRANSFER => 1
));

// Send the request, save the response
$response = curl_exec($curl); 

// Decode the response 
$response = json_decode($response);

// Close request
curl_close($curl);

$crypto_high_low = [];
$crypto_price_map = [];

foreach($response as $c){
    $price_high = $crypto[$c->currency]['price'];
    $price_low = $crypto[$c->currency]['price'];
        
    if($c->currency == 'BITTORRENT'){
        $price_high = $crypto['BTT']['price'];
        $price_low = $crypto['BTT']['price'];
        $c->currency = 'BTT';
    }
    
    foreach($c->prices as $price){
        if($price >= $price_high){
            $price_high = $price;
        }
        if($price <= $price_low){
            $price_low = $price;
        }
    }
    
    // Add the currency to a usable array
    $crypto_high_low[$c->currency] = [
        'high' => $price_high,
        'low' => $price_low,
    ];
    $price_high = 0;
    $price_low = 0;
    
    $crypto_price_map[$c->currency] = [
        'times' => $c->timestamps,
        'prices' => $c->prices,
    ];
}

/*
|---------------------------------------------------------------
| MISC FUNCTIONS
|---------------------------------------------------------------
*/

// Function for displaying the market cap
function bd_nice_number($n) {
    // Strip any formatting;
    $n = (0+str_replace(",","",$n));
       
    // Check if it's a num
    if(!is_numeric($n)) return false;
       
    // Filter through trillions, billions, and millions
    if($n>1000000000000) return round(($n/1000000000000),2).'T';
    else if($n>1000000000) return round(($n/1000000000),2).'B';
    else if($n>1000000) return round(($n/1000000),2).'M';
       
    return number_format($n);
}

?>
<!doctype html>
<html>
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cryptocurrency</title>
  <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" crossorigin="anonymous">
</head>
<body class="bg-gray-100">
<div class="px-4 py-8 sm:p-8 bg-gradient-to-b from-gray-700 to-gray-600">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
  <div class="md:flex md:items-center md:justify-between">
    <div class="flex-1 min-w-0">
      <h2 class="text-2xl font-bold leading-7 text-white sm:text-3xl sm:truncate select-none">
        <i class="fas fa-fw fa-coins"></i> Cryptocurrency
      </h2>
    </div>
  </div>

    </div>
  </div>
<div>
<?php if($crypto){?>
    <div class="max-w-7xl mx-auto pt-6 pb-24 px-6 sm:px-6 lg:px-8">
      <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3">
        <div>
            <div class="bg-white shadow overflow-hidden rounded-lg">
              <div class="px-6 py-3 border-b border-gray-00 sm:px-6 bg-gray-50 rounded-t-lg">
                <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Total Invested
                </h3>
              </div>
              <div class="border-t border-gray-200">
                  <div class="p-4 sm:px-6 text-3xl font-extrabold text-gray-900">
                    $<?php 
                  $total = 0;
                  foreach($assets as $c){
                      $total += $c['invested'];
                  }
                  echo number_format($total, 2, '.', ',');
                  ?>
                  </div>
              </div>
            </div>
            
            <div class="bg-white shadow overflow-hidden rounded-lg mt-6">
              <div class="px-6 py-3 border-b border-gray-00 sm:px-6 bg-gray-50 rounded-t-lg">
                <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Total Value
                </h3>
              </div>
              <div class="border-t border-gray-200">
                  <div class="p-4 sm:px-6 text-3xl font-extrabold text-gray-900">
                    $<?php 
                  $value = 0;
                  foreach($crypto as $c){
                    $value += ($c['price'] * $assets[$c['ticker']]['coins']);
                  }
                  echo number_format($value, 2, '.', ',');
                  ?>
                  </div>
              </div>
            </div>
            
            <div class="bg-white shadow overflow-hidden rounded-lg mt-6">
              <div class="px-6 py-3 border-b border-gray-00 sm:px-6 bg-gray-50 rounded-t-lg">
                <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Gain/Loss
                </h3>
              </div>
              <div class="border-t border-gray-200">
                  <div class="p-4 sm:px-6 text-3xl font-extrabold text-gray-900">
                    <?php 
                $net_total = $value - $total;
                if($net_total > 0){
                ?>
                <div class="flex block items-baseline text-green-800">
                      <svg class="-ml-1 mr-0.5 flex-shrink-0 self-center h-7 w-7 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                      </svg>
                      <span class="sr-only">
                        Increased by
                      </span>
                      $<?php echo number_format($net_total, 2, '.', ',');?>
                    </div>
                    <?php }elseif($net_total < 0){?>
                    <div class="flex block items-baseline text-red-800">
                      <svg class="-ml-1 mr-0.5 flex-shrink-0 self-center h-7 w-7 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                      </svg>
                      <span class="sr-only">
                        Decreased by
                      </span>
                      -$<?php echo number_format(abs($net_total), 2, '.', ',');?>
                    </div>
                <?php }else{?>
                    <div class="flex block items-baseline text-gray-500">
                      <svg class="-ml-1 mr-0.5 flex-shrink-0 self-center h-7 w-7 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6" />
                        </svg>
                      <span class="sr-only">
                        Even 
                      </span>
                      $<?php echo number_format($net_total, 2, '.', ',');?>
                    </div>
                    <?php }?>
                  </div>
              </div>
            </div>
        </div>
        <div>
            <div class="bg-white shadow overflow-hidden rounded-lg">
              <div class="px-6 py-3 border-b border-gray-00 sm:px-6 bg-gray-50 rounded-t-lg">
                <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Assets by Coin Value (<?php echo $currnecy;?>)
                </h3>
              </div>
              <div class="border-t border-gray-200">
                  <div class="px-4 py-5 sm:px-6">
                    <?php 
                    if($total != 0){?>
                        <div class="m-auto" style="width:300px">
                            <canvas id="assets"></canvas>
                        </div>
                    <?php }else{?>
                        <div class="py-20">
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                              <svg class="h-6 w-6  text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                              </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-5">
                              <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                No Information Available
                              </h3>
                            </div>
                        </div>
                    <?php }?>
                  </div>
              </div>
             </div>
        </div>
        <div>
            <div class="bg-white shadow overflow-hidden rounded-lg">
              <div class="px-6 py-3 border-b border-gray-00 sm:px-6 bg-gray-50 rounded-t-lg">
                <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                  GitHub
                </h3>
              </div>
              <div class="border-t border-gray-200">
                  <div class="px-4 py-5 sm:px-6">
                    <ul class="fa-ul text-sm text-gray-800" style="margin-left: 25px;">
                      <li class="mb-3"><span class="fa-li"><i class="fab fa-github fa-fw text-xl text-gray-500"></i></span>&nbsp; <a target="_blank"href="https://github.com/seb646/crypto">seb646/crypto</a></li>
                      <li><span class="fa-li"><i class="fas fa-shield-alt fa-fw text-xl text-gray-500"></i></span>&nbsp; <a target="_blank" href="https://github.com/seb646/crypto/blob/main/LICENSE">BSD 3-Clause License</a></li>
                    </ul>
                  </div>
              </div>
             </div>
             <div class="bg-white shadow overflow-hidden rounded-lg mt-6">
              <div class="px-6 py-3 border-b border-gray-00 sm:px-6 bg-gray-50 rounded-t-lg">
                <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Credits
                </h3>
              </div>
              <div class="border-t border-gray-200">
                  <div class="px-4 py-5 sm:px-6">
                    <ul class="fa-ul text-sm text-gray-800" style="margin-left: 25px;">
                      <li class="mb-3"><span class="fa-li"><i class="fas fa-minus fa-fw text-base text-gray-500"></i></span><span class="inline-block ml-1">Built by <a target="_blank" href="https://srod.ca">Sebastian Rodriguez</a> using <a target="_blank" href="https://tailwindcss.com">TailwindCSS</a> and <a target="_blank" href="https://chartjs.org/">Chart.js</a></span></li>
                      <li><span class="fa-li"><i class="fas fa-minus fa-fw text-base text-gray-500"></i></span><span class="inline-block ml-1">Pricing data provided by the <a target="_blank" href="https://nomics.com">Nomics Cryptocurrency Market API</a></span></li>
                    </ul>
                  </div>
              </div>
        </div>
      </dl>

      <div class="mt-8 space-y-4 sm:space-y-0 sm:grid sm:grid-cols-2 sm:gap-6 lg:max-w-4xl lg:mx-auto xl:max-w-none xl:mx-0 xl:grid-cols-3">
        <?php foreach($crypto as $c){
        $value = $c['price'] * $assets[$c['ticker']]['coins'];
        $crypto_value[$c['ticker']] = $value;?>
        <div class="space-y-4">
        <div class="border border-gray-200 rounded-lg shadow-sm bg-white overflow-hidden" style="height:fit-content">
          <div class="p-6">
            <div class="grid grid-cols-3">
                <div class="col-span-2">
                    <a target="_blank" href="<?php echo 'https://coinmarketcap.com/currencies/'.str_replace(' ', '-', strtolower($c['name']));?>">
                    <div style="height: 30px;width: 30px;float: left;margin: auto;background-size:100% 100%;border-radius:90px;background-position: center;margin-right: 10px;margin-top:5px;background-image:url(<?php echo $c['logo'];?>);"></div>
                    <h2 class="block text-lg leading-6 font-medium text-gray-900"><?php echo $c['name'];?> <br><span class="text-gray-500 text-sm"><?php echo $c['ticker'];?>/<?php echo $currnecy;?></span></h2>
                    </a>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500 text-right">Market Cap</div>
                    <div class="text-sm text-gray-600 text-right">$<?php echo bd_nice_number($c['market_cap']);?></div>
                </div>
            </div>
            <div class="mt-6 mb-2 flex justify-between items-baseline md:block lg:flex">
              <div class="flex items-baseline text-3xl font-extrabold text-gray-900">$<?php 
              if($c['price'] >= 1000){
                echo number_format($c['price'], 3, '.', ',');
              }elseif($c['ticker'] == 'BTT'){
                echo $c['price'];
              }else{
                echo number_format($c['price'], 4, '.', ',');
              }
              ?></div>
              <?php if($c['percent_change_24h'] > 0){?>
              <div class="inline-flex items-baseline px-2.5 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800 md:mt-2 lg:mt-0">
                  <svg class="-ml-1 mr-0.5 flex-shrink-0 self-center h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                  </svg>
                  <span class="sr-only">
                    Increased by
                  </span>
                  <?php echo round($c['percent_change_24h'],4);?>%
                </div>
                <?php }else{?>
                <div class="inline-flex items-baseline px-2.5 py-0.5 rounded-full text-sm font-medium bg-red-100 text-red-800 md:mt-2 lg:mt-0">
                  <svg class="-ml-1 mr-0.5 flex-shrink-0 self-center h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                  <span class="sr-only">
                    Decreased by
                  </span>
                  <?php echo round($c['percent_change_24h'],4);?>%
                </div>
                <?php }?>
            </div>
            <div class="relative">
                <div class="mt-4 overflow-hidden h-2 mb-1 text-xs flex rounded bg-gray-200">
                    <div style="width:<?php echo (($c['price']-$crypto_high_low[$c['ticker']]['low'])/($crypto_high_low[$c['ticker']]['high']-$crypto_high_low[$c['ticker']]['low'])*100);?>%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-gray-500"></div>
              </div>
              <div class="flex items-center justify-between">
                <div>
                  <span class="text-xs font-semibold inline-block text-gray-600">
                    $<?php if($crypto_high_low[$c['ticker']]['low'] >= 1000){
                            echo number_format($crypto_high_low[$c['ticker']]['low'], 3, '.', ',');
                          }elseif($c['ticker'] == 'BTT'){
                            echo $crypto_high_low[$c['ticker']]['low'];
                          }else{
                            echo number_format($crypto_high_low[$c['ticker']]['low'], 4, '.', ',');
                          };?>
                  </span>
                </div>
                <div class="text-right">
                  <span class="text-xs font-semibold inline-block text-gray-600">
                    $<?php if($crypto_high_low[$c['ticker']]['high'] >= 1000){
                            echo number_format($crypto_high_low[$c['ticker']]['high'], 3, '.', ',');
                          }elseif($c['ticker'] == 'BTT'){
                            echo $crypto_high_low[$c['ticker']]['high'];
                          }else{
                            echo number_format($crypto_high_low[$c['ticker']]['high'], 4, '.', ',');
                          };?>
                  </span>
                </div>
              </div>
            </div>
        <?php if($assets[$c['ticker']]['coins'] > 0){?>
        <dt class="text-sm font-medium text-gray-500 mt-5">
          Your Coins
        </dt>
        <dd class="mt-1 text-sm text-gray-900">
          <ul class="border border-gray-200 rounded-md divide-y divide-gray-200">
            <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
              <div class="w-0 flex-1 flex items-center">
                <i class="fas fa-coins text-gray-400"></i>
                <span class="ml-2 flex-1 w-0 truncate">
                  <?php if($assets[$c['ticker']]['coins']){ echo $assets[$c['ticker']]['coins']; }else{ echo '0'; }?> <?php echo $c['ticker'];?>
                </span>
              </div>
              <div class="ml-4 flex-shrink-0">
                <span href="#" class="font-medium text-gray-600">
                  $<?php echo number_format($value, 2, '.', ',');?>
                </span>
              </div>
            </li>
          </ul>
        </dd>
        <dt class="text-sm font-medium text-gray-500 mt-4">
            Your Investment
        </dt>
        <dd class="mt-1 text-sm text-gray-900">
          <ul class="border border-gray-200 rounded-md divide-y divide-gray-200">
            <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
              <div class="w-0 flex-1 flex items-center">
                <i class="fas fa-money-bill-wave text-gray-400"></i>
                <span class="ml-2 flex-1 w-0 truncate">
                  $<?php if($assets[$c['ticker']]['invested']){ echo number_format($assets[$c['ticker']]['invested'], 2, '.', ','); }else{ echo '0'; }?> <?php echo $currnecy;?>
                </span>
              </div>
              <div class="ml-4 flex-shrink-0">
                  <?php 
                $net = $value - $assets[$c['ticker']]['invested'];
                $net = number_format($net, 2, '.', ',');
                if($net > 0){?>
              <div class="flex block items-baseline py-0.5 rounded-full text-sm font-medium text-green-800 md:mt-2 lg:mt-0">
                  <svg class="-ml-1 mr-0.5 flex-shrink-0 self-center h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                  </svg>
                  <span class="sr-only">
                    Increased by
                  </span>
                  $<?php echo $net;?>
                </div>
                <?php }elseif($net < 0){?>
                <div class="flex block items-baseline py-0.5 rounded-full text-sm font-medium text-red-800 md:mt-2 lg:mt-0">
                  <svg class="-ml-1 mr-0.5 flex-shrink-0 self-center h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                  <span class="sr-only">
                    Decreased by
                  </span>
                  -$<?php echo abs($net);?>
                </div>
                <?php }else{?>
                <div class="flex block items-baseline py-0.5 rounded-full text-sm font-medium text-gray-500 md:mt-2 lg:mt-0">
                  <svg class="-ml-1 mr-0.5 flex-shrink-0 self-center h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6" />
                    </svg>
                  <span class="sr-only">
                    Even 
                  </span>
                  $<?php echo abs($net);?>
                </div>
                <?php }?>
              </div>
            </li>
          </ul>
          <?php }?>
        </div>
    <div style="margin-left: -8px;width: 103%;margin-bottom: -8px;">
                <canvas id="<?php echo $c['ticker'];?>"></canvas>
            </div>
  </div>
</div>

        <?php }?>

      </div>
    </div>
    <?php }else{?>
    <div class="flex items-end justify-center px-4 py-44 text-center sm:block">
        <div class="inline-block align-bottom rounded-lg px-6 py-10 text-left overflow-hidden sm:my-8 sm:align-middle sm:max-w-sm sm:w-full">
      <div>
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
          <svg class="h-6 w-6  text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
        </div>
        <div class="mt-3 text-center sm:mt-5">
          <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
            Setup Failed
          </h3>
          <div class="mt-2">
            <p class="text-sm text-gray-500">
              Please verify your Nomics API key, query parameters, and the endpoint URL. 
            </p>
          </div>
        </div>
      </div>
    </div>
    </div>
    <?php }?>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.2.0/chart.min.js"></script>
<script>
<?php foreach($crypto_price_map as $key => $value){?>
var ctx = document.getElementById('<?php echo $key;?>');
var chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [
            <?php 
            foreach($value['times'] as $time){ 
                $time = strtotime($time);
                $time = date('M j @ H:i T', $time);
                echo '\''.$time.'\',';
            }?>
        ],
        datasets: [{
            label: '<?php echo $currnecy;?>',
            fill: true,
            data: [
                <?php 
                foreach($value['prices'] as $price){ 
                    echo $price.',';
                }?>
            ],
            backgroundColor: [
                'rgb(209 250 228)',
            ],
            borderColor: [
                'rgb(18 185 129)',
            ],
            borderWidth: 3,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                displayColors: false
            }
        },
        scales: {
            x: {
                ticks: {
                    display: false
                },
                grid: {
                    display: false,
                    drawBorder: false,
                }
            },
            y: {
                ticks: {
                    display: false
                },
                grid: {
                    display: false,
                    drawBorder: false,
                }
            }
        },
        elements: {
            point: {
                borderWidth: 0,
                radius: 0,
                hitRadius: 10,
            }
        }
    }
});
<?php }?>
var ctx = document.getElementById('assets');
var chart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: [
            <?php 
            foreach($crypto_value as $key => $value){ 
                echo '\''.$key.'\',';
            }?>
        ],
        datasets: [{
            data: [
                <?php 
                foreach($crypto_value as $key => $value){ 
                    echo $value.',';
                }?>
            ],
            backgroundColor: [
              'rgb(247,147,26)',
              'rgb(95,101,139)',
              'rgb(194,166,50)',
              'rgb(59,199,200)',
              'rgb(70,70,70)',
            ],
        }]
    },
    options: {
        cutout: 80,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                displayColors: false,
            }
        }
    }
});
</script>
</body>
</html>
