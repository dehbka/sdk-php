<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Tests\Unit\DataConverter;

use Ramsey\Uuid\Uuid;
use Temporal\DataConverter\EncodingKeys;
use Temporal\DataConverter\JsonConverter;
use Temporal\DataConverter\PayloadConverterInterface;
use Temporal\Tests\Unit\UnitTestCase;

/**
 * @group unit
 * @group data-converter
 */
class JsonConverterTestCase extends UnitTestCase
{
    protected function create(): PayloadConverterInterface
    {
        return new JsonConverter();
    }

    public function testUuidToPayload(): void
    {
        $converter = $this->create();

        $dto = Uuid::uuid4();

        $payload = $converter->toPayload($dto);

        $this->assertNotNull($payload);
        $this->assertSame(
            \json_encode((string)$dto),
            $payload->getData(),
        );
    }
}
