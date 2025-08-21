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
        protected Session $io,
        protected Telegraph $telegraph
    ) {
    }

    public function execute(
        Request $request
    ): bool {
        $source = $request->parameters->tryString('source');
        $sources = $this->getSources($source);

        foreach ($sources as $source) {
            $this->io->{'brightMagenta'}($source->name . ' ');
            $info = $source->refreshListInfo();

            if ($info !== null) {
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
        if ($source !== null) {
            $source = $this->telegraph->load($source);
            return $source !== null ? [$source] : [];
        }

        return $this->telegraph->loadAll();
    }
}
