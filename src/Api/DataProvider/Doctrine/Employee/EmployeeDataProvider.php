<?php

declare(strict_types=1);

namespace App\Api\DataProvider\Doctrine\Employee;

use App\Api\Model\Employee\EmployeeItemModel;
use App\Api\ModelBuilder\Doctrine\EmployeeItemModelBuilder;
use App\Api\ModelBuilder\Exception\DataProviderDoesNotExistException;
use App\Api\ModelBuilder\Exception\ModelBuilderDoesNotExistException;
use App\Api\ModelBuilder\ModelBuilderRegistry;
use App\Entity\Employee;
use App\Repository\EmployeeRepository;

class EmployeeDataProvider
{
    public function __construct(
        private readonly EmployeeRepository $repo,
        private readonly ModelBuilderRegistry $builderRegistry
    ) {
    }

    /**
     * @throws DataProviderDoesNotExistException
     * @throws ModelBuilderDoesNotExistException|ModelBuilderDoesNotExistException
     */
    public function getAllEmployeesItems(): \Generator
    {
        $dataSourceIterator = $this->repo->getEmployees();

        if (iterator_count($dataSourceIterator) > 0) {
            /** @var EmployeeItemModelBuilder $modelBuilder */
            $modelBuilder = $this->builderRegistry->getModelBuilder(EmployeeItemModel::class);

            foreach ($dataSourceIterator as $build) {
                yield $modelBuilder->buildModel($build);
            }
        } else {
            // Return empty \Generator
            yield from [];
        }
    }

    /**
     * @throws DataProviderDoesNotExistException
     * @throws ModelBuilderDoesNotExistException
     */
    public function getEmployee(Employee $employee): EmployeeItemModel
    {
        /** @var EmployeeItemModelBuilder $modelBuilder */
        $modelBuilder = $this->builderRegistry->getModelBuilder(EmployeeItemModel::class);

        return $modelBuilder->buildModel($employee);
    }
}
