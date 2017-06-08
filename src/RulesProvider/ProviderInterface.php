<?php

namespace Maximaster\ProductionCalendar\RulesProvider;

use Maximaster\ProductionCalendar\Rules;

interface ProviderInterface
{
    /**
     * ProviderInterface constructor.
     * @param ProviderInterface|null $parentProvider
     */
    public function __construct(ProviderInterface $parentProvider);

    /**
     * @return Rules
     */
    public function get();
}