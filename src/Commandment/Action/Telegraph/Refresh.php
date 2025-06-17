<?php

/**
 * @package Telegraph
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Commandment\Action\Telegraph;

use DecodeLabs\Commandment\Action;
use DecodeLabs\Commandment\Argument;
use DecodeLabs\Commandment\Request;
use DecodeLabs\Telegraph;
use DecodeLabs\Telegraph\Source;
use DecodeLabs\Terminus\Session;

#[Argument\Value(
    name: 'source',
    description: 'Source to refresh',
    required: false
)]
class Refresh implements Action
{
    public function __construct(
        protected Session $io
    ) {
    }

    public function execute(
        Request $request
    ): bool {
        if(!$store = Telegraph::getStore()) {
            $this->io->error('No store has been configured');
            return false;
        }

        $cache = Telegraph::getCache();
        $source = $request->parameters->tryString('source');
        $sources = $this->getSources($source);

        foreach($sources as $source) {
            $this->io->{'brightMagenta'}($source->name . ' ');
            $store->clearListInfo($source);
            $cache->clearListInfo($source);

            $info = $source->getListInfo();

            if($info !== null) {
                $this->io->{'brightYellow'}($info->id . ' ');
                $this->io->success('done');
            } else {
                $this->io->error('failed');
            }
        }

        return true;
    }

    /**
     * @return array<Source>
     */
    private function getSources(
        ?string $source = null
    ): array {
        if($source !== null) {
            $source = Telegraph::load($source);
            return $source !== null ? [$source] : [];
        }

        return Telegraph::loadAll();
    }
}
