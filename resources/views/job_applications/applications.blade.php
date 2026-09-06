<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Applications - Job Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen font-sans pb-12" x-data="ApplicationModal()">

    <!-- Navigation Header -->
    <nav class="bg-gray-800/80 backdrop-blur-md border-b border-gray-700/50 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-8">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-[#92BA56]/20 rounded-lg border border-[#92BA56]/30">
                            <svg class="w-6 h-6 text-[#92BA56]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <span class="font-bold text-xl tracking-tight text-white">Job Tracker</span>
                    </div>

                    <div class="flex items-center space-x-4">
                        <a href="{{ route('job-applications.index') }}" class="px-3 py-2 rounded-lg text-sm font-semibold transition-colors text-gray-300 hover:text-white hover:bg-gray-700/50">Dashboard</a>
                        <a href="{{ route('job-applications.applications') }}" class="px-3 py-2 rounded-lg text-sm font-semibold transition-colors bg-gray-900 text-[#92BA56] border border-[#92BA56]/30">Applications</a>
                    </div>
                </div>

                <a href="{{ route('job-applications.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-gray-900 bg-[#92BA56] hover:bg-[#82a74a] rounded-lg transition-colors shadow-md shadow-[#92BA56]/20">
                    + Log Application
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-200">&times;</button>
            </div>
        @endif

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white tracking-tight">All Job Applications</h1>
                <p class="text-gray-400 text-sm mt-0.5">Manage and inspect compensation breakdowns.</p>
            </div>
            <span class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-gray-800 text-gray-300 border border-gray-700">
                Total Records: {{ $JobApplications->count() }}
            </span>
        </div>

        <!-- Table View -->
        <div class="bg-gray-800/40 backdrop-blur-sm border border-gray-700/60 rounded-xl shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-300">
                    <thead class="bg-gray-800/80 border-b border-gray-700/60 text-xs uppercase tracking-wider text-gray-400 font-semibold">
                        <tr>
                            <th class="px-6 py-4">Company & Position</th>
                            <th class="px-6 py-4">Work Setup & City</th>
                            <th class="px-6 py-4">Base Compensation</th>
                            <th class="px-6 py-4">Source</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/40">
                        @forelse($JobApplications as $Application)
                            <tr class="hover:bg-gray-800/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-white text-base">{{ $Application->company_name }}</div>
                                    <div class="text-gray-400 text-xs">{{ $Application->position }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-gray-200">{{ $Application->job_type }}</div>
                                    <div class="text-gray-400 text-xs">{{ $Application->city }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-200">
                                    <span class="text-[#92BA56] font-bold">{{ $Application->currency }}</span> {{ number_format($Application->salary, 2) }}
                                    <span class="text-gray-400 text-xs">/ {{ strtolower($Application->pay_rate_type) }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-md text-xs font-medium bg-gray-700/50 text-gray-300 border border-gray-600/30">
                                        {{ $Application->source }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium border bg-gray-700/50 text-gray-300 border-gray-600/30">
                                        {{ $Application->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- View Detail Modal Button -->
                                        <button @click="openModal({{ json_encode($Application) }}, {{ json_encode($Application->calculated_earnings) }})"
                                                class="px-3 py-1.5 text-xs font-medium text-[#92BA56] bg-[#92BA56]/10 hover:bg-[#92BA56]/20 rounded-md border border-[#92BA56]/30 transition-colors">
                                            View Details
                                        </button>

                                        <a href="{{ route('job-applications.edit', $Application->id) }}" class="px-3 py-1.5 text-xs font-medium text-gray-300 bg-gray-700/50 hover:bg-gray-700 hover:text-white rounded-md border border-gray-600/40 transition-colors">
                                            Edit
                                        </a>

                                        <form action="{{ route('job-applications.destroy', $Application->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Delete record?')" class="px-3 py-1.5 text-xs font-medium text-rose-400 bg-rose-500/10 hover:bg-rose-500/20 rounded-md border border-rose-500/20 transition-colors">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">No applications recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- Information & Earnings Breakdown Modal -->
    <div x-show="IsOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Backdrop -->
            <div x-show="IsOpen" x-transition.opacity @click="IsOpen = false" class="fixed inset-0 bg-gray-950/80 backdrop-blur-sm transition-opacity"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Content Card -->
            <div x-show="IsOpen" x-transition 
                 class="inline-block align-bottom bg-gray-800 border border-gray-700/80 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full p-6">
                
                <!-- Modal Header -->
                <div class="flex items-start justify-between border-b border-gray-700/60 pb-4">
                    <div>
                        <h3 class="text-xl font-bold text-white" x-text="SelectedApp.company_name"></h3>
                        <p class="text-sm text-[#92BA56] font-medium mt-0.5" x-text="SelectedApp.position"></p>
                    </div>
                    <button @click="IsOpen = false" class="text-gray-400 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="py-6 space-y-6">

                    <!-- Earnings Projection Grid -->
                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Estimated Compensation Breakdown</h4>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div class="bg-gray-900/60 p-3 rounded-xl border border-gray-700/50">
                                <span class="text-[11px] text-gray-400 block font-medium">Hourly</span>
                                <span class="text-base font-bold text-white" x-text="SelectedApp.currency + ' ' + Earnings.Hourly"></span>
                            </div>
                            <div class="bg-gray-900/60 p-3 rounded-xl border border-gray-700/50">
                                <span class="text-[11px] text-gray-400 block font-medium">Daily (8 hrs)</span>
                                <span class="text-base font-bold text-white" x-text="SelectedApp.currency + ' ' + Earnings.Daily"></span>
                            </div>
                            <div class="bg-gray-900/60 p-3 rounded-xl border border-gray-700/50">
                                <span class="text-[11px] text-gray-400 block font-medium">Weekly</span>
                                <span class="text-base font-bold text-white" x-text="SelectedApp.currency + ' ' + Earnings.Weekly"></span>
                            </div>
                            <div class="bg-gray-900/60 p-3 rounded-xl border border-gray-700/50">
                                <span class="text-[11px] font-semibold text-[#92BA56] block">Monthly</span>
                                <span class="text-base font-bold text-[#92BA56]" x-text="SelectedApp.currency + ' ' + Earnings.Monthly"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Application Details Grid -->
                    <div class="grid grid-cols-2 gap-4 text-sm bg-gray-900/40 p-4 rounded-xl border border-gray-700/40">
                        <div>
                            <span class="text-gray-400 block text-xs">Work Setup:</span>
                            <span class="font-medium text-gray-200" x-text="SelectedApp.job_type"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block text-xs">Location / City:</span>
                            <span class="font-medium text-gray-200" x-text="SelectedApp.city"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block text-xs">Source Platform:</span>
                            <span class="font-medium text-gray-200" x-text="SelectedApp.source"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block text-xs">Weekly Work Hours:</span>
                            <span class="font-medium text-gray-200" x-text="(SelectedApp.hours_per_week || 40) + ' hrs/week'"></span>
                        </div>
                    </div>

                    <!-- Description Section -->
                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Job Description</h4>
                        <p class="text-xs text-gray-300 leading-relaxed bg-gray-900/50 p-3 rounded-lg border border-gray-700/40 max-h-36 overflow-y-auto whitespace-pre-line" x-text="SelectedApp.description"></p>
                    </div>

                    <!-- Pros and Cons -->
                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div class="bg-emerald-500/10 p-3 rounded-lg border border-emerald-500/20">
                            <span class="font-bold text-emerald-400 block mb-1">Pros</span>
                            <span class="text-gray-300" x-text="SelectedApp.pros || 'None specified'"></span>
                        </div>
                        <div class="bg-rose-500/10 p-3 rounded-lg border border-rose-500/20">
                            <span class="font-bold text-rose-400 block mb-1">Cons</span>
                            <span class="text-gray-300" x-text="SelectedApp.cons || 'None specified'"></span>
                        </div>
                    </div>

                </div>

                <!-- Footer Link Button -->
                <div class="border-t border-gray-700/60 pt-4 flex items-center justify-between">
                    <template x-if="SelectedApp.ApplicationURL">
                        <a :href="SelectedApp.ApplicationURL" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#92BA56] hover:underline">
                            View Job Listing
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </a>
                    </template>
                    <button @click="IsOpen = false" class="px-4 py-2 text-xs font-semibold bg-gray-700 text-gray-200 hover:bg-gray-600 rounded-lg transition-colors ml-auto">
                        Close
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- AlpineJS Modal Logic -->
    <script>
        function ApplicationModal() {
            return {
                IsOpen: false,
                SelectedApp: {},
                Earnings: {},
                openModal(App, Calculated) {
                    this.SelectedApp = App;
                    this.Earnings = Calculated;
                    this.IsOpen = true;
                }
            }
        }
    </script>
</body>
</html>