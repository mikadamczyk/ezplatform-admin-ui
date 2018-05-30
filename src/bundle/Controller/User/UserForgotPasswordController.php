<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller\User;

use EzSystems\EzPlatformUserBundle\Controller\PasswordResetController;
use eZ\Publish\API\Repository\Values\Content\Location;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UserForgotPasswordController extends PasswordResetController
{
    public function __construct()
    {
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param string $uriFragment
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToLocation(Location $location, string $uriFragment = ''): RedirectResponse
    {
        return $this->redirectToRoute('_ezpublishLocation', [
            'locationId' => $location->id,
            '_fragment' => $uriFragment,
        ]);
    }
}
