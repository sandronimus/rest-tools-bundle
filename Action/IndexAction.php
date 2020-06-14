<?php

namespace Sandronimus\RestToolsBundle\Action;

use Sandronimus\RestToolsBundle\Action\Interfaces\IndexActionConfigurationInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\View\View;
use RuntimeException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class IndexAction
{
    /** @var FormFactoryInterface */
    private $formFactory;

    /** @var RequestStack */
    private $requestStack;

    /**
     * IndexAction constructor.
     *
     * @param FormFactoryInterface $formFactory
     * @param RequestStack         $requestStack
     */
    public function __construct(FormFactoryInterface $formFactory, RequestStack $requestStack)
    {
        $this->formFactory  = $formFactory;
        $this->requestStack = $requestStack;
    }

    /**
     * @param Interfaces\IndexActionConfigurationInterface $configuration
     *
     * @return View
     */
    public function run(IndexActionConfigurationInterface $configuration): View
    {
        $filter = $configuration->getFilter();
        $form   = $this->formFactory->create(
            $configuration->getFilterFormType(),
            $filter
        );

        $form->handleRequest($this->requestStack->getCurrentRequest());
        if ($form->isSubmitted() && $form->isValid()) {
            $filterQueryBuilder = $filter->getQueryBuilder();
            $filter->applyFilter($filterQueryBuilder);
        }
        elseif ($form->isSubmitted()) {
            return View::create($form);
        }
        else {
            $filterQueryBuilder = $filter->getQueryBuilder();
        }

        $totalCount = $this->getTotalCount($filterQueryBuilder);

        $this->applyPagination($filterQueryBuilder);

        $view = new View();
        $context = new Context();

        $context->setGroups($configuration->getSerializationGroups());
        $view->setContext($context);

        $view->setData($filterQueryBuilder->getQuery()->getResult());
        $view->setHeader('X-Total-Count', $totalCount);

        return $view;
    }

    /**
     * @param QueryBuilder $queryBuilder
     */
    private function applyPagination(QueryBuilder $queryBuilder): void
    {
        $page = 1;
        $itemsPerPage = 20;

        $request = $this->requestStack->getCurrentRequest();
        if ($request === null) {
            throw new RuntimeException('Null request');
        }

        $requestPage = (int)$request->get('page');
        if ($requestPage > 0) {
            $page = $requestPage;
        }

        $requestOnPage = (int)$request->get('itemsPerPage');
        if (in_array($requestOnPage, [20, 50, 100], true)) {
            $itemsPerPage = $requestOnPage;
        }

        $queryBuilder
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->setMaxResults($itemsPerPage);
    }

    /**
     * @param QueryBuilder $filterQueryBuilder
     *
     * @return int
     */
    private function getTotalCount(QueryBuilder $filterQueryBuilder): int
    {
        $paginator = new Paginator($filterQueryBuilder);

        return $paginator->count();
    }
}
