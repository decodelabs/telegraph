<?php

/**
 * @package Telegraph
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Telegraph;

use DecodeLabs\Archetype;
use DecodeLabs\Dovetail;
use DecodeLabs\Dovetail\Config\Telegraph as TelegraphConfig;
use DecodeLabs\Exceptional;
use DecodeLabs\Telegraph;
use DecodeLabs\Veneer;

class Context
{
    /**
     * @var array<string,Source>
     */
    protected array $sources = [];

    protected ?Config $config = null;

    public function setConfig(
        ?Config $config
    ): void {
        $this->config = $config;
    }

    public function getConfig(): ?Config
    {
        if (
            $this->config === null &&
            class_exists(Dovetail::class)
        ) {
            $this->config = TelegraphConfig::load();
        }

        return $this->config;
    }

    public function loadDefault(): Source
    {
        $config = $this->getConfig();

        if(!$sourceName = $config?->getDefaultSourceName()) {
            throw Exceptional::NotFound(
                'Telegraph default source not configured'
            );
        }

        return $this->load($sourceName);
    }

    public function load(
        string $name
    ): Source {
        if (isset($this->sources[$name])) {
            return $this->sources[$name];
        }

        $adapter = $this->loadAdapterFor($name);
        $remoteId = $this->getConfig()?->getSourceRemoteId($name);

        if($remoteId === null) {
            throw Exceptional::Setup(
                'Telegraph remote ID not configured for source: ' . $name
            );
        }

        $source = new Source(
            $name,
            $adapter,
            $remoteId
        );

        return $this->sources[$name] = $source;
    }



    public function loadDefaultAdapter(): Adapter
    {
        $config = $this->getConfig();

        if(!$sourceName = $config?->getDefaultSourceName()) {
            throw Exceptional::NotFound(
                'Telegraph default source not configured'
            );
        }

        return $this->loadAdapterFor($sourceName);
    }

    public function loadAdapterFor(
        string $name
    ): Adapter {
        if (!$config = $this->getConfig()) {
            throw Exceptional::ComponentUnavailable(
                'Telegraph config not set'
            );
        }

        if(!$adapterName = $config->getSourceAdapter($name)) {
            throw Exceptional::NotFound(
                'Telegraph adapter not configured: ' . $name
            );
        }

        $settings = $config->getSourceSettings($name);
        return $this->loadAdapter($adapterName, $settings);
    }

    /**
     * @param array<string,mixed> $settings
     */
    public function loadAdapter(
        string $name,
        array $settings = []
    ): Adapter {
        $name = ucfirst($name);

        if(!$class = Archetype::tryResolve(Adapter::class, $name)) {
            throw Exceptional::NotFound(
                'Telegraph adapter not found: ' . $name
            );
        }

        return new $class($settings);
    }
}


// Veneer
Veneer\Manager::getGlobalManager()->register(
    Context::class,
    Telegraph::class
);
