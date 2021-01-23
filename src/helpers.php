<?php

use Vinlon\Laravel\Options\OptionsManager;

if (!function_exists('opt')) {
    /**
     * Get the opt instance
     * @param string|null $prefix
     * @return OptionsManager
     */
    function opt($prefix = null)
    {
        /** @var OptionsManager $optionManager */
        $optionManager = app()->get(OptionsManager::class);
        if (is_null($prefix)) {
            return $optionManager;
        } else {
            return $optionManager->withPrefix($prefix);
        }
    }
}
