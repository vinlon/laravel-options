<?php


namespace Vinlon\Laravel\Options;

use MongoDB\Driver\Query;

class OptionsManager
{
    private $prefix = '';

    /**
     * OptionsManager constructor.
     */
    public function __construct()
    {
    }

    /**
     * Set prefix
     * @param $prefix
     * @return OptionsManager
     */
    public function withPrefix($prefix)
    {
        $this->prefix = $prefix;
        return $this;
    }


    /**
     * @param string $key
     * @param string|null $default
     * @return string
     */
    public function get($key, $default = null)
    {
        /** @var Option $option */
        $option = Option::query()
            ->where('key', $this->getPrefixedKey($key))
            ->first();
        if (!$option) {
            return $default;
        }
        return $option->value;
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function set($key, $value)
    {
        Option::query()->updateOrInsert([
            'key' => $this->getPrefixedKey($key),
             'value' => $value,
        ]);
    }

    /**
     * @param string $key
     */
    public function remove($key)
    {
        Option::query()->where('key', $this->getPrefixedKey($key))->delete();
    }

    /**
     * @param string[] $keys
     * @param bool $removePrefix
     * @return array
     */
    public function batchGet($keys, $removePrefix = true)
    {
        $prefixedKeys = array_map(function ($key) {
            return $this->getPrefixedKey($key);
        }, $keys);
        $options = Option::query()
            ->whereIn('key', $prefixedKeys)
            ->get();

        return $options->mapWithKeys(function (Option $option) use ($removePrefix) {
            $key = $option->key;
            if (strlen($this->prefix) > 0 && $removePrefix) {
                $key = substr($key, strlen($this->prefix));
            }
            return [$key => $option->value];
        });
    }

    public function batchSet($keyValuePairs)
    {
        $existedValues = $this->batchGet(array_keys($keyValuePairs), false);
        $pendingInsert = $pendingUpdate = [];
        foreach ($keyValuePairs as $key => $value) {
            $key = $this->getPrefixedKey($key);
            if (array_key_exists($key, $existedValues)) {
                if ($value != $existedValues[$key]) {
                    $pendingUpdate[] = ['key' => $key, 'value' => $value];

                }
            } else {
                $pendingInsert[] = ['key' => $key, 'value' => $value];
            }
        }
        if (count($pendingInsert) > 0) {
            Option::query()->insert($pendingInsert);
        }
        if (count($pendingUpdate) > 0) {
            Option::query()->update($pendingUpdate);
        }
    }

    private function getPrefixedKey($key)
    {
        return $this->prefix . $key;
    }
}