<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ 
                        auth()->user()->isAdmin() ? route('admin.dashboard') : 
                        (auth()->user()->isDriver() ? route('driver.dashboard') : route('mechanic.dashboard')) 
                    }}" class="font-black text-lg tracking-tight text-gray-800">
                        VM<span class="text-blue-600">M</span>S
                    </a>
                </div>

                <!-- Nav Links -->
                <div class="hidden space-x-6 sm:-my-px sm:ms-6 sm:flex">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">Dashboard</x-nav-link>
                            <x-nav-link :href="route('admin.vehicles.index')" :active="request()->routeIs('admin.vehicles.*')">Vehicles</x-nav-link>
                            <x-nav-link :href="route('admin.maintenance-types.index')" :active="request()->routeIs('admin.maintenance-types.*')">Maintenance Types</x-nav-link>
                            <x-nav-link :href="route('admin.schedules.index')" :active="request()->routeIs('admin.schedules.*')">PMS Schedules</x-nav-link>
                            <x-nav-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.*')">Reports</x-nav-link>
                            <x-nav-link :href="route('admin.jobs.index')" :active="request()->routeIs('admin.jobs.*')">Jobs</x-nav-link>
                            <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">Accounts</x-nav-link>
                            <x-nav-link :href="route('admin.maintenance-history')" :active="request()->routeIs('admin.maintenance-history')">History</x-responsive-nav-link>
                            <x-nav-link :href="route('admin.reports-overview.index')" :active="request()->routeIs('admin.reports-overview.*')">Generate Reports</x-nav-link>
                            @elseif(auth()->user()->isDriver())
                            <x-nav-link :href="route('driver.dashboard')" :active="request()->routeIs('driver.dashboard')">Dashboard</x-nav-link>
                            <x-nav-link :href="route('driver.reports.index')" :active="request()->routeIs('driver.reports.*')">My Reports</x-nav-link>
                            <x-nav-link :href="route('driver.maintenance-history')" :active="request()->routeIs('driver.maintenance-history')">History</x-nav-link>
                        @elseif(auth()->user()->isMechanic())
                            <x-nav-link :href="route('mechanic.dashboard')" :active="request()->routeIs('mechanic.dashboard')">Dashboard</x-nav-link>
                            <x-nav-link :href="route('mechanic.jobs.index')" :active="request()->routeIs('mechanic.jobs.*')">My Jobs</x-nav-link>
                            <x-nav-link :href="route('mechanic.maintenance-history')" :active="request()->routeIs('mechanic.maintenance-history')">History</x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- User Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Log Out
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 20h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @auth
                @if(auth()->user()->isAdmin())
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">Dashboard</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.vehicles.index')" :active="request()->routeIs('admin.vehicles.*')">Vehicles</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.maintenance-types.index')" :active="request()->routeIs('admin.maintenance-types.*')">Maintenance Types</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.schedules.index')" :active="request()->routeIs('admin.schedules.*')">PMS Schedules</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.*')">Reports</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.jobs.index')" :active="request()->routeIs('admin.jobs.*')">Jobs</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">Accounts</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.reports-overview.index')" :active="request()->routeIs('admin.reports-overview.*')">Generate Reports</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.maintenance-history')" :active="request()->routeIs('admin.maintenance-history')">History</x-responsive-nav-link>
                @elseif(auth()->user()->isDriver())
                    <x-responsive-nav-link :href="route('driver.dashboard')" :active="request()->routeIs('driver.dashboard')">Dashboard</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('driver.reports.index')" :active="request()->routeIs('driver.reports.*')">My Reports</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('driver.maintenance-history')" :active="request()->routeIs('driver.maintenance-history')">History</x-responsive-nav-link>
                @elseif(auth()->user()->isMechanic())
                    <x-responsive-nav-link :href="route('mechanic.dashboard')" :active="request()->routeIs('mechanic.dashboard')">Dashboard</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('mechanic.jobs.index')" :active="request()->routeIs('mechanic.jobs.*')">My Jobs</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('mechanic.maintenance-history')" :active="request()->routeIs('mechanic.maintenance-history')">History</x-responsive-nav-link>
                @endif
            @endauth
        </div>
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Log Out</x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>