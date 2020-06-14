<?php

namespace Sandronimus\RestToolsBundle\Filter;

use Doctrine\ORM\QueryBuilder;

interface FilterInterface
{
    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder(): QueryBuilder;

    /**
     * @param QueryBuilder $queryBuilder
     */
    public function applyFilter(QueryBuilder $queryBuilder): void;
}
