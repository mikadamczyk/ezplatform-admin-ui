<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Provider;

use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\Values\User\Limitation\LanguageLimitation;
use EzSystems\EzPlatformAdminUi\Util\PermissionUtilInterface;
use eZ\Publish\API\Repository\Values\Content\Language;

class PermissionAwareLanguageChoiceListProvider implements ChoiceListProviderInterface
{
    /** @var \EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Provider\ChoiceListProviderInterface */
    private $decorated;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \EzSystems\EzPlatformAdminUi\Util\PermissionUtilInterface */
    private $permissionUtil;

    /** @var string */
    private $module;

    /** @var string */
    private $function;

    /**
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     * @param \EzSystems\EzPlatformAdminUi\Util\PermissionUtilInterface $permissionUtil
     * @param \EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Provider\LanguageChoiceListProvider $decorated
     * @param string $module
     * @param string $function
     */
    public function __construct(
        PermissionResolver $permissionResolver,
        PermissionUtilInterface $permissionUtil,
        LanguageChoiceListProvider $decorated,
        string $module,
        string $function
    ) {
        $this->decorated = $decorated;
        $this->permissionResolver = $permissionResolver;
        $this->permissionUtil = $permissionUtil;
        $this->module = $module;
        $this->function = $function;
    }

    /**
     * @return array
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function getChoiceList(): array
    {
        $restrictedLanguagesCodes = [];
        $hasAccess = $this->permissionResolver->hasAccess($this->module, $this->function);
        if (!is_bool($hasAccess)) {
            $restrictedLanguagesCodes = $this->permissionUtil->getRestrictions($hasAccess, LanguageLimitation::class);
        }

        $languages = $this->decorated->getChoiceList();

        if (empty($restrictedLanguagesCodes)) {
            return $languages;
        }

        return array_filter($languages, function (Language $language) use ($restrictedLanguagesCodes) {
            return in_array($language->languageCode, $restrictedLanguagesCodes, true);
        });
    }
}
