<?php 
namespace App\Helpers;
use Phpfastcache\CacheManager;
use Phpfastcache\Helper\Psr16Adapter;
use Phpfastcache\Drivers\Files\Config;

CacheManager::setDefaultConfig(
    new Config([
        'path' => dirname(__DIR__, 2) . '/data/cache',
        'securityKey' => 'agc',
        'secureFileManipulation' => true,
        'cacheFileExtension' => 'cache'
    ])
);
  
class Cache {
    protected static $cacheDriver = 'Files';
    protected static $Psr16Adapter;

    public static function getAdapter(){
        if (static::$Psr16Adapter === null) {
            static::$Psr16Adapter = new Psr16Adapter(static::$cacheDriver);
        }
        
        return static::$Psr16Adapter;
    }
};