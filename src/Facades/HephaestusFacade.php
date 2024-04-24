<?php

use Illuminate\Support\Facades\Facade;

/**
 * Debut d'implementation jsp si ce serait utile.
 */
class HephaestusFacade extends Facade
{

    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'hephaestus';
    }

}
