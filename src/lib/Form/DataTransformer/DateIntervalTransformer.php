<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Translates Content's ID to domain specific VersionInfo object.
 */
class DateIntervalTransformer implements DataTransformerInterface
{
    /**
     * @param VersionInfo|null $value
     *
     * @return array|null
     *
     * @throws TransformationFailedException
     */
    public function transform($value)
    {
        return null;
    }

    /**
     * @param array|null $value
     *
     * @return array|null
     *
     * @throws \Exception
     * @throws TransformationFailedException
     * @throws UnauthorizedException
     * @throws NotFoundException
     */
    public function reverseTransform($value)
    {
        if (null === $value || !is_array($value)) {
            return null;
        }

        if (!array_key_exists('date_interval', $value) || !array_key_exists('end_date', $value)) {
            throw new TransformationFailedException(
                "Invalid data. Value array is missing 'date_interval' and/or 'end_date' keys"
            );
        }

        if (empty($value['date_interval'])) {
            return [];
        }
        $date = new \DateTime();

        if ($value['end_date']) {
            $date->setTimestamp($value['end_date']);
        }

        $date = new \DateTime();
        $end_date = $date->getTimestamp();
        $interval = new \DateInterval($value['date_interval']);
        $date->sub($interval);
        $start_date = $date->getTimestamp();

//        dump(['start_date' => $start_date, 'end_date' => $end_date]);
        return ['start_date' => $start_date, 'end_date' => $end_date];
    }
}
