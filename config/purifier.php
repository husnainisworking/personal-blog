<?php
/**
 * Ok, glad you are here
 * first we get a config instance, and set the settings
 * $config = HTMLPurifier_Config::createDefault();
 * $config->set('Core.Encoding', $this->config->get('purifier.encoding'));
 * $config->set('Cache.SerializerPath', $this->config->get('purifier.cachePath'));
 * if ( ! $this->config->get('purifier.finalize')) {
 *     $config->autoFinalize = false;
 * }
 * $config->loadArray($this->getConfig());
 *
 * You must NOT delete the default settings
 * anything in settings should be compacted with params that needed to instance HTMLPurifier_Config.
 *
 * @link http://htmlpurifier.org/live/configdoc/plain.html
 */



// This is HTMLPurifier configuration file used in Laravel.
return [
    'encoding'           => 'UTF-8',
    'finalize'           => true,
    'ignoreNonStrings'   => false,
    'cachePath'          => storage_path('app/purifier'),
    'cacheFileMode'      => 0755, // file permission for cache files.
    'settings'      => [
        'default' => [
            'HTML.Doctype'             => 'HTML 4.01 Transitional',
            'HTML.Allowed'             => 'p,b,a[href],i,strong,em,br',
            'CSS.AllowedProperties'    => '',
            'AutoFormat.AutoParagraph' => true,
            'AutoFormat.RemoveEmpty'   => true,
        ],

        // Strict config for comments - NO HTML allowed
        'comment' => [

            'HTML.Allowed' => '', // Strip all HTML tags
            'AutoFormat.AutoParagraph' => false,
            'AutoFormat.RemoveEmpty' => true,
            'Core.EscapeInvalidTags' => true,
            'Core.EscapeASCIICharacters' => false,
        ],

        // Config for names/titles - NO HTML at all.
        'text' => [
            'HTML.Allowed' => '',
            'AutoFormat.RemoveEmpty' => true,
        ],
    ],

];
