# Cryptocurrency Monitor

## Getting Started

### Set your API key
Obtain a free API key from [Nomics](https://p.nomics.com/cryptocurrency-bitcoin-api) and place it on line 57 of `index.php`. The Nomics API provides a great service with a competitive 1 second rate limit.  

```php
$api_key = [
    'key' => 'YOUR_KEY_HERE',
];
```

### Set your currency
Set the base currency used for conversion. You may use either a fiat currency or a cryptocurrency, examples include: `USD`, `GBP`, `EUR` or `USDT`, `BTC`. Place the value on line 61 of  `index.php`.

```php
$currnecy = 'USD';
```

### Define your crypto assets
Record your cryptocurrency investments in the array between lines 64-89 of `index.php`. Use numeric values, without quotes, since these values will be used for calculations on the page! Use the currency tickers as listed on [Nomic's website](https://nomics.com).

```php
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
    ] 
];
```
