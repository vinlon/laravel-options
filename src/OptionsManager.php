<?php


namespace Vinlon\Laravel\Options;

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
        $key = $this->getPrefixedKey($key);
        $option = Option::query()
            ->where('key', $key)
            ->firstOrNew(['key' => $key]);
        $option->value = $value;
        $option->save();
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
        $prefixedKeys = $this->getPrefixedKeys($keys);
        $options = Option::query()
            ->whereIn('key', $prefixedKeys)
            ->get();

        return $options->mapWithKeys(function (Option $option) use ($removePrefix) {
            $key = $option->key;
            if (strlen($this->prefix) > 0 && $removePrefix) {
                $key = substr($key, strlen($this->prefix));
            }
            return [$key => $option->value];
        })->toArray();
    }

    /**
     * @param array $keyValuePairs
     */
    public function batchSet($keyValuePairs)
    {
        $prefixedKeys = $this->getPrefixedKeys(array_keys($keyValuePairs));
        $existedValues = Option::query()
            ->whereIn('key', $prefixedKeys)
            ->get()
            ->mapWithKeys(function (Option $option) {
                return [$option->key => $option];
            });

        // TODO: 暂时不考虑性能问题
        foreach ($keyValuePairs as $key => $value) {
            $key = $this->getPrefixedKey($key);
            /** @var Option $existedValue */
            $existedValue = $existedValues->get($key);
            if ($existedValue) {
                if ($value != $existedValue->value) {
                    $existedValue->value = $value;
                    $existedValue->save();
                }
            } else {
                $option = new Option();
                $option->key = $key;
                $option->value = $value;
                $option->save();
            }
        }
    }

    /**
     * @param string $key
     */
    public function del($key)
    {
        $key = $this->getPrefixedKey($key);
        Option::query()->where('key', $key)->delete();
    }

    /**
     * @param string[] $keys
     */
    public function batchDel($keys)
    {
        $keys = $this->getPrefixedKeys($keys);
        Option::query()->whereIn('key', $keys)->delete();
    }

    private function getPrefixedKey($key)
    {
        return $this->prefix . $key;
    }

    private function getPrefixedKeys($keys)
    {
        return array_map(function ($key) {
            return $this->getPrefixedKey($key);
        }, $keys);
    }
}
