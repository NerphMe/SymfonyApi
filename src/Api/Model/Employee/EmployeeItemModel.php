<?php

declare(strict_types=1);

namespace App\Api\Model\Employee;

class EmployeeItemModel
{
    public int $id;

    public string $name;
    public string $surname;
    public string $email;
    public string $currentSalaryAmount;
    public string $hiredAt;
    public string $createdAt;
    public ?string $updatedAt;
}
