<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\Trash;

use eZ\Publish\API\Repository\ContentTypeService;
use EzSystems\EzPlatformAdminUi\Form\Data\Trash\TrashItemRestoreData;
use EzSystems\EzPlatformAdminUi\Form\Data\TrashItemData;
use EzSystems\EzPlatformAdminUi\Form\Type\UniversalDiscoveryWidget\UniversalDiscoveryWidgetType;
use EzSystems\EzPlatformAdminUi\UI\Service\PathService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrashItemRestoreType extends AbstractType
{
    /**
     * @var ContentTypeService
     */
    private $contentTypeService;

    /**
     * @var PathService
     */
    private $pathService;

    /**
     * TrashItemRestoreType constructor.
     *
     * @param ContentTypeService $contentTypeService
     * @param PathService $pathService
     */
    public function __construct(ContentTypeService $contentTypeService, PathService $pathService)
    {
        $this->contentTypeService = $contentTypeService;
        $this->pathService = $pathService;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        $options['data']['trash_items'] = $options['trashItems'];
//        var_dump($options['trashItems']);
//        die();
        $builder
            ->add(
                'trash_items',
                TrashItemChoiceType::class,
                [
                    'multiple' => true,
                    'expanded' => true,
                    'choice_label' => false,
                    'label' => false,
                    'choices' => $options['trashItems'],
                    'choice_attr' => function (TrashItemData $val) {
                        return [
                            'data-is-parent-in-trash' => (int)$val->isParentInTrash(),
                        ];
                    },
                    'data' => null
                ]
            )
            ->add(
                'location',
                UniversalDiscoveryWidgetType::class,
                [
                    'multiple' => false,
                    'label' => /** @Desc("Restore under new parent") */
                        'trash_item_restore_form.restore_under_new_parent',
                    'attr' => $options['attr'],
                ]
            )
            ->add(
                'restore',
                SubmitType::class,
                [
                    'label' => /** @Desc("Restore selected") */
                        'trash_item_restore_form.restore',
                ]
            );

//        $updateTrashItems = function (FormEvent $event) use ($options) {
//            $data = $event->getData();
//            $form = $event->getForm();
//
//            if ($data instanceof TrashItemRestoreData) {
//
//                $form->add('trash_items', TrashItemChoiceType::class, [
//                    'multiple' => true,
//                    'expanded' => true,
//                    'choice_label' => false,
//                    'label' => false,
//                    'choices' => $data->getTrashItems(),
//                    'data' => null
//                ]);
//            }
//        };
//
//        $builder->addEventListener(FormEvents::PRE_SET_DATA, $updateTrashItems);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrashItemRestoreData::class,
            'translation_domain' => 'forms',
            'trashItems' => [],
        ]);
    }
}
