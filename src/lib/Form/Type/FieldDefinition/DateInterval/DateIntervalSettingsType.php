<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\FieldDefinition\DateInterval;

use eZ\Publish\Core\FieldType\DateInterval\Type as TypeAlias;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

final class DateIntervalSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'widget',
            ChoiceType::class,
            [
                'choices' => [
                    /** @Desc("Choice") */
                    'field_definition.ibexadateinterval.widget_choice' => TypeAlias::WIDGET_CHOICE,
                    /** @Desc("Text") */
                    'field_definition.ibexadateinterval.widget_text' => TypeAlias::WIDGET_TEXT,
                    /** @Desc("Integer") */
                    'field_definition.ibexadateinterval.widget_integer' => TypeAlias::WIDGET_INTEGER,
                    /** @Desc("Text") */
                    'field_definition.ibexadateinterval.widget_single_text' => TypeAlias::WIDGET_SINGLE_TEXT,
                ],
                'expanded' => true,
                'required' => true,
                'label' => /** @Desc("The basic way in which this field should be rendered") */ 'field_definition.ezdate.default_type',
                'translation_domain' => 'content_type',
            ]
        );
    }
}
