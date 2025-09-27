<x-layouts.app-layout>
    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-md overflow-hidden shadow-xl sm:rounded-lg border border-gray-100">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold">Clients</h1>
                        <a href="{{ route('clients.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-sm flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add New
                        </a>
                    </div>

                    @if(session('success'))
                    <div id="success-alert" class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 flex justify-between items-center">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ session('success') }}
                        </div>
                        <button type="button" onclick="document.getElementById('success-alert').style.display='none'" class="text-green-700 hover:text-green-900">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    @endif

                    <div class="overflow-x-auto bg-white/70 backdrop-blur-sm p-4 rounded-lg shadow-inner border border-white">
                        <table class="min-w-full bg-transparent">
                            <thead>
                                <tr class="bg-gray-100 text-gray-700 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">Client Name</th>
                                    <th class="py-3 px-6 text-left">Display Name</th>
                                    <th class="py-3 px-6 text-left">Contact</th>
                                    <th class="py-3 px-6 text-left">Type</th>
                                    <th class="py-3 px-6 text-left">Tax Number</th>
                                    <th class="py-3 px-6 text-center">Status</th>
                                    <th class="py-3 px-6 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm">
                                @forelse ($clients as $client)
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="py-3 px-6">{{ $client->client_name }}</td>
                                        <td class="py-3 px-6">{{ $client->display_name }}</td>
                                        <td class="py-3 px-6">
                                           Email: {{ $client->client_email ?? 'NA' }} <br>
                                           Phone: {{ $client->client_phone ?? 'NA' }}
                                        </td>
                                        <td class="py-3 px-6">{{ $client->client_type }}</td>
                                        <td class="py-3 px-6">{{ $client->client_tax_number }}</td>
                                        <td class="py-3 px-6 text-center">
                                            @if($client->is_active)
                                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Active</span>
                                            @else
                                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-6 text-center">
                                            <div class="flex item-center justify-center gap-2">
                                                <a href="{{ route('clients.edit', $client->id) }}" class="bg-blue-100 text-blue-600 hover:bg-blue-200 px-3 py-1 rounded-md inline-flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    Edit
                                                </a>
                                                <button type="button" onclick="confirmDelete({{ $client->id }}, '{{ $client->client_name }}')" class="bg-red-100 text-red-600 hover:bg-red-200 px-3 py-1 rounded-md inline-flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-6 text-center text-gray-400 text-base">
                                            No clients found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(id, name) {
            if (confirm('Are you sure you want to delete client "' + name + '"?')) {
                // Proceed with the deletion
                document.getElementById('delete-form-' + id).submit();
            }
        }
    </script>

    @foreach ($clients as $client)
        <form id="delete-form-{{ $client->id }}" action="{{ route('clients.destroy', $client->id) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
</x-layouts.app-layout>
