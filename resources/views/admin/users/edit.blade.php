<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Account — {{ $user->full_name }}</h2>
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

                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
                    @csrf @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="first_name" value="First Name *" />
                            <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full"
                                        :value="old('first_name', $user->first_name)" required />
                            <x-input-error :messages="$errors->get('first_name')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="middle_name" value="Middle Name" />
                            <x-text-input id="middle_name" name="middle_name" type="text" class="mt-1 block w-full"
                                        :value="old('middle_name', $user->middle_name)" />
                        </div>
                        <div>
                            <x-input-label for="last_name" value="Last Name *" />
                            <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full"
                                        :value="old('last_name', $user->last_name)" required />
                            <x-input-error :messages="$errors->get('last_name')" class="mt-1" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="email" value="Email *" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                      :value="old('email', $user->email)" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="role" value="Role *" />
                        <select id="role" name="role" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="admin"    {{ old('role', $user->role) === 'admin'    ? 'selected' : '' }}>Admin</option>
                            <option value="driver"   {{ old('role', $user->role) === 'driver'   ? 'selected' : '' }}>Driver</option>
                            <option value="mechanic" {{ old('role', $user->role) === 'mechanic' ? 'selected' : '' }}>Mechanic</option>
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="password" value="New Password (leave blank to keep current)" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="password_confirmation" value="Confirm New Password" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" />
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                        {{-- Deactivate button on the left --}}
                        @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}"
                                onsubmit="return confirm('Toggle this account\'s status?')">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="px-4 py-2 text-sm font-semibold rounded-md {{ $user->status === 'active' ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                                    {{ $user->status === 'active' ? 'Deactivate Account' : 'Activate Account' }}
                                </button>
                            </form>
                        @else
                            <span></span>
                        @endif

                        <div class="flex gap-3">
                            <a href="{{ route('admin.users.index') }}"
                            class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">Cancel</a>
                            <x-primary-button>Save Changes</x-primary-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>