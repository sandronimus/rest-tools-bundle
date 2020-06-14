<?php

namespace Sandronimus\RestToolsBundle\Action\Interfaces;

interface NewActionConfigurationInterface
{
    /**
     * @return object
     */
    public function createEntity();

    /**
     * @return string
     */
    public function getFormType(): string;

    /**
     * @return array
     */
    public function getSerializationGroups(): array;
}
