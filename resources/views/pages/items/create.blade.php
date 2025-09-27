<x-layouts.app-layout>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-md overflow-hidden shadow-xl sm:rounded-lg border border-gray-100">
                <div class="p-6">
                    <div class="mb-6">
                        <a href="{{ route('items.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg inline-flex items-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                            </svg>
                            Back to Items
                        </a>
                        <h1 class="text-2xl font-bold text-gray-900">{{ isset($isEditing) && $isEditing ? 'Edit Item' : 'Create New Item' }}</h1>
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
                        <form action="{{ route('items.update', $item->id) }}" method="POST" class="space-y-6 p-5 bg-white/70 backdrop-blur-sm rounded-lg shadow-inner border border-white" id="itemForm">
                        @method('PUT')
                    @else
                        <form action="{{ route('items.store') }}" method="POST" class="space-y-6 p-5 bg-white/70 backdrop-blur-sm rounded-lg shadow-inner border border-white" id="itemForm">
                    @endif
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-forms.label for="item_type" value="Item Type" required="true" />
                                <x-forms.select name="item_type" id="item_type" required>
                                    <option value="">Select Item Type</option>
                                    <option value="Product" {{ old('item_type', isset($item) ? $item->item_type : '') == 'Product' ? 'selected' : '' }}>Product</option>
                                    <option value="Service" {{ old('item_type', isset($item) ? $item->item_type : '') == 'Service' ? 'selected' : '' }}>Service</option>
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-tags text-gray-500"></i>
                                    </x-slot>
                                </x-forms.select>
                                @error('item_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <x-forms.label for="item_hsn_code" value="HSN Code" required="true" />
                                <x-forms.input type="text" name="item_hsn_code" id="item_hsn_code" value="{{ old('item_hsn_code', isset($item) ? $item->item_hsn_code : '') }}" placeholder="Enter HSN Code" required pattern="[0-9]+" title="HSN Code must contain numbers only">
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-barcode text-gray-500"></i>
                                    </x-slot>
                                </x-forms.input>
                                <p class="hidden mt-1 text-sm text-red-600 hsn-validation-error">HSN Code must contain numbers only</p>
                                @error('item_hsn_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <x-forms.label for="client_id" value="Client" />
                            <x-forms.select name="client_id" id="client_id">
                                <option value="">Select Client (Optional)</option>
                                <!-- Client options will be populated here when the Client model is implemented -->
                                <x-slot name="afterIcon">
                                    <i class="fas fa-user-tie text-gray-500"></i>
                                </x-slot>
                            </x-forms.select>
                            @error('client_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-forms.label for="item_name" value="Item Name" required="true" />
                            <x-forms.input type="text" name="item_name" id="item_name" value="{{ old('item_name', isset($item) ? $item->item_name : '') }}" placeholder="Enter Item Name" required>
                                <x-slot name="afterIcon">
                                    <i class="fas fa-box text-gray-500"></i>
                                </x-slot>
                            </x-forms.input>
                            @error('item_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-forms.label for="item_description" value="Item Description" required="true" />
                            <x-forms.textarea name="item_description" id="item_description" rows="4" placeholder="Enter Item Description" required>
                                {{ old('item_description', isset($item) ? $item->item_description : '') }}
                                <x-slot name="afterIcon">
                                    <i class="fas fa-align-left text-gray-500"></i>
                                </x-slot>
                            </x-forms.textarea>
                            @error('item_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-forms.label for="item_price" value="Item Price" required="true" />
                            <div class="relative">
                                <x-forms.input type="text" name="item_price" id="item_price" value="{{ old('item_price', isset($item) ? $item->item_price : '') }}" placeholder="0.00" required pattern="^[0-9]+(\.[0-9]{1,2})?$" title="Please enter a valid price (numbers and decimal only)">
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-rupee-sign text-gray-500"></i>
                                    </x-slot>
                                </x-forms.input>
                                <p class="hidden mt-1 text-sm text-red-600 price-validation-error">Please enter a valid price (numbers and decimal only)</p>
                            </div>
                            @error('item_price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-forms.label for="item_sku" value="SKU" />
                            <x-forms.input type="text" name="item_sku" id="item_sku" value="{{ old('item_sku', isset($item) ? $item->item_sku : '') }}" placeholder="Enter SKU (Optional)">
                                <x-slot name="afterIcon">
                                    <i class="fas fa-fingerprint text-gray-500"></i>
                                </x-slot>
                            </x-forms.input>
                            @error('item_sku')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end pt-4">
                            <x-forms.button type="submit" class="bg-blue-600 hover:bg-blue-700">
                                {{ isset($isEditing) && $isEditing ? 'Update Item' : 'Create Item' }}
                            </x-forms.button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app-layout>
