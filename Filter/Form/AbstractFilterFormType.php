<?php

namespace Sandronimus\RestToolsBundle\Filter\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractFilterFormType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method'             => 'GET',
            'allow_extra_fields' => true,
            'csrf_protection'    => false,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix(): string
    {
        return '';
    }
}

