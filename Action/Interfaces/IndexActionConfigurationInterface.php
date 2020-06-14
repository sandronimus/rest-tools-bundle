<?php

namespace Sandronimus\RestToolsBundle\Action\Interfaces;

use Sandronimus\RestToolsBundle\Filter\FilterInterface;

interface IndexActionConfigurationInterface
{
    /**
     * @return FilterInterface
     */
    public function getFilter(): FilterInterface;

    /**
     * @return string
     */
    public function getFilterFormType(): string;

    /**
     * @return array
     */
    public function getSerializationGroups(): array;
}
