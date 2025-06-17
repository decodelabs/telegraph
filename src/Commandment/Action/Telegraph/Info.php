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
use DecodeLabs\Terminus\Session;

#[Argument\Value(
    name: 'source',
    description: 'Source to view',
    required: true
)]
class Info implements Action
{
    public function __construct(
        protected Session $io
    ) {
    }

    public function execute(
        Request $request
    ): bool {
        $info = Telegraph::getListInfo($request->parameters->asString('source'));

        if(!$info) {
            $this->io->error('No list info found');
            return false;
        }

        $this->io->{'brightMagenta'}($info->id . ' ');
        $this->io->{'brightCyan'}($info->name . ' ');
        $this->io->{'.green'}('(' . ($info->memberCount ?? '??') . ')');

        $this->io->{'.red'}($info->subscribeUrl);

        if(!empty($info->groups)) {
            $this->io->{'.blue'}('Groups:');
        }

        foreach($info->groups as $group) {
            $this->io->{'>brightYellow'}($group->id . ' ');
            $this->io->{'.white'}($group->name);
        }

        if(!empty($info->tags)) {
            $this->io->{'.blue'}('Tags:');
        }

        foreach($info->tags as $tag) {
            $this->io->{'>brightYellow'}($tag->id . ' ');
            $this->io->{'.white'}($tag->name);
        }

        return true;
    }
}
