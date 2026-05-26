<x-layouts.app-layout>
    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="page-shell">
                <div class="page-inner">
                    <div class="page-header">
                        <div>
                            <h1 class="page-title">Attendance Management</h1>
                            <p class="page-subtitle">Create, review, and edit attendance boards for each payroll period.</p>
                        </div>

                        <!-- Create Attendance Board Button -->
                        <a href="{{ route('attendance.create') }}" class="btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Create Attendance Board
                        </a>
                    </div>

                    <!-- Success Message -->
                    @if(session('success'))
                        <div class="alert-success mb-6">
                            <div class="flex">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-400 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <p>{{ session('success') }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Error Message -->
                    @if(session('error'))
                        <div class="alert-error mb-6">
                            <div class="flex">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L10 11.414l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                                <p>{{ session('error') }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Attendance Periods Table -->
                    <div class="overflow-hidden rounded-lg border border-violet-500/20 bg-black/35 shadow-sm shadow-black/50">
                        <div class="border-b border-violet-500/20 px-6 py-4">
                            <h3 class="text-lg font-semibold text-white">Attendance Periods</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="app-table">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employees</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Working Days</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Salary</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">OT Hours</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Advances</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($attendances->isNotEmpty())
                                        @foreach($attendances as $attendance)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-white">
                                                        {{$attendance->attendance_month_year}}
                                                    </div>
                                                    <div class="text-xs text-violet-300/65">{{ DateTime::createFromFormat('Y-m-d H:i:s',$attendance->start_date)->format('d M Y') .' - ' . DateTime::createFromFormat('Y-m-d H:i:s',$attendance->end_date)->format('d M Y') }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                        {{ $attendance->employee_count }} employees
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-violet-100">
                                                    {{ $attendance->total_days }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">
                                                    ₹{{ number_format($attendance->total_salary_paid, 2) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-violet-100">
                                                    {{ $attendance->total_overtime_hours }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                                                    ₹{{ number_format($attendance->total_advance_paid, 2) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <a href="{{ route('attendance.show', $attendance->id) }}" class="btn-soft">Details</a>
                                                    <a href="{{ route('attendance.edit', $attendance->id) }}" class="btn-soft">Edit</a>
                                                    <form method="POST" action="{{ route('attendance.destroy', $attendance->id) }}" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this attendance board?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn-soft-danger">Delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="8" class="px-6 py-16 text-center">
                                                <div class="surface-muted mx-4 p-6">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto mb-4 h-12 w-12 text-violet-300/50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                    </svg>
                                                    <h3 class="mb-2 text-lg font-medium text-white">No Attendance Records Found</h3>
                                                    <p class="mb-4 text-violet-200/75">You haven't created any attendance boards yet.</p>
                                                    <a href="{{ route('attendance.create') }}" class="btn-primary inline-flex items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                        </svg>
                                                        Create Your First Attendance Board
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app-layout>

@push('scripts')
<script>
    // Auto-refresh functionality (optional)
    document.addEventListener('DOMContentLoaded', function() {
        // You can add any JavaScript functionality here
        console.log('Attendance summary page loaded');
    });
</script>
@endpush
