# laravel-options

Global key value storage


# Installation

```shell
composer require vinlon/laravel-options
```


# Init Database

```shell
php artisan migrate
```

# Usage

- basic usage: set, get and del

```php
opt()->set('key', 'value');
opt()->set('key', 'value_new');
$value = opt()->get('key');  // "value_new"
opt()->del('key');
$value = opt()->get('key', 'default_value'); // "default_value"
```

- batch operation: batchSet, batchGet, batchDel

```php
opt()->batchSet(['key1' => 'value1', 'key2' => 'value2']);
opt()->batchSet(['key1' => 'value1_new', 'key2' => 'value2']);
$values = opt()->batchGet(['key1', 'key2']); // ["key1":"value1_new", "key2":"value2"]
opt()->batchDel(['key1', 'key2']);
$values = opt()->batchGet(['key1', 'key2']); // []
```

- use prefix

```php
opt('test_')->set('key', 'test_value');
$value = opt()->get('test_key');
$value = opt('test_')->get('key'); // "test_value"
$value = opt()->withPrefix('test_')->get('key'); // "test_value"
opt('test_')->del('key');
$value = opt('test_')->get('key'); // null

opt('test_')->batchSet(['key' => 'test_value_new']);
$values = opt('test_')->batchGet(['key']);  // ["key":"test_value_new"]
$values = opt('test_')->batchGet(['key'], false);  // ["test_key":"test_value_new"]
opt('test_')->batchDel(['key']);
$values = opt('test_')->batchGet(['key']);  // []
```

# Reference

https://github.com/appstract/laravel-options  
https://github.com/overtrue/laravel-options  

