<?php

namespace App\Tests;

use App\Entity\Employee;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class EmployeeControllerTest extends WebTestCase
{
    public function testGetEmployees(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/employee');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseContent = $client->getResponse()->getContent();
        $employees = json_decode($responseContent, true);

        $this->assertIsArray($employees);

        if (count($employees) > 0) {
            $this->assertArrayHasKey('id', $employees[0]);
            $this->assertArrayHasKey('name', $employees[0]);
            $this->assertArrayHasKey('surname', $employees[0]);
            $this->assertArrayHasKey('email', $employees[0]);
            $this->assertArrayHasKey('currentSalaryAmount', $employees[0]);
            $this->assertArrayHasKey('hiredAt', $employees[0]);
            $this->assertArrayHasKey('createdAt', $employees[0]);
            $this->assertArrayHasKey('updatedAt', $employees[0]);
        } else {
            $this->assertEmpty($employees);
        }
    }

    public function testGetEmployee(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        $entityManager = $container->get('doctrine')->getManager();
        $employee = $entityManager->getRepository(Employee::class)->findAll();

        $validEmployee = $employee[0]->getId();
        $invalidEmployee = 1355123123;

        $client->request('GET', "/api/employee/{$validEmployee}");

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseContent = $client->getResponse()->getContent();
        $employee = json_decode($responseContent, true);

        $this->assertIsArray($employee);
        $this->assertArrayHasKey('id', $employee);
        $this->assertArrayHasKey('name', $employee);
        $this->assertArrayHasKey('surname', $employee);
        $this->assertArrayHasKey('email', $employee);
        $this->assertArrayHasKey('currentSalaryAmount', $employee);
        $this->assertArrayHasKey('hiredAt', $employee);
        $this->assertArrayHasKey('createdAt', $employee);
        $this->assertArrayHasKey('updatedAt', $employee);

        $client->request('GET', "/api/employee/{$invalidEmployee}");

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /**
     * @throws \Exception
     */
    public function testCreateEmployeeSuccess(): void
    {
        $client = static::createClient();
        $data = [
            'name' => 'John',
            'surname' => 'Doe',
            'email' => 'john.doe'.random_int(1, 99999).'@example.com',
            'currentSalaryAmount' => '5000' ,
            'hiredAt' => '2023-10-18 14:22:38'
        ];

        $client->request('POST', '/api/employee', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $responseContent = $client->getResponse()->getContent();
        $responseData = json_decode($responseContent, true);

        $this->assertIsArray($responseData);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertArrayHasKey('name', $responseData);
        $this->assertArrayHasKey('surname', $responseData);
        $this->assertArrayHasKey('email', $responseData);
        $this->assertArrayHasKey('currentSalaryAmount', $responseData);
        $this->assertArrayHasKey('hiredAt', $responseData);
        $this->assertArrayHasKey('createdAt', $responseData);
        $this->assertArrayHasKey('updatedAt', $responseData);
    }

    public function testCreateEmployeeValidationEmptyFieldsError(): void
    {
        $client = static::createClient();
        $data = [
            'name' => '',
            'surname' => '',
            'email' => '',
            'currentSalaryAmount' => '',
            'hiredAt' => '',
        ];

        $client->request('POST', '/api/employee', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $responseContent = $client->getResponse()->getContent();
        $responseData = json_decode($responseContent, true);

        $this->assertIsArray($responseData);
        $this->assertArrayHasKey('errors', $responseData);

        $expectedErrors = [
            ['field' => 'name', 'errors' => ['Name cannot be blank.']],
            ['field' => 'surname', 'errors' => ['Surname cannot be blank.']],
            ['field' => 'email', 'errors' => ['Email cannot be blank.']],
            ['field' => 'currentSalaryAmount', 'errors' => ['Current Salary Amount cannot be blank.', 'Current Salary Amount should be greater or equal 100']],
            ['field' => 'hiredAt', 'errors' => ['Hired time cannot be blank']],
        ];

        foreach ($expectedErrors as $expectedError) {
            $this->assertContains($expectedError, $responseData['errors']);
        }
    }

    public function testCreateEmployeeValidationPastDateError(): void
    {
        $client = static::createClient();
        $data = [
            'name' => 'John',
            'surname' => 'Doe',
            'email' => 'john.doe@example.com',
            'currentSalaryAmount' => '5000',
            'hiredAt' => '2023-10-16 14:22:38',
        ];

        $client->request('POST', '/api/employee', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $responseContent = $client->getResponse()->getContent();
        $responseData = json_decode($responseContent, true);

        $this->assertIsArray($responseData);
        $this->assertArrayHasKey('errors', $responseData);
        $expectedErrors = [
            [
                'field' => 'hiredAt',
                'errors' => ['The date "2023-10-16 14:22:38" is in the past, which is not allowed.']
            ],
        ];

        $this->assertEquals($expectedErrors, $responseData['errors']);
    }

    public function testUpdateEmployeeSuccess(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        $entityManager = $container->get('doctrine')->getManager();
        $employee = $entityManager->getRepository(Employee::class)->findAll();

        $validEmployee = $employee[0]->getId();
        $updatedData = [
            'name' => 'Updated John',
            'surname' => 'Updated Doe',
            'email' => 'updated.john.doe@example.com',
            'currentSalaryAmount' => '7000',
            'hiredAt' => '2023-10-18 14:22:38',
        ];

        $client->request('PUT', '/api/employee/'.$validEmployee, [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($updatedData));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $responseContent = $client->getResponse()->getContent();
        $responseData = json_decode($responseContent, true);

        $this->assertIsArray($responseData);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertArrayHasKey('name', $responseData);
        $this->assertArrayHasKey('surname', $responseData);
        $this->assertArrayHasKey('email', $responseData);
        $this->assertArrayHasKey('currentSalaryAmount', $responseData);
        $this->assertArrayHasKey('hiredAt', $responseData);
        $this->assertArrayHasKey('createdAt', $responseData);
        $this->assertArrayHasKey('updatedAt', $responseData);

        $this->assertEquals($updatedData['name'], $responseData['name']);
        $this->assertEquals($updatedData['surname'], $responseData['surname']);
        $this->assertEquals($updatedData['email'], $responseData['email']);
        $this->assertEquals($updatedData['currentSalaryAmount'], $responseData['currentSalaryAmount']);
        $this->assertEquals($updatedData['hiredAt'], $responseData['hiredAt']);
    }

    public function testUpdateEmployeeValidationErrors(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        $entityManager = $container->get('doctrine')->getManager();
        $employee = $entityManager->getRepository(Employee::class)->findAll();

        $validEmployee = $employee[0]->getId();
        $invalidData = [
            'name' => '',
            'surname' => '',
            'email' => '',
            'currentSalaryAmount' => '',
            'hiredAt' => '',
        ];

        $client->request('PUT', '/api/employee/'.$validEmployee, [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($invalidData));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $responseContent = $client->getResponse()->getContent();
        $responseData = json_decode($responseContent, true);

        $this->assertIsArray($responseData);
        $this->assertArrayHasKey('errors', $responseData);

        $expectedErrors = [
            ['field' => 'name', 'errors' => ['Name cannot be blank.']],
            ['field' => 'surname', 'errors' => ['Surname cannot be blank.']],
            ['field' => 'email', 'errors' => ['Email cannot be blank.']],
            ['field' => 'currentSalaryAmount', 'errors' => ['Current Salary Amount cannot be blank.', 'Current Salary Amount should be greater or equal 100']],
            ['field' => 'hiredAt', 'errors' => ['Hired time cannot be blank']],
        ];

        foreach ($expectedErrors as $expectedError) {
            $this->assertContains($expectedError, $responseData['errors']);
        }
    }

    public function testDeleteEmployeeSuccess(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        $entityManager = $container->get('doctrine')->getManager();
        $employee = $entityManager->getRepository(Employee::class)->findAll();

        $validEmployee = $employee[0]->getId();
        $client->request('DELETE', '/api/employee/'.$validEmployee);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function testDeleteEmployeeNotFound(): void
    {
        $client = static::createClient();

        $client->request('DELETE', '/api/employee/999942353523');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
