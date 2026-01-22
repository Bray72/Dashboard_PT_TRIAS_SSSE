@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-green-900">Import Work Permits</h1>
        <p class="text-gray-600 mt-2">Bulk import work permit data from CSV file</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Import Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg p-6 mb-8 border border-green-600 shadow-lg">
                <form id="importForm" method="POST" action="{{ route('import.work-permit.process') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Company *</label>
                        <select name="company_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            <option value="">-- Select Company --</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                        @error('company_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">CSV File *</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-green-500 transition"
                             onclick="document.getElementById('csvFile').click()">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-600">
                                <span class="font-semibold">Click to upload</span> or drag and drop
                            </p>
                            <p class="text-xs text-gray-500">CSV files only</p>
                        </div>
                        <input type="file" id="csvFile" name="file" accept=".csv" required class="hidden"
                               onchange="handleFileSelect(this)">
                        <p class="text-sm text-gray-500 mt-2">Selected: <span id="fileName">None</span></p>
                        @error('file')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                            <span class="flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                </svg>
                                Import Data
                            </span>
                        </button>
                        <a href="{{ route('import.work-permit.sample') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Sample
                        </a>
                    </div>
                </form>

                <!-- Success Message -->
                <div id="successMessage" class="mt-6 hidden bg-green-100 border-l-4 border-green-500 text-green-700 p-4">
                    <p class="font-bold">Success!</p>
                    <p id="successText"></p>
                </div>

                <!-- Error Message -->
                <div id="errorMessage" class="mt-6 hidden bg-red-100 border-l-4 border-red-500 text-red-700 p-4">
                    <p class="font-bold">Error!</p>
                    <p id="errorText"></p>
                </div>

                <!-- Errors List -->
                <div id="errorsList" class="mt-6 hidden">
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800 p-4">
                        <p class="font-bold">Import Warnings/Errors:</p>
                        <ul id="errorsUl" class="mt-2 text-sm list-disc list-inside"></ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Information Panel -->
        <div class="lg:col-span-1">
            <div class="bg-green-50 rounded-lg p-6 border border-green-200">
                <h3 class="text-lg font-semibold text-green-900 mb-4">CSV Format Requirements</h3>
                
                <div class="space-y-4 text-sm">
                    <div>
                        <p class="font-semibold text-green-900 mb-2">Required Columns:</p>
                        <ul class="list-disc list-inside text-gray-700 space-y-1">
                            <li>Permit Type ID</li>
                            <li>Month (1-12)</li>
                            <li>Year (e.g., 2024)</li>
                            <li>Total (permit count)</li>
                        </ul>
                    </div>

                    <div>
                        <p class="font-semibold text-green-900 mb-2">Permit Types:</p>
                        <ul class="text-gray-700 space-y-1">
                            @foreach($permitTypes as $type)
                                <li><strong>{{ $type->id }}:</strong> {{ $type->name }}</li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="bg-white p-3 rounded border border-green-100">
                        <p class="font-semibold text-gray-900 mb-2">Example Row:</p>
                        <p class="text-gray-600 text-xs font-mono">
                            1, 1, 2024, 10
                        </p>
                    </div>

                    <div class="bg-green-100 p-3 rounded">
                        <p class="text-xs text-green-900">
                            💡 Download the sample CSV file to see the correct format.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function handleFileSelect(input) {
    const fileName = input.files[0]?.name || 'None';
    document.getElementById('fileName').textContent = fileName;
}

document.getElementById('importForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    
    try {
        const response = await fetch(this.action, {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        // Clear previous messages
        document.getElementById('successMessage').classList.add('hidden');
        document.getElementById('errorMessage').classList.add('hidden');
        document.getElementById('errorsList').classList.add('hidden');

        if (data.success) {
            document.getElementById('successText').textContent = `${data.message} (${data.imported} records imported)`;
            document.getElementById('successMessage').classList.remove('hidden');

            if (data.errors.length > 0) {
                const ul = document.getElementById('errorsUl');
                ul.innerHTML = '';
                data.errors.forEach(error => {
                    const li = document.createElement('li');
                    li.textContent = error;
                    ul.appendChild(li);
                });
                document.getElementById('errorsList').classList.remove('hidden');
            }

            // Reset form
            this.reset();
            document.getElementById('fileName').textContent = 'None';
        } else {
            document.getElementById('errorText').textContent = data.message;
            document.getElementById('errorMessage').classList.remove('hidden');
        }
    } catch (error) {
        document.getElementById('errorText').textContent = 'Network error: ' + error.message;
        document.getElementById('errorMessage').classList.remove('hidden');
    }
});
</script>
@endsection
