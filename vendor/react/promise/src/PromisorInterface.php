<?php

namespace ECSPrefix20211001\React\Promise;

interface PromisorInterface
{
    /**
     * Returns the promise of the deferred.
     *
     * @return PromiseInterface
     */
    public function promise();
}
