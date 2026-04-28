<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manage Accounts</h2>
            <a href="{{ route('admin.users.create') }}"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-md hover:bg-blue-700">
                + Add Account
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="bg-green-50 border border-green-300 text-green-800 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded">{{ session('error') }}</div>
            @endif

            {{-- Filters --}}
            <form method="GET" class="flex gap-3 flex-wrap">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search name or email..."
                    class="border border-gray-300 rounded-md px-3 pl-3 pr-10 py-2 text-sm w-56 focus:outline-none focus:ring-1 focus:ring-blue-500">
                <select name="role" class="border border-gray-300 rounded-md px-3 pl-3 pr-10 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">All Roles</option>
                    <option value="admin"    {{ request('role') === 'admin'    ? 'selected' : '' }}>Admin</option>
                    <option value="driver"   {{ request('role') === 'driver'   ? 'selected' : '' }}>Driver</option>
                    <option value="mechanic" {{ request('role') === 'mechanic' ? 'selected' : '' }}>Mechanic</option>
                </select>
                <select name="status" class="border border-gray-300 rounded-md px-3 pl-3 pr-10 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm rounded-md hover:bg-gray-700">Filter</button>
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:underline self-center">Reset</a>
            </form>

            {{-- Table --}}
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $user->full_name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                                <td class="px-4 py-3">
                                    @php $roleColor = ['admin'=>'bg-purple-100 text-purple-700','driver'=>'bg-blue-100 text-blue-700','mechanic'=>'bg-cyan-100 text-cyan-700'][$user->role] ?? 'bg-gray-100 text-gray-700'; @endphp
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $roleColor }}">{{ ucfirst($user->role) }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $user->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                        class="text-xs px-3 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 font-medium">Edit</a>
                                        <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}"
                                            onsubmit="return confirm('Toggle account status?')">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                class="text-xs px-3 py-1 rounded font-medium {{ $user->status === 'active' ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                                                {{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">No accounts found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-4 py-3 border-t border-gray-100 text-sm text-gray-500 flex items-center justify-between">
                    <span>Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }} records</span>
                    {{ $users->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>