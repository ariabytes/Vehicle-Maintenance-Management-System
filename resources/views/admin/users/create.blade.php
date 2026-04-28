<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Add Account</h2>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:underline">← Back to Accounts</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg border border-gray-200 p-6">

                @if($errors->any())
                    <div class="bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded mb-6">
                        <ul class="list-disc list-inside text-sm space-y-1">
                            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="first_name" value="First Name *" />
                            <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full"
                                        :value="old('first_name')" required />
                            <x-input-error :messages="$errors->get('first_name')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="middle_name" value="Middle Name" />
                            <x-text-input id="middle_name" name="middle_name" type="text" class="mt-1 block w-full"
                                        :value="old('middle_name')" />
                        </div>
                        <div>
                            <x-input-label for="last_name" value="Last Name *" />
                            <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full"
                                        :value="old('last_name')" required />
                            <x-input-error :messages="$errors->get('last_name')" class="mt-1" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="email" value="Email *" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                    :value="old('email')" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="role" value="Role *" />
                        <select id="role" name="role" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Select role...</option>
                            <option value="admin"    {{ old('role') === 'admin'    ? 'selected' : '' }}>Admin</option>
                            <option value="driver"   {{ old('role') === 'driver'   ? 'selected' : '' }}>Driver</option>
                            <option value="mechanic" {{ old('role') === 'mechanic' ? 'selected' : '' }}>Mechanic</option>
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-1" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="password" value="Password *" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('password')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="password_confirmation" value="Confirm Password *" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                        <a href="{{ route('admin.users.index') }}"
                        class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">Cancel</a>
                        <x-primary-button>Add Account</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>