<?php

namespace Sandronimus\RestToolsBundle\Action;

use Sandronimus\RestToolsBundle\Action\Interfaces\NewActionConfigurationInterface;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\View\View;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class NewAction
{
    /** @var FormFactoryInterface */
    private $formFactory;

    /** @var RequestStack */
    private $requestStack;

    /** @var EntityManagerInterface  */
    private $em;

    /**
     * NewAction constructor.
     *
     * @param FormFactoryInterface   $formFactory
     * @param RequestStack           $requestStack
     * @param EntityManagerInterface $em
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        RequestStack $requestStack,
        EntityManagerInterface $em
    ) {
        $this->formFactory  = $formFactory;
        $this->requestStack = $requestStack;
        $this->em           = $em;
    }

    /**
     * @param NewActionConfigurationInterface $configuration
     *
     * @return View
     */
    public function run(NewActionConfigurationInterface $configuration): View
    {
        $entity = $configuration->createEntity();

        $form = $this->formFactory->create($configuration->getFormType(), $entity);
        $form->handleRequest($this->requestStack->getCurrentRequest());
        if (!$form->isSubmitted() || !$form->isValid()) {
            return View::create($form);
        }

        $this->em->persist($entity);
        $this->em->flush();

        $context = new Context();
        $context->setGroups($configuration->getSerializationGroups());


        $view = View::create($entity);
        $view->setContext($context);

        return $view;
    }
}
