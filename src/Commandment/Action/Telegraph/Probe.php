<?php

/**
 * @package Telegraph
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Commandment\Action\Telegraph;

use DecodeLabs\Coercion;
use DecodeLabs\Commandment\Action;
use DecodeLabs\Commandment\Request;
use DecodeLabs\Telegraph;
use DecodeLabs\Terminus\Session;

class Probe implements Action
{
    public function __construct(
        protected Session $io,
        protected Telegraph $telegraph
    ) {
    }

    public function execute(
        Request $request
    ): bool {
        if (!$config = $this->telegraph->config) {
            $this->io->error('No config has been provided');
            return false;
        }

        $sources = $config->getSourceNames();
        $map = [];

        foreach ($sources as $sourceName) {
            $settings = $config->getSourceSettings($sourceName);
            $settings = array_merge([
                'adapter' => $config->getSourceAdapter($sourceName)
            ], $settings);

            $hash = md5((string)json_encode($settings));
            $map[$hash] = $settings;
        }

        foreach ($map as $settings) {
            $adapter = Coercion::asString($settings['adapter']);
            $this->io->{'.brightMagenta'}($adapter . ' ');
            $adapter = $this->telegraph->loadAdapter($adapter, $settings);

            foreach ($adapter->fetchAllListReferences() as $list) {
                $this->io->{'>brightYellow'}($list->id . ' ');
                $this->io->{'.brightCyan'}($list->name);
            }

            $this->io->newLine();
        }

        return true;
    }
}
