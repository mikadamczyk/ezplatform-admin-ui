<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\Trash;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\TrashService;
use EzSystems\EzPlatformAdminUi\Form\Data\TrashItemData;
use EzSystems\EzPlatformAdminUi\UI\Service\PathService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrashItemChoiceType extends AbstractType
{
    /** @var TrashService */
    private $trashService;

    /** @var PathService */
    private $pathService;

    /** @var ContentTypeService */
    private $contentTypeService;

    /**
     * @var LocationService
     */
    private $locationService;

    /**
     * @param TrashService $trashService
     * @param PathService $pathService
     * @param ContentTypeService $contentTypeService
     */
    public function __construct(
        TrashService $trashService,
        PathService $pathService,
        ContentTypeService $contentTypeService,
        LocationService $locationService
    ) {
        $this->trashService = $trashService;
        $this->pathService = $pathService;
        $this->contentTypeService = $contentTypeService;
        $this->locationService = $locationService;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new CallbackTransformer(
            function($value) {
                if (null === $value) {
                    return null;
                }

                if (!($value instanceof TrashItemData)) {
                    throw new TransformationFailedException('Expected a ' . TrashItemData::class . ' object.');
                }

                if ($value->getLocation() !== null) {
                    return $value->getLocation()->id;
                }

                return null;
            },
            function($value) {
                if (empty($value)) {
                    return null;
                }

                return null;
            }
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {

//        $resolver->setDefaults([
//            'choices' => $this->getTrashItemDataChoices(),
//            'choice_attr' => function (TrashItemData $val) {
//                return [
//                    'data-is-parent-in-trash' => (int)$val->isParentInTrash(),
//                ];
//            },
//        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): ?string
    {
        return TextType::class;
    }
}
