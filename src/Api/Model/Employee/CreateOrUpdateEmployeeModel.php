<?php

declare(strict_types=1);

namespace App\Api\Model\Employee;

use App\Api\Interfaces\ResolvableModelInterface;
use App\Validator\NotPastDate;
use Symfony\Component\Validator\Constraints as Assert;

class CreateOrUpdateEmployeeModel implements ResolvableModelInterface
{
    #[Assert\NotBlank(message: 'Name cannot be blank.')]
    #[Assert\Type(type: 'string', message: 'Name should be string')]
    public string $name;
    #[Assert\NotBlank(message: 'Surname cannot be blank.')]
    #[Assert\Type(type: 'string', message: 'Surname should be string')]
    public string $surname;

    #[Assert\NotBlank(message: 'Email cannot be blank.')]
    #[Assert\Type(type: 'string', message: 'Email should be string')]
    #[Assert\Email(message: 'Please enter valid email')]
    public string $email;

    #[Assert\NotBlank(message: 'Current Salary Amount cannot be blank.')]
    #[Assert\GreaterThanOrEqual(value: 100, message: 'Current Salary Amount should be greater or equal 100')]
    public string $currentSalaryAmount;

    #[Assert\NotBlank(message: 'Hired time cannot be blank')]
    #[NotPastDate]
    public string $hiredAt;
}
