<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\RepositoryForms\Form\Processor;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\TranslationCreateStruct;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\ContentTranslationData;
use EzSystems\RepositoryForms\Event\FormActionEvent;
use EzSystems\RepositoryForms\Event\RepositoryFormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EzSystems\RepositoryForms\Form\Processor\ContentFormProcessor as DecoratedContentFormProcessor;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

/**
 * Listens for and processes RepositoryForm events: publish, remove draft, save draft and translate version
 *
 * ContentFormProcessor is needed to execute new `translateVersion()` method when user wants execute translation action.
 * This approach avoids BC brake in `EzSystems\RepositoryForms\Form\Processor\ContentFormProcessor`
 */
class ContentFormProcessor implements EventSubscriberInterface
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \EzSystems\RepositoryForms\Form\Processor\ContentFormProcessor */
    private $contentFormProcessor;

    /** @var \Symfony\Component\Routing\RouterInterface */
    private $router;

    /**
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \EzSystems\RepositoryForms\Form\Processor\ContentFormProcessor $contentFormProcessor
     * @param \Symfony\Component\Routing\RouterInterface $router
     */
    public function __construct(
        ContentService $contentService,
        DecoratedContentFormProcessor $contentFormProcessor,
        RouterInterface $router
    ) {
        $this->contentService = $contentService;
        $this->contentFormProcessor = $contentFormProcessor;
        $this->router = $router;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            RepositoryFormEvents::CONTENT_PUBLISH => ['processPublish', 10],
            RepositoryFormEvents::CONTENT_CANCEL => ['processCancel', 10],
            RepositoryFormEvents::CONTENT_SAVE_DRAFT => ['processSaveDraft', 10],
            RepositoryFormEvents::CONTENT_CREATE_DRAFT => ['processCreateDraft', 10],
        ];
    }

    /**
     * @param \EzSystems\RepositoryForms\Event\FormActionEvent $event
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function processPublish(FormActionEvent $event): void
    {
        $form = $event->getForm();
        /** @var \EzSystems\EzPlatformAdminUi\RepositoryForms\Data\ContentTranslationData $data */
        $data = $form->getData();

        if (!$data instanceof ContentTranslationData) {
            $this->contentFormProcessor->processPublish($event);
        }

        $content = $data->content;
        $initialLanguageCode = $data->initialLanguageCode;

        $translationCreateStruct = $this->getTranslationCreateStruct($data, $initialLanguageCode);

        $translatedContentDraft = $this->contentService->translateVersion($content->versionInfo, $translationCreateStruct);
        $this->contentService->publishVersion($translatedContentDraft->versionInfo);

        $redirectUrl = $form['redirectUrlAfterPublish']->getData() ?: $this->router->generate(
            '_ezpublishLocation', [
                'locationId' => $content->contentInfo->mainLocationId,
            ]
        );

        $event->setResponse(new RedirectResponse($redirectUrl));
    }

    /**
     * @param \EzSystems\RepositoryForms\Event\FormActionEvent $event
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException
     */
    public function processSaveDraft(FormActionEvent $event): void
    {
        $this->contentFormProcessor->processSaveDraft($event);
    }

    /**
     * @param \EzSystems\RepositoryForms\Event\FormActionEvent $event
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function processCancel(FormActionEvent $event): void
    {
        $this->contentFormProcessor->processCancel($event);
    }

    /**
     * @param \EzSystems\RepositoryForms\Event\FormActionEvent $event
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function processCreateDraft(FormActionEvent $event): void
    {
        $this->contentFormProcessor->processCreateDraft($event);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\RepositoryForms\Data\ContentTranslationData $data
     * @param string $initialLanguageCode
     *
     * @return \eZ\Publish\API\Repository\Values\Content\TranslationCreateStruct
     */
    private function getTranslationCreateStruct(ContentTranslationData $data, string $initialLanguageCode): TranslationCreateStruct
    {
        $content = $data->content;

        $translationCreateStruct = $this->contentService->newTranslationCreateStruct($content->contentInfo);
        $translationCreateStruct->initialLanguageCode = $initialLanguageCode;

        $fields = array_filter($data->fieldsData, function (FieldData $fieldData) use ($content, $data) {
            $mainLanguageCode = $content->getVersionInfo()->getContentInfo()->mainLanguageCode;

            return $mainLanguageCode === $data->initialLanguageCode
                || ($mainLanguageCode !== $data->initialLanguageCode && $fieldData->fieldDefinition->isTranslatable);
        });

        foreach ($fields as $field) {
            $translationCreateStruct->setField($field->fieldDefinition->identifier, $field->value);
        }

        return $translationCreateStruct;
    }
}
