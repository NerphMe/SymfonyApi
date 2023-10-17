<?php

declare(strict_types=1);

namespace App\Api\ModelBuilder\Doctrine;

use App\Api\Model\Employee\EmployeeItemModel;
use App\Api\ModelBuilder\ModelBuilderInterface;
use App\Entity\Employee;

class EmployeeItemModelBuilder implements ModelBuilderInterface
{
    public function buildModel(Employee $employee): EmployeeItemModel
    {
        $employeeItemModel = new EmployeeItemModel();

        $employeeItemModel->id = $employee->getId();
        $employeeItemModel->name = $employee->getName();
        $employeeItemModel->surname = $employee->getSurname();
        $employeeItemModel->email = $employee->getEmail();
        $employeeItemModel->currentSalaryAmount = $employee->getCurrentSalaryAmount();
        $employeeItemModel->hiredAt = $employee->getHiredAt()->format('Y-m-d H:i:s');
        $employeeItemModel->createdAt = $employee->getCreatedAt()->format('Y-m-d H:i:s');
        $employeeItemModel->updatedAt = $employee->getUpdatedAt()?->format('Y-m-d H:i:s');

        return $employeeItemModel;
    }

    public function getModelClass(): string
    {
        return EmployeeItemModel::class;
    }

    public function getProviderName(): string
    {
        return 'doctrine';
    }
}
