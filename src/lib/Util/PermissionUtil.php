<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Util;

use eZ\Publish\API\Repository\Values\User\Limitation\ContentTypeLimitation;

class PermissionUtil
{
    /**
     * This method should only be used for very specific use cases. It should be used in a content cases
     * where assignment limitations are not relevant.
     *
     * @param array $hasAccess
     *
     * @return array
     */
    public function flattenArrayOfLimitations(array $hasAccess): array
    {
        $limitations = [];
        foreach ($hasAccess as $permissionSet) {
            if ($permissionSet['limitation'] !== null) {
                $limitations[] = $permissionSet['limitation'];
            }
            /** @var \eZ\Publish\API\Repository\Values\User\Policy $policy */
            foreach ($permissionSet['policies'] as $policy) {
                $policyLimitations = $policy->getLimitations();
                if (!empty($policyLimitations)) {
                    foreach ($policyLimitations as $policyLimitation) {
                        $limitations[] = $policyLimitation;
                    }
                }
            }
        }

        return $limitations;
    }

    /**
     * @param array $hasAccess
     *
     * @return array
     */
    public function getRestrictedContentTypesIds(array $hasAccess): array
    {
        $restrictedContentTypesIds = [];

        foreach ($this->flattenArrayOfLimitations($hasAccess) as $limitation) {
            if ($limitation instanceof ContentTypeLimitation) {
                $restrictedContentTypesIds[] = $limitation->limitationValues;
            }
        }

        return empty($restrictedContentTypesIds) ? $restrictedContentTypesIds : array_unique(array_merge(...$restrictedContentTypesIds));
    }
}
