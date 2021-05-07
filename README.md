# Cryptocurrency Portfolio Monitor

## Getting Started

### API Key
Obtain a free API key from [Nomics](https://p.nomics.com/cryptocurrency-bitcoin-api) and place it on line 57 of `index.php`.

```php
$api_key = [
    'key' => 'YOUR_KEY_HERE',
];
```

### Define Your Crypto Assets
Record your cryptocurrency investments in the array between lines 61-86 of `index.php`. Use numeric values, without quotes, since these values will be used for calculations on the page! Use the currency tickers as listed on [Nomic's website](https://nomics.com).

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
