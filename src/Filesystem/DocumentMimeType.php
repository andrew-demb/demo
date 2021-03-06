<?php

/**
 * PHP Service Bus demo application
 *
 * @author  Maksim Masiukevich <dev@async-php.com>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */
declare(strict_types = 1);

namespace App\Filesystem;

/**
 * Document media type
 */
final class DocumentMimeType
{
    /**
     * @var string
     */
    public $base;

    /**
     * @var string
     */
    public $subType;

    public function __construct(string $base, string $subType)
    {
        $this->base    = $base;
        $this->subType = $subType;
    }
}
