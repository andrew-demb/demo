<?php

/**
 * Demo application, remotely similar to Uber
 *
 * @author  Maksim Masiukevich <desperado@minsk-info.ru>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */
declare(strict_types = 1);

namespace App\Driver\Document\Data;

/**
 * Documents collection
 */
final class DriverDocuments
{
    /**
     * @var array<string, \App\Driver\Document\Data\DriverDocument>
     */
    private $collection = [];

    /**
     * @param DriverDocument $document
     *
     * @return void
     */
    public function add(DriverDocument $document): void
    {
        $this->collection[(string) $document->id()] = $document;
    }

    /**
     * @param DriverDocumentId $id
     *
     * @return void
     */
    public function remove(DriverDocumentId $id): void
    {
        unset($this->collection[(string) $id->id()]);
    }
}