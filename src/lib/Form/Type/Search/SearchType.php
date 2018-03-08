<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\Search;

use EzSystems\EzPlatformAdminUi\Form\Data\Search\SearchData;
use EzSystems\EzPlatformAdminUi\Form\Type\ContentType\ContentTypeChoiceType;
use EzSystems\EzPlatformAdminUi\Form\Type\Section\SectionChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SearchType as CoreSearchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Exception\AccessException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('query', CoreSearchType::class)
            ->add('page', HiddenType::class)
            ->add('limit', HiddenType::class)
            ->add('section', SectionChoiceType::class, [
                'required' => false,
                'multiple' => false,
            ])
            ->add('content_types', ContentTypeChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('last_modified', DateIntervalType::class)
            ->add('created', DateIntervalType::class)
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @throws AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
        ]);
    }
}
