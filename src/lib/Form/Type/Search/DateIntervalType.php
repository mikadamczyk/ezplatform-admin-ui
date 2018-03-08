<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\Search;

use EzSystems\EzPlatformAdminUi\Form\DataTransformer\DateIntervalTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType as BaseDateIntervalType;

class DateIntervalType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date_interval', BaseDateIntervalType::class, [
//                'attr' => ['hidden' => true],
                'input' => 'string',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('end_date', IntegerType::class, [
//                'attr' => ['hidden' => true],
                'required' => false,
            ])
            ->addModelTransformer(new DateIntervalTransformer());
    }
}
