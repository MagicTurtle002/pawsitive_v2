<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Registration System</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.12.0/cdn.min.js" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <!-- Modal -->
        <div id="petOwnerModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50" x-data="{
            petTypes: [
                { id: 1, name: 'Dog' },
                { id: 2, name: 'Cat' },
                { id: 3, name: 'Bird' },
                { id: 4, name: 'Reptile' }
            ],
            breeds: {
                1: [
                    { id: 1, name: 'Golden Retriever' },
                    { id: 2, name: 'German Shepherd' },
                    { id: 3, name: 'Labrador' },
                    { id: 4, name: 'Beagle' }
                ],
                2: [
                    { id: 5, name: 'Persian' },
                    { id: 6, name: 'Siamese' },
                    { id: 7, name: 'Maine Coon' },
                    { id: 8, name: 'Ragdoll' }
                ],
                3: [
                    { id: 9, name: 'Parakeet' },
                    { id: 10, name: 'Cockatiel' },
                    { id: 11, name: 'Canary' }
                ],
                4: [
                    { id: 12, name: 'Turtle' },
                    { id: 13, name: 'Snake' },
                    { id: 14, name: 'Lizard' }
                ]
            },
            selectedSpecies: '',
            selectedBreeds: [],
            calculateAge() {
                const birthday = document.getElementById('Birthday').value;
                if (!birthday) {
                    document.getElementById('CalculatedAge').value = '';
                    return;
                }
                
                const birthDate = new Date(birthday);
                const today = new Date();
                
                let years = today.getFullYear() - birthDate.getFullYear();
                let months = today.getMonth() - birthDate.getMonth();
                
                if (months < 0 || (months === 0 && today.getDate() < birthDate.getDate())) {
                    years--;
                    months += 12;
                }
                
                const result = years > 0 ? 
                    `${years} year${years !== 1 ? 's' : ''}` : '';
                const monthsText = months > 0 ? 
                    `${months} month${months !== 1 ? 's' : ''}` : '';
                
                document.getElementById('CalculatedAge').value = `${result}${result && monthsText ? ', ' : ''}${monthsText}`;
            },
            updateBreeds() {
                this.selectedBreeds = this.breeds[this.selectedSpecies] || [];
                document.getElementById('Breed').value = '';
            },
            confirmSubmission() {
                // Validate form
                const form = document.getElementById('ownerPetForm');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }
                
                // In a real implementation, you would handle the form submission here
                // For now, we'll just show an alert
                alert('Form submitted successfully!');
                
                // Close the modal
                document.getElementById('petOwnerModal').classList.add('hidden');
            }
        }">
            <div class="relative top-10 mx-auto p-5 w-full max-w-4xl">
                <div class="bg-white rounded-lg shadow-xl relative">
                    <!-- Close button -->
                    <button 
                        type="button"
                        class="absolute top-3 right-3 text-gray-500 hover:text-gray-700"
                        onclick="document.getElementById('petOwnerModal').classList.add('hidden')">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    
                    <!-- Form content -->
                    <div class="px-6 py-6">
                        <form id="ownerPetForm" class="space-y-6" novalidate>
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                            
                            <div>
                                <h2 class="text-2xl font-semibold text-gray-800">Register Pet Owner</h2>
                                <div class="mt-1 border-b border-gray-200"></div>
                            </div>
                            
                            <!-- Owner Information -->
                            <div>
                                <h3 class="text-xl font-medium text-gray-700">Owner Information</h3>
                                
                                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="FirstName" class="block text-sm font-medium text-gray-700">
                                            First Name<span class="text-red-600">*</span>
                                        </label>
                                        <input 
                                            type="text" 
                                            id="FirstName" 
                                            name="FirstName" 
                                            placeholder="Enter first name"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                            required
                                        >
                                    </div>
                                    
                                    <div>
                                        <label for="LastName" class="block text-sm font-medium text-gray-700">
                                            Last Name<span class="text-red-600">*</span>
                                        </label>
                                        <input 
                                            type="text" 
                                            id="LastName" 
                                            name="LastName" 
                                            placeholder="Enter last name"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                            required
                                        >
                                    </div>
                                    
                                    <div>
                                        <label for="Email" class="block text-sm font-medium text-gray-700">
                                            Email<span class="text-red-600">*</span>
                                        </label>
                                        <input 
                                            type="email" 
                                            id="Email" 
                                            name="Email" 
                                            placeholder="Enter email"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                            required
                                        >
                                    </div>
                                    
                                    <div>
                                        <label for="Phone" class="block text-sm font-medium text-gray-700">
                                            Phone Number<span class="text-red-600">*</span>
                                        </label>
                                        <input 
                                            type="text" 
                                            id="Phone" 
                                            name="Phone" 
                                            value="+63"
                                            placeholder="Enter phone number"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                            required
                                            maxlength="13"
                                            pattern="^\+63\d{10}$"
                                        >
                                        <p class="mt-1 text-xs text-gray-500">Format: +63 followed by 10 digits</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="border-b border-gray-200"></div>
                            
                            <!-- Pet Information -->
                            <div>
                                <h3 class="text-xl font-medium text-gray-700">Pet Information</h3>
                                
                                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="Name" class="block text-sm font-medium text-gray-700">
                                            Pet Name<span class="text-red-600">*</span>
                                        </label>
                                        <input 
                                            type="text" 
                                            id="Name" 
                                            name="Name" 
                                            placeholder="Enter pet name"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                            required
                                            minlength="3"
                                        >
                                    </div>
                                    
                                    <div>
                                        <label for="Gender" class="block text-sm font-medium text-gray-700">
                                            Gender<span class="text-red-600">*</span>
                                        </label>
                                        <select 
                                            id="Gender" 
                                            name="Gender"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                            required
                                        >
                                            <option value="">Select gender</option>
                                            <option value="1">Male</option>
                                            <option value="2">Female</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label for="SpeciesId" class="block text-sm font-medium text-gray-700">
                                            Pet Type<span class="text-red-600">*</span>
                                        </label>
                                        <select 
                                            id="SpeciesId" 
                                            name="SpeciesId"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                            required
                                            x-model="selectedSpecies"
                                            @change="updateBreeds"
                                        >
                                            <option value="">Select pet type</option>
                                            <template x-for="type in petTypes" :key="type.id">
                                                <option :value="type.id" x-text="type.name"></option>
                                            </template>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label for="Breed" class="block text-sm font-medium text-gray-700">
                                            Breed
                                        </label>
                                        <select 
                                            id="Breed" 
                                            name="Breed"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                        >
                                            <option value="">Select breed</option>
                                            <template x-for="breed in selectedBreeds" :key="breed.id">
                                                <option :value="breed.id" x-text="breed.name"></option>
                                            </template>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label for="Birthday" class="block text-sm font-medium text-gray-700">
                                            Birthday<span class="text-red-600">*</span>
                                        </label>
                                        <input 
                                            type="date" 
                                            id="Birthday" 
                                            name="Birthday" 
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                            required
                                            @change="calculateAge"
                                        >
                                    </div>
                                    
                                    <div>
                                        <label for="CalculatedAge" class="block text-sm font-medium text-gray-700">
                                            Calculated Age<span class="text-red-600">*</span>
                                        </label>
                                        <input 
                                            type="text" 
                                            id="CalculatedAge" 
                                            name="CalculatedAge" 
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50"
                                            readonly
                                        >
                                    </div>
                                    
                                    <div>
                                        <label for="Weight" class="block text-sm font-medium text-gray-700">
                                            Weight (kg)<span class="text-red-600">*</span>
                                        </label>
                                        <input 
                                            type="number" 
                                            id="Weight" 
                                            name="Weight" 
                                            placeholder="Enter weight (e.g., 10.5)"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                            required
                                            step="0.1"
                                            min="0"
                                            max="50"
                                        >
                                    </div>
                                    
                                    <div>
                                        <label for="LastVisit" class="block text-sm font-medium text-gray-700">
                                            Date of Last Visit
                                        </label>
                                        <input 
                                            type="date" 
                                            id="LastVisit" 
                                            name="LastVisit" 
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                        >
                                    </div>
                                </div>
                            </div>
                            
                            <div class="border-b border-gray-200"></div>
                            
                            <!-- Buttons -->
                            <div class="flex justify-end space-x-3">
                                <button 
                                    type="button"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    onclick="document.getElementById('petOwnerModal').classList.add('hidden')"
                                >
                                    Cancel
                                </button>
                                <button 
                                    type="button"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    @click="confirmSubmission"
                                >
                                    Add Owner and Pet
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>