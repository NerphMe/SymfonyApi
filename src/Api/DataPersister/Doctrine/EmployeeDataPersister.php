<?php

declare(strict_types=1);

namespace App\Api\DataPersister\Doctrine;

use App\Api\Model\Employee\CreateOrUpdateEmployeeModel;
use App\Api\Model\Employee\EmployeeItemModel;
use App\Api\ModelBuilder\Exception\DataProviderDoesNotExistException;
use App\Api\ModelBuilder\Exception\ModelBuilderDoesNotExistException;
use App\Api\ModelBuilder\ModelBuilderRegistry;
use App\Entity\Employee;
use Doctrine\ORM\EntityManagerInterface;

class EmployeeDataPersister
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ModelBuilderRegistry $builderRegistry,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function createEmployee(CreateOrUpdateEmployeeModel $model): EmployeeItemModel
    {
        $employee = new Employee();

        $employee->setName($model->name)
            ->setEmail($model->email)
            ->setSurname($model->surname)
            ->setHiredAt(new \DateTime($model->hiredAt))
            ->setCurrentSalaryAmount($model->currentSalaryAmount);

        $this->entityManager->persist($employee);
        $this->entityManager->flush();

        return $this->buildSingeItemModel($employee);
    }

    public function deleteEmployee(Employee $employee): void
    {
        $this->entityManager->remove($employee);
        $this->entityManager->flush();
    }

    /**
     * @throws \Exception
     */
    public function updateEmployee(Employee $employee, CreateOrUpdateEmployeeModel $model): EmployeeItemModel
    {
        $employee->setName($model->name)
            ->setEmail($model->email)
            ->setSurname($model->surname)
            ->setHiredAt(new \DateTime($model->hiredAt))
            ->setCurrentSalaryAmount($model->currentSalaryAmount);

        $this->entityManager->flush();

        return $this->buildSingeItemModel($employee);
    }

    /**
     * @throws DataProviderDoesNotExistException
     * @throws ModelBuilderDoesNotExistException
     */
    private function buildSingeItemModel(Employee $employee): EmployeeItemModel
    {
        $modelBuilder = $this->builderRegistry->getModelBuilder(EmployeeItemModel::class);

        return $modelBuilder->buildModel($employee);
    }
}
