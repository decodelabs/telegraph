<?php
/**
 * This is a stub file for IDE compatibility only.
 * It should not be included in your projects.
 */
namespace DecodeLabs;

use DecodeLabs\Veneer\Proxy as Proxy;
use DecodeLabs\Veneer\ProxyTrait as ProxyTrait;
use DecodeLabs\Telegraph\Context as Inst;
use DecodeLabs\Telegraph\Config as Ref0;
use DecodeLabs\Telegraph\Source as Ref1;
use DecodeLabs\Telegraph\Adapter as Ref2;

class Telegraph implements Proxy
{
    use ProxyTrait;

    public const Veneer = 'DecodeLabs\\Telegraph';
    public const VeneerTarget = Inst::class;

    protected static Inst $_veneerInstance;

    public static function setConfig(?Ref0 $config): void {}
    public static function getConfig(): ?Ref0 {
        return static::$_veneerInstance->getConfig();
    }
    public static function loadDefault(): Ref1 {
        return static::$_veneerInstance->loadDefault();
    }
    public static function load(string $name): Ref1 {
        return static::$_veneerInstance->load(...func_get_args());
    }
    public static function loadDefaultAdapter(): Ref2 {
        return static::$_veneerInstance->loadDefaultAdapter();
    }
    public static function loadAdapterFor(string $name): Ref2 {
        return static::$_veneerInstance->loadAdapterFor(...func_get_args());
    }
    public static function loadAdapter(string $name, array $settings = []): Ref2 {
        return static::$_veneerInstance->loadAdapter(...func_get_args());
    }
};
