<?php

declare(strict_types=1);

namespace App\Api\ModelBuilder;

use App\Api\ModelBuilder\Exception\BadModelBuilderException;
use App\Api\ModelBuilder\Exception\DataProviderDoesNotExistException;
use App\Api\ModelBuilder\Exception\ModelBuilderDoesNotExistException;

/**
 * Registry of the model (DTO) builders for exposed API data structures.
 *
 * Is being used to get model builder by model (DTO) class in controllers or other services
 */
class ModelBuilderRegistry
{
    protected array $registry;
    public const DEFAULT_PROVIDER_NAME = 'doctrine';

    /**
     * @param ModelBuilderInterface[] $modelBuilders All services which implement ModelBuilderInterface
     *
     * @throws BadModelBuilderException
     */
    public function __construct(iterable $modelBuilders)
    {
        foreach ($modelBuilders as $modelBuilder) {
            if (!($modelBuilder instanceof ModelBuilderInterface)) {
                throw new BadModelBuilderException('Can not register ModelBuilder in the registry as it does
                    not implement the ModelBuilderInterface');
            }

            $this->registry[$modelBuilder->getProviderName()][$modelBuilder->getModelClass()] = $modelBuilder;
        }
    }

    /**
     * @param string $providerName Name of the DataProvider, e.g. 'doctrine', 'api'
     * @param string $modelClass   e.g. 'App\Api\Model\OpenInquiryItem'
     *
     * @return ModelBuilderInterface the concrete model builder class for requested model and data provider type
     *
     * @throws DataProviderDoesNotExistException
     * @throws ModelBuilderDoesNotExistException
     */
    public function getModelBuilder(string $modelClass, string $providerName = ModelBuilderRegistry::DEFAULT_PROVIDER_NAME): ModelBuilderInterface
    {
        if (!isset($this->registry[$providerName])) {
            throw new DataProviderDoesNotExistException(sprintf('Data Provider %s does not registered in ModelBuilderRegistry', $providerName));
        }

        if (!isset($this->registry[$providerName][$modelClass])) {
            throw new ModelBuilderDoesNotExistException(sprintf('Model Builder %s for the %s DataProvider is not registered in ModelBuilderRegistry', $modelClass, $providerName));
        }

        return $this->registry[$providerName][$modelClass];
    }
}
