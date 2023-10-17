<?php

declare(strict_types=1);

namespace App\Api\ModelBuilder;

/**
 * Interface for all builders that receive data from data sources (database or external API) and converts it to
 *  an outcome data structure.
 */
interface ModelBuilderInterface
{
    /**
     * Returns the name of the resulting model class name.
     *
     * Required for a search for the appropriate Model Builder for the needed outcome model
     */
    public function getModelClass(): string;

    /**
     * Returns the data source provider name (e.g. Doctrine, SomeApiService, etc.).
     *
     * Required for a search for the appropriate Model Builder for particular data source provider
     */
    public function getProviderName(): string;
}
