<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Repositories\EmployeeRepository;
use App\DTO\Employee\ManageEmployeeDTO;
use App\Classes\ErrorData;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class EmployeeController extends Controller
{
    protected $employeeRepository;

    public function __construct(EmployeeRepository $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    /**
     * Display a listing of employees with filtering capabilities
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Prepare filters for the repository
        $filters = [];

        // Filter by status (active/inactive) - default to active if no status specified
        if ($request->has('status') && !empty($request->status)) {
            $filters['status'] = $request->status;
        } else {
            // Default to active employees when no status filter is applied
            $filters['status'] = 'active';
        }

        // Filter by department
        if ($request->has('department') && !empty($request->department)) {
            $filters['department'] = $request->department;
        }

        // Filter by gender
        if ($request->has('gender') && !empty($request->gender)) {
            $filters['gender'] = $request->gender;
        }

        // Search by name or mobile number
        if ($request->has('search') && !empty($request->search)) {
            $filters['search'] = $request->search;
        }

        // Filter by joining date range
        if ($request->has('joining_from') && !empty($request->joining_from)) {
            $filters['joining_from'] = $request->joining_from;
        }

        if ($request->has('joining_to') && !empty($request->joining_to)) {
            $filters['joining_to'] = $request->joining_to;
        }

        // Use the repository to get employee data and statistics
        $employees = $this->employeeRepository->getAllEmployees($filters);
        $departments = $this->employeeRepository->getDistinctDepartments();
        $statistics = $this->employeeRepository->getEmployeeStatistics($filters);

        $statuses = ['active', 'inactive'];
        $genders = ['male', 'female'];

        return view('pages.employees.index', [
            'employees' => $employees,
            'departments' => $departments,
            'statuses' => $statuses,
            'genders' => $genders,
            'totalEmployees' => $statistics['total_employees'],
            'activeEmployees' => $statistics['active_employees'],
            'inactiveEmployees' => $statistics['inactive_employees'],
            'totalMonthlySalary' => $statistics['total_monthly_salary'],
            'currentFilters' => $request->all()
        ]);
    }

    /**
     * Show the form for creating a new employee.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $departments = ['MI', 'SMT', 'Other'];
        $genders = ['male', 'female'];
        $statuses = ['active', 'inactive'];

        return view('pages.employees.create', [
            'departments' => $departments,
            'genders' => $genders,
            'statuses' => $statuses
        ]);
    }

    /**
     * Store a newly created employee in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $employeeDTO = ManageEmployeeDTO::from($request->all());
        if ($employeeDTO instanceof ErrorData) {
            return redirect()->back()->withErrors($employeeDTO->getErrorMessages())->withInput();
        }

        $response = $this->employeeRepository->storeEmployee($employeeDTO);
        if ($response instanceof ErrorData) {
            return redirect()->back()->withErrors($response->getErrorMessages())->withInput();
        }

        return redirect()->route('employees.index')->with('success', 'Employee created successfully!');
    }

    /**
     * Show the form for editing the specified employee.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit(int $id)
    {
        $employee = $this->employeeRepository->findEmployee($id);

        if (!$employee) {
            return redirect()->route('employees.index')->with('error', 'Employee not found.');
        }

        $departments = ['MI', 'SMT', 'Other'];
        $genders = ['male', 'female'];
        $statuses = ['active', 'inactive'];

        return view('pages.employees.create', [
            'employee' => $employee,
            'departments' => $departments,
            'genders' => $genders,
            'statuses' => $statuses,
            'isEditing' => true
        ]);
    }

    /**
     * Update the specified employee in storage.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        // Add employee_id to request data for DTO validation
        $requestData = $request->all();
        $requestData['employee_id'] = $id;

        $employeeDTO = ManageEmployeeDTO::from($requestData);
        if ($employeeDTO instanceof ErrorData) {
            return redirect()->back()->withErrors($employeeDTO->getErrorMessages())->withInput();
        }

        $response = $this->employeeRepository->updateEmployee($id, $employeeDTO);
        if ($response instanceof ErrorData) {
            return redirect()->back()->withErrors($response->getErrorMessages())->withInput();
        }

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully!');
    }

    /**
     * Display the salary management page for a specific employee
     *
     * @param int $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function salary(int $id)
    {
        $employee = $this->employeeRepository->findEmployee($id);

        if (!$employee) {
            return redirect()->route('employees.index')->with('error', 'Employee not found.');
        }

        // Get salary history for this employee
        $salaryHistory = $this->employeeRepository->getEmployeeSalaryHistory($id);

        return view('pages.employees.salary', compact('employee', 'salaryHistory'));
    }

    /**
     * Update employee salary
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function updateSalary(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'new_salary' => 'required|integer|min:0',
            'effective_date' => 'required|date',
        ]);

        $employee = $this->employeeRepository->findEmployee($id);

        if (!$employee) {
            return redirect()->route('employees.index')->with('error', 'Employee not found.');
        }

        $response = $this->employeeRepository->updateEmployeeSalary(
            $id,
            $request->new_salary,
            $request->effective_date
        );

        if ($response instanceof ErrorData) {
            return redirect()->back()->withErrors($response->getErrorMessages())->withInput();
        }

        return redirect()->route('employees.salary', $id)->with('success', 'Employee salary updated successfully!');
    }

    /**
     * Remove the specified employee from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $employee = $this->employeeRepository->findEmployee($id);

        if (!$employee) {
            return redirect()->route('employees.index')->with('error', 'Employee not found.');
        }

        $response = $this->employeeRepository->deleteEmployee($id);

        if ($response instanceof ErrorData) {
            return redirect()->back()->withErrors($response->getErrorMessages());
        }

        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully!');
    }

    /**
     * Pay advance amount to employee
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function payAdvance(Request $request, int $id): \Illuminate\Http\JsonResponse
    {
        // Validate the request
        $request->validate([
            'advance_amount' => 'required|numeric|min:1|max:50000',
            'reason' => 'nullable|string|max:255'
        ]);

        // Find the employee
        $employee = $this->employeeRepository->findEmployee($id);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found.',
                'data' => null
            ], 404);
        }

        // Process the advance payment through repository
        $response = $this->employeeRepository->payAdvanceToEmployee(
            $id,
            $request->advance_amount,
            $request->reason
        );

        if ($response instanceof ErrorData) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process advance payment.',
                'errors' => $response->getErrorMessages(),
                'data' => null
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Advance payment processed successfully.',
            'data' => [
                'updated_advance_due' => $response // Assuming repository returns updated advance_due amount
            ]
        ], 200);
    }
}
