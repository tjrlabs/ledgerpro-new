<x-layouts.app-layout>
    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-md overflow-hidden shadow-xl sm:rounded-lg border border-gray-100">
                <div class="p-6">
                    <div class="mb-6">
                        <a href="{{ route('employees.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg inline-flex items-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                            </svg>
                            Back to Employees
                        </a>
                        <h1 class="text-2xl font-bold text-gray-900">{{ isset($isEditing) && $isEditing ? 'Edit Employee' : 'Create New Employee' }}</h1>
                    </div>

                    @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
                        <h4 class="text-lg font-medium mb-2">Please correct the following errors:</h4>
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if(isset($isEditing) && $isEditing)
                        <form action="{{ route('employees.update', $employee->id) }}" method="POST" class="space-y-6 p-5 bg-white/70 backdrop-blur-sm rounded-lg shadow-inner border border-white" id="employeeForm">
                        @method('PUT')
                    @else
                        <form action="{{ route('employees.store') }}" method="POST" class="space-y-6 p-5 bg-white/70 backdrop-blur-sm rounded-lg shadow-inner border border-white" id="employeeForm">
                    @endif
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- First Name -->
                            <div>
                                <x-forms.label for="first_name" value="First Name" required="true" />
                                <x-forms.input type="text" name="first_name" id="first_name" value="{{ old('first_name', isset($employee) ? $employee->first_name : '') }}" placeholder="Enter First Name" required>
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-user text-gray-500"></i>
                                    </x-slot>
                                </x-forms.input>
                                @error('first_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Last Name -->
                            <div>
                                <x-forms.label for="last_name" value="Last Name" required="true" />
                                <x-forms.input type="text" name="last_name" id="last_name" value="{{ old('last_name', isset($employee) ? $employee->last_name : '') }}" placeholder="Enter Last Name" required>
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-user text-gray-500"></i>
                                    </x-slot>
                                </x-forms.input>
                                @error('last_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Gender -->
                            <div>
                                <x-forms.label for="gender" value="Gender" required="true" />
                                <x-forms.select name="gender" id="gender" required>
                                    <option value="">Select Gender</option>
                                    @foreach($genders as $gender)
                                        <option value="{{ $gender }}" {{ (old('gender') == $gender || (isset($employee) && $employee->gender == $gender)) ? 'selected' : '' }}>
                                            {{ ucfirst($gender) }}
                                        </option>
                                    @endforeach
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-venus-mars text-gray-500"></i>
                                    </x-slot>
                                </x-forms.select>
                                @error('gender')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Mobile Number -->
                            <div>
                                <x-forms.label for="mobile_number" value="Mobile Number" />
                                <x-forms.input type="text" name="mobile_number" id="mobile_number" value="{{ old('mobile_number', isset($employee) ? $employee->mobile_number : '') }}" placeholder="Enter Mobile Number" required>
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-phone text-gray-500"></i>
                                    </x-slot>
                                </x-forms.input>
                                @error('mobile_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Department -->
                            <div>
                                <x-forms.label for="department" value="Department" required="true" />
                                <x-forms.select name="department" id="department" required>
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department }}" {{ (old('department') == $department || (isset($employee) && $employee->department == $department)) ? 'selected' : '' }}>
                                            {{ $department }}
                                        </option>
                                    @endforeach
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-building text-gray-500"></i>
                                    </x-slot>
                                </x-forms.select>
                                @error('department')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Designation -->
                            <div>
                                <x-forms.label for="designation" value="Designation" />
                                <x-forms.input type="text" name="designation" id="designation" value="{{ old('designation', isset($employee) ? $employee->designation : '') }}" placeholder="Enter Designation">
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-briefcase text-gray-500"></i>
                                    </x-slot>
                                </x-forms.input>
                                @error('designation')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 {{ (isset($isEditing) && $isEditing) ? 'md:grid-cols-1' : 'md:grid-cols-2' }} gap-6">
                            <!-- Salary -->
                            @if(!isset($isEditing) || !$isEditing)
                                <div>
                                    <x-forms.label for="salary" value="Monthly Salary (₹)" required="true" />
                                    <x-forms.input type="number" name="salary" id="salary" step="1" min="0" value="{{ old('salary', isset($employee) ? $employee->salary : '') }}" placeholder="0" required>
                                        <x-slot name="afterIcon">
                                            <i class="fas fa-rupee-sign text-gray-500"></i>
                                        </x-slot>
                                    </x-forms.input>
                                    @error('salary')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif

                            <!-- Salary Hours -->
                            <div>
                                <x-forms.label for="salary_hours" value="Working Hours/Day" />
                                <x-forms.input type="number" name="salary_hours" id="salary_hours" step="1" min="1" max="24" value="{{ old('salary_hours', isset($employee) ? $employee->salary_hours : '8') }}" placeholder="8">
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-clock text-gray-500"></i>
                                    </x-slot>
                                </x-forms.input>
                                @error('salary_hours')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Joining Date -->
                            <div>
                                <x-forms.label for="joining_date" value="Joining Date" required="true" />
                                <x-forms.input type="text" name="joining_date" id="joining_date" class="datepicker" value="{{ old('joining_date', isset($employee) ? $employee->joining_date : '') }}" placeholder="Select Joining Date" required>
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-calendar text-gray-500"></i>
                                    </x-slot>
                                </x-forms.input>
                                @error('joining_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <x-forms.label for="status" value="Status" required="true" />
                                <x-forms.select name="status" id="status" required>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}" {{ (old('status', 'active') == $status || (isset($employee) && $employee->status == $status)) ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-toggle-on text-gray-500"></i>
                                    </x-slot>
                                </x-forms.select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Leaving Date (only show for inactive employees) -->
                        <div id="leaving_date_container" class="grid grid-cols-1 md:grid-cols-2 gap-6" style="display: none;">
                            <div>
                                <x-forms.label for="leaving_date" value="Leaving Date" />
                                <x-forms.input type="text" name="leaving_date" id="leaving_date" class="datepicker" value="{{ old('leaving_date', isset($employee) ? $employee->leaving_date : '') }}" placeholder="Select Leaving Date">
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-calendar-times text-gray-500"></i>
                                    </x-slot>
                                </x-forms.input>
                                @error('leaving_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <x-forms.button type="submit" class="bg-blue-600 hover:bg-blue-700">
                                {{ isset($isEditing) && $isEditing ? 'Update Employee' : 'Create Employee' }}
                            </x-forms.button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery UI Datepicker CDN -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <script>
        jQuery(document).ready(function($) {
            // Initialize datepicker
            if (typeof $.fn.datepicker === 'function') {
                $(".datepicker").datepicker({
                    dateFormat: 'yy-mm-dd',
                    changeMonth: true,
                    changeYear: true,
                    yearRange: "1950:c+0"
                });

                // Set maxDate for joining date (today)
                $("#joining_date").datepicker("option", "maxDate", 0);
            }

            // Handle status changes to show/hide leaving date
            function handleStatusChange() {
                const status = $('#status').val();
                if (status === 'inactive') {
                    $('#leaving_date_container').show();
                    $('#leaving_date').attr('required', true);
                } else {
                    $('#leaving_date_container').hide();
                    $('#leaving_date').attr('required', false).val('');
                }
            }

            // Trigger status change handler
            $('#status').on('change', function() {
                handleStatusChange();
            });

            // Initial setup
            handleStatusChange();

            // Mobile number validation (Indian format)
            $('#mobile_number').on('input', function() {
                let value = $(this).val().replace(/\D/g, ''); // Remove non-digits
                if (value.length > 10) {
                    value = value.substring(0, 10);
                }
                $(this).val(value);
            });

            // Salary formatting
            $('#salary').on('input', function() {
                let value = $(this).val().replace(/\D/g, ''); // Remove non-digits
                $(this).val(value);
            });
        });
    </script>
</x-layouts.app-layout>
