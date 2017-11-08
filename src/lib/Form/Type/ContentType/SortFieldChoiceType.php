<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ContentType;

use eZ\Publish\API\Repository\Values\Content\Location;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Translation\Exception\InvalidArgumentException;

/**
 * Form type for sort field selection.
 */
class SortFieldChoiceType extends AbstractType
{
    /** @var TranslatorInterface */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => $this->getSortFieldChoices(),
            'choices_as_values' => true,
            'translation_domain' => 'ezrepoforms_content_type',
        ]);
    }

    public function getParent(): ?string
    {
        return ChoiceType::class;
    }

    /**
     * Generate sort field options available to choose.
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    private function getSortFieldChoices(): array
    {
        $choices = [];
        foreach ($this->getSortFieldValues() as $sortField) {
            $choices[$this->getSortFieldLabel((string)$sortField)] = $sortField;
        }

        return $choices;
    }

    /**
     * Generate human readable label for sort field.
     *
     * @param string $sortField
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    private function getSortFieldLabel(string $sortField): string
    {
        return $this->translator->trans(/** @Ignore */'content_type.sort_field.' . $sortField, [], 'ezrepoforms_content_type');
    }

    /**
     * Returns available sort field values.
     *
     * @return array
     */
    private function getSortFieldValues(): array
    {
        return [
            Location::SORT_FIELD_NAME,
            Location::SORT_FIELD_PRIORITY,
            Location::SORT_FIELD_MODIFIED,
            Location::SORT_FIELD_PUBLISHED,
        ];
    }
}
