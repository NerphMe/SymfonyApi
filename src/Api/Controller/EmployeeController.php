<?php

namespace App\Api\Controller;

use App\Api\DataPersister\Doctrine\EmployeeDataPersister;
use App\Api\DataProvider\Doctrine\Employee\EmployeeDataProvider;
use App\Api\Model\Employee\CreateOrUpdateEmployeeModel;
use App\Api\ModelBuilder\Exception\DataProviderDoesNotExistException;
use App\Api\ModelBuilder\Exception\ModelBuilderDoesNotExistException;
use App\Entity\Employee;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', )]
class EmployeeController extends AbstractController
{
    public function __construct(
        private readonly EmployeeDataProvider $employeeDataProvider,
        private readonly EmployeeDataPersister $employeeDataPersister,
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/employee",
     *     summary="Get list of all employees",
     *
     *     @OA\Response(
     *         response=200,
     *         description="Returns the list of all employees",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref=@Model(type=Employee::class))
     *         )
     *     ),
     *
     *     @OA\Response(response=400, description="Bad request"),
     * )
     *
     * @throws DataProviderDoesNotExistException
     * @throws ModelBuilderDoesNotExistException
     */
    #[Route('/employee', name: 'get_all_employees', methods: 'GET')]
    public function getAllEmployees(): JsonResponse
    {
        $data = [];

        foreach ($this->employeeDataProvider->getAllEmployeesItems() as $employeeItem) {
            $data[] = $employeeItem;
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/employee/{employee}",
     *     summary="Get details of a single employee",
     *
     *     @OA\Parameter(
     *         name="employee",
     *         in="path",
     *         required=true,
     *         description="The unique identifier of the employee."
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Returns detailed information about one employee",
     *
     *         @OA\JsonContent(ref=@Model(type=Employee::class))
     *     ),
     *
     *     @OA\Response(response=404, description="Employee not found")
     * )
     *
     * @throws DataProviderDoesNotExistException
     * @throws ModelBuilderDoesNotExistException
     */
    #[Route('/employee/{employee}', name: 'get_employee', methods: 'GET')]
    public function getEmployee(Employee $employee): JsonResponse
    {
        $employee = $this->employeeDataProvider->getEmployee($employee);

        return new JsonResponse($employee, Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/employee",
     *     summary="Create a new employee",
     *
     *     @OA\RequestBody(
     *
     *         @OA\JsonContent(ref=@Model(type=CreateOrUpdateEmployeeModel::class))
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Employee created successfully",
     *
     *         @OA\JsonContent(ref=@Model(type=Employee::class))
     *     ),
     *
     *     @OA\Response(response=400, description="Invalid input"),
     *     @OA\Response(response=403, description="Forbidden"),
     * )
     *
     * @throws \Exception
     */
    #[Route('/employee', name: 'create_employee', methods: 'POST')]
    public function createEmployee(CreateOrUpdateEmployeeModel $model): JsonResponse
    {
        $employee = $this->employeeDataPersister->createEmployee($model);

        return new JsonResponse($employee, Response::HTTP_CREATED);
    }

    /**
     * @OA\Delete(
     *     path="/api/employee/{employee}",
     *     summary="Delete an employee",
     *
     *     @OA\Parameter(
     *         name="employee",
     *         in="path",
     *         required=true,
     *         description="The unique identifier of the employee."
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="Employee deleted successfully"
     *     ),
     *     @OA\Response(response=404, description="Employee not found"),
     *     @OA\Response(response=403, description="Forbidden"),
     * )
     *
     * @throws \Exception
     */
    #[Route('/employee/{employee}', name: 'delete_employee', methods: 'DELETE')]
    public function deleteEmployee(Employee $employee): JsonResponse
    {
        $this->employeeDataPersister->deleteEmployee($employee);

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\Put(
     *     path="/api/employee/{employee}",
     *     summary="Update an employee",
     *
     *     @OA\Parameter(
     *         name="employee",
     *         in="path",
     *         required=true,
     *         description="The unique identifier of the employee."
     *     ),
     *
     *     @OA\RequestBody(
     *
     *         @OA\JsonContent(ref=@Model(type=CreateOrUpdateEmployeeModel::class))
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Employee updated successfully",
     *
     *         @OA\JsonContent(ref=@Model(type=Employee::class))
     *     ),
     *
     *     @OA\Response(response=400, description="Invalid input"),
     *     @OA\Response(response=404, description="Employee not found"),
     *     @OA\Response(response=403, description="Forbidden"),
     * )
     *
     * @throws \Exception
     */
    #[Route('/employee/{employee}', name: 'update_employee', methods: 'PUT')]
    public function updateEmployee(Employee $employee, CreateOrUpdateEmployeeModel $model): JsonResponse
    {
        $employee = $this->employeeDataPersister->updateEmployee($employee, $model);

        return new JsonResponse($employee, Response::HTTP_OK);
    }
}
