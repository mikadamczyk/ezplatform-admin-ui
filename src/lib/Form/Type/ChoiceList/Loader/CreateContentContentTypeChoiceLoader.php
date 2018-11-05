<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Loader;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup;
use EzSystems\EzPlatformAdminUi\Util\PermissionUtil;

class CreateContentContentTypeChoiceLoader
{
    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    protected $contentTypeService;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \EzSystems\EzPlatformAdminUi\Util\PermissionUtil */
    private $permissionUtil;

    /**
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     * @param \EzSystems\EzPlatformAdminUi\Util\PermissionUtil $permissionUtil
     */
    public function __construct(
        ContentTypeService $contentTypeService,
        PermissionResolver $permissionResolver,
        PermissionUtil $permissionUtil
    ) {
        $this->contentTypeService = $contentTypeService;
        $this->permissionResolver = $permissionResolver;
        $this->permissionUtil = $permissionUtil;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentType[]
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function load(): array
    {
        $contentTypes = [];
        $restrictedContentTypesIds = [];
        $contentTypeGroups = $this->contentTypeService->loadContentTypeGroups();
        $hasAccess = $this->permissionResolver->hasAccess('content', 'create');
        if (!is_bool($hasAccess)) {
            $restrictedContentTypesIds = $this->permissionUtil->getRestrictedContentTypesIds($hasAccess);
        }

        foreach ($contentTypeGroups as $contentTypeGroup) {
            $contentTypes[$contentTypeGroup->identifier] = $this->loadContentTypes($contentTypeGroup, $restrictedContentTypesIds);
        }

        return $contentTypes;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup $contentTypeGroup
     * @param array $restrictedContentTypesIds
     *
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentType[]
     */
    private function loadContentTypes(
        ContentTypeGroup $contentTypeGroup,
        array $restrictedContentTypesIds
    ): array {
        $contentTypes = $this->contentTypeService->loadContentTypes($contentTypeGroup);

        if (empty($restrictedContentTypesIds)) {
            return $contentTypes;
        }

        $restrictedContentTypes = array_filter($contentTypes, function ($contentType) use ($restrictedContentTypesIds) {
            return in_array($contentType->id, $restrictedContentTypesIds);
        });

        return $restrictedContentTypes;
    }
}
