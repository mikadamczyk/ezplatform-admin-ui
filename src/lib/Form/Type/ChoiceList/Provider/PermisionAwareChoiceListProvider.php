<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Provider;

use eZ\Publish\API\Repository\PermissionResolver;
use EzSystems\EzPlatformAdminUi\Util\PermissionUtil;

class PermisionAwareChoiceListProvider implements ChoiceListProviderInterface
{
    /** @var \EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Provider\ChoiceListProviderInterface */
    private $decorated;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \EzSystems\EzPlatformAdminUi\Util\PermissionUtil */
    private $permissionUtil;

    /**
     * @var string
     */
    private $module;
    /**
     * @var string
     */
    private $function;

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Provider\ChoiceListProviderInterface $decorated
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     * @param \EzSystems\EzPlatformAdminUi\Util\PermissionUtil $permissionUtil
     */
    public function __construct(PermissionResolver $permissionResolver, PermissionUtil $permissionUtil, ContentTypeChoiceListProvider $decorated, string $module, string $function)
    {
        $this->decorated = $decorated;
        $this->permissionResolver = $permissionResolver;
        $this->permissionUtil = $permissionUtil;
        $this->module = $module;
        $this->function = $function;
    }

    public function getChoiceList(): array
    {
        $hasAccess = $this->permissionResolver->hasAccess($this->module, $this->function);
        if (!is_bool($hasAccess)) {
            $restrictedContentTypesIds = $this->permissionUtil->getRestrictedContentTypesIds($hasAccess);
        }

        $contentTypesGroups = $this->decorated->getChoiceList();

        if (empty($restrictedContentTypesIds)) {
            return $contentTypesGroups;
        }

        foreach($contentTypesGroups as $group => $contentTypes) {
            $contentTypesGroups[$group] = array_filter($contentTypesGroups, function ($contentType) use ($restrictedContentTypesIds) {
                return in_array($contentType->id, $restrictedContentTypesIds);
            });
        }

        return $contentTypesGroups;
    }
}
