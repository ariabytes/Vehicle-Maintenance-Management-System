<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VMMS — Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex">

    {{-- Left Panel --}}
    <div class="hidden lg:flex lg:w-1/2 bg-gray-900 text-white flex-col justify-center px-16 py-14">
        <div class="bg-blue-950/100 border border-blue-800 rounded-2xl p-10 space-y-8">

            <div>
                <h1 class="text-3xl font-black tracking-tight">VM<span class="text-blue-300">M</span>S</h1>
                <p class="text-blue-300 text-xs uppercase tracking-widest mt-1">Vehicle Maintenance Management System</p>
            </div>

            <div class="space-y-6">
                <p class="text-blue-200 text-sm leading-relaxed">
                    A fleet maintenance and issue reporting system. VMMS streamlines fleet operations by connecting three types of users:
                </p>

                <ul class="space-y-5">
                    <li class="flex gap-3">
                        <span class="mt-0.5 w-6 h-6 rounded-full bg-blue-700 flex items-center justify-center shrink-0 text-blue-200 text-xs font-bold">A</span>
                        <div>
                            <p class="text-white text-sm font-semibold">Admin</p>
                            <p class="text-blue-300 text-xs mt-0.5 leading-relaxed">Manages accounts, vehicles, and maintenance schedules. Reviews and approves driver-reported issues, assigns jobs to mechanics, and monitors the entire fleet maintenance and reported issues.</p>
                        </div>
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-0.5 w-6 h-6 rounded-full bg-blue-700 flex items-center justify-center shrink-0 text-blue-200 text-xs font-bold">D</span>
                        <div>
                            <p class="text-white text-sm font-semibold">Driver</p>
                            <p class="text-blue-300 text-xs mt-0.5 leading-relaxed">Reports vehicle issues, views their assigned vehicle's current status, and tracks upcoming preventive maintenance schedules.</p>
                        </div>
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-0.5 w-6 h-6 rounded-full bg-blue-700 flex items-center justify-center shrink-0 text-blue-200 text-xs font-bold">M</span>
                        <div>
                            <p class="text-white text-sm font-semibold">Mechanic</p>
                            <p class="text-blue-300 text-xs mt-0.5 leading-relaxed">Receives assigned repair and maintenance jobs, updates job progress, and logs findings upon completion.</p>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="border-t border-blue-800 pt-6 space-y-1">
                <p class="text-blue-300 text-xs">Submitted in partial fulfillment of IT9A · Professional Track for IT3</p>
                <p class="text-blue-300 text-xs">University of Mindanao</p>
                <p class="text-blue-300 text-xs font-semibold mt-1">Añora</p>
            </div>

        </div>
    </div>

    {{-- Right Panel --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center bg-white px-8 py-12">
        <div class="w-full max-w-md">

            <h2 class="text-2xl font-bold text-gray-800 mb-1">Sign in</h2>
            <p class="text-sm text-gray-500 mb-8">Enter your credentials to access your dashboard.</p>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            @if($errors->any())
                <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded mb-6 text-sm">
                    @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                                  :value="old('email')" required autofocus autocomplete="username"
                                  placeholder="Enter your email" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="password" value="Password" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password"
                                  name="password" required autocomplete="current-password"
                                  placeholder="••••••••" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <div>
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox"
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                               name="remember">
                        <span class="ms-2 text-sm text-gray-600">Remember me</span>
                    </label>
                </div>

                <x-primary-button class="w-full justify-center py-2.5">
                    Sign In
                </x-primary-button>
            </form>

            {{-- Test Credentials Toggle --}}
            <div class="mt-8 border border-gray-200 rounded-lg overflow-hidden">
                <button onclick="document.getElementById('test-creds').classList.toggle('hidden')"
                        class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 text-sm text-gray-600 hover:bg-gray-100 transition font-medium">
                    <span>🔑 Try a test account!</span>
                </button>
                <div id="test-creds" class="hidden px-4 py-4 bg-white border-t border-gray-100 space-y-3 text-sm">
                    <div class="bg-blue-50 border border-blue-200 rounded p-3">
                        <p class="font-semibold text-blue-800 text-xs uppercase tracking-wide mb-2">Admin Account</p>
                        <p class="text-gray-700">Email: <span class="font-mono text-blue-700">test@gmail.vmms</span></p>
                        <p class="text-gray-700">Password: <span class="font-mono text-blue-700">password</span></p>
                    </div>
                    <p class="text-xs text-gray-400 leading-relaxed">
                        Log in as admin, then go to <strong>Accounts</strong> to find driver and mechanic credentials. You can log in as any user to explore each role's dashboard.
                    </p>
                </div>
            </div>

            <div class="lg:hidden mt-10">
                <div class="bg-gray-900 border border-blue-800 rounded-2xl p-6 space-y-5 mb-8">
                    <div>
                        <h1 class="text-2xl font-black tracking-tight text-white">VM<span class="text-blue-300">M</span>S</h1>
                        <p class="text-blue-300 text-xs uppercase tracking-widest mt-1">Vehicle Maintenance Management System</p>
                    </div>

                    <p class="text-blue-200 text-xs leading-relaxed">
                        A fleet maintenance and issue reporting system. VMMS streamlines fleet operations by connecting three types of users:
                    </p>

                    <ul class="space-y-3">
                        <li class="flex gap-3">
                            <span class="mt-0.5 w-5 h-5 rounded-full bg-blue-700 flex items-center justify-center shrink-0 text-blue-200 text-xs font-bold">A</span>
                            <div>
                                <p class="text-white text-xs font-semibold">Admin</p>
                                <p class="text-blue-300 text-xs mt-0.5 leading-relaxed">Manages accounts, vehicles, and maintenance schedules. Reviews and approves driver-reported issues and assigns jobs to mechanics.</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <span class="mt-0.5 w-5 h-5 rounded-full bg-blue-700 flex items-center justify-center shrink-0 text-blue-200 text-xs font-bold">D</span>
                            <div>
                                <p class="text-white text-xs font-semibold">Driver</p>
                                <p class="text-blue-300 text-xs mt-0.5 leading-relaxed">Reports vehicle issues, views their assigned vehicle's current status, and tracks upcoming preventive maintenance schedules.</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <span class="mt-0.5 w-5 h-5 rounded-full bg-blue-700 flex items-center justify-center shrink-0 text-blue-200 text-xs font-bold">M</span>
                            <div>
                                <p class="text-white text-xs font-semibold">Mechanic</p>
                                <p class="text-blue-300 text-xs mt-0.5 leading-relaxed">Receives assigned repair and maintenance jobs, updates job progress, and logs findings upon completion.</p>
                            </div>
                        </li>
                    </ul>

                    <div class="border-t border-blue-800 pt-4 space-y-1">
                        <p class="text-blue-300 text-xs">Submitted in partial fulfillment of IT9A · Professional Track for IT3</p>
                        <p class="text-blue-300 text-xs">University of Mindanao</p>
                        <p class="text-blue-300 text-xs font-semibold mt-1">Añora</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>