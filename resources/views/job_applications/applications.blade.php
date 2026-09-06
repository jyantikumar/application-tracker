<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Applications - Job Tracker</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen font-sans pb-12" x-data="PageData()">

    <!-- Top Navigation Header -->
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

                <!-- Navigation Bar Action Buttons -->
                <div class="flex items-center gap-3">
                    <button @click="IsImportModalOpen = true" 
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-gray-300 bg-gray-800 hover:bg-gray-700 border border-gray-700 rounded-lg transition-colors">
                        <svg class="w-4 h-4 text-[#92BA56]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        Import CSV
                    </button>

                    <a href="{{ route('job-applications.create') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-gray-900 bg-[#92BA56] hover:bg-[#82a74a] rounded-lg transition-colors shadow-md shadow-[#92BA56]/20">
                        + Log Application
                    </a>
                </div>
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

        <!-- Page Header & Controls -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white tracking-tight">All Job Applications</h1>
                <p class="text-gray-400 text-sm mt-0.5">Manage and inspect compensation breakdowns & interview logs.</p>
            </div>
            
            <!-- Dynamic Search & Filters -->
            <div class="flex flex-wrap items-center gap-3">
                <!-- Search Input -->
                <div class="relative min-w-[240px]">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" x-model="SearchQuery" placeholder="Search company, position, city..."
                           class="w-full bg-gray-800/80 border border-gray-700 rounded-lg pl-9 pr-4 py-2 text-xs text-gray-100 placeholder-gray-400 focus:border-[#92BA56] focus:outline-none transition-colors">
                </div>

                <!-- Setup Filter -->
                <select x-model="JobTypeFilter" class="bg-gray-800/80 border border-gray-700 rounded-lg px-3 py-2 text-xs text-gray-200 focus:border-[#92BA56] focus:outline-none">
                    <option value="">All Work Setups</option>
                    <option value="Remote">Remote</option>
                    <option value="Hybrid">Hybrid</option>
                    <option value="Onsite">Onsite</option>
                </select>

                <!-- Status Filter -->
                <select x-model="StatusFilter" class="bg-gray-800/80 border border-gray-700 rounded-lg px-3 py-2 text-xs text-gray-200 focus:border-[#92BA56] focus:outline-none">
                    <option value="">All Statuses</option>
                    <option value="Applied">Applied</option>
                    <option value="Under Review">Under Review</option>
                    <option value="Currently Interviewing">Currently Interviewing</option>
                    <option value="Interviewed">Interviewed</option>
                    <option value="Waiting response">Waiting Response</option>
                    <option value="Rejected">Rejected</option>
                    <option value="No Response">No Response</option>
                </select>

                <span class="text-xs font-semibold px-3 py-2 rounded-lg bg-gray-800 text-gray-300 border border-gray-700">
                    Total: <span x-text="FilteredApplications.length"></span>
                </span>
            </div>
        </div>

        <!-- Table View -->
        <div class="bg-gray-800/40 backdrop-blur-sm border border-gray-700/60 rounded-xl shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-300">
                    <thead class="bg-gray-800/80 border-b border-gray-700/60 text-xs uppercase tracking-wider text-gray-400 font-semibold">
                        <tr>
                            <th class="px-6 py-4">Company & Position</th>
                            <th class="px-6 py-4">Work Setup</th>
                            <th class="px-6 py-4">Compensation</th>
                            <th class="px-6 py-4">Date Applied</th>
                            <th class="px-6 py-4">Interview Status</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/40">
                        <template x-for="Application in FilteredApplications" :key="Application.id">
                            <tr class="hover:bg-gray-800/50 transition-colors">
                                
                                <!-- Company & Position -->
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-white text-base" x-text="Application.company_name"></div>
                                    <div class="text-gray-400 text-xs" x-text="Application.position"></div>
                                </td>

                                <!-- Work Setup & City -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-gray-200" x-text="Application.job_type"></div>
                                    <div class="text-gray-400 text-xs" x-text="Application.city || 'N/A'"></div>
                                </td>

                                <!-- Formatted Salary Display -->
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-200">
                                    <span class="text-[#92BA56] font-bold" x-text="Application.formatted_salary || 'N/A'"></span>
                                    <template x-if="Application.pay_rate_type">
                                        <span class="text-gray-400 text-xs">/ <span x-text="Application.pay_rate_type.toLowerCase()"></span></span>
                                    </template>
                                </td>

                                <!-- Date Applied -->
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-300" 
                                    x-text="Application.created_at ? new Date(Application.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'N/A'">
                                </td>

                                <!-- Interview Status Indicator Badge -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <template x-if="Application.has_been_interviewed">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Interviewed (<span x-text="getInterviews(Application).filter(i => i.status === 'Completed').length"></span>)
                                        </span>
                                    </template>
                                    <template x-if="!Application.has_been_interviewed && getInterviews(Application).filter(i => i.status === 'Scheduled').length > 0">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                            Scheduled (<span x-text="getInterviews(Application).filter(i => i.status === 'Scheduled').length"></span>)
                                        </span>
                                    </template>
                                    <template x-if="!Application.has_been_interviewed && getInterviews(Application).filter(i => i.status === 'Scheduled').length === 0">
                                        <span class="text-xs text-gray-500">No interviews logged</span>
                                    </template>
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium border bg-gray-700/50 text-gray-300 border-gray-600/30" x-text="Application.status"></span>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Open Details Modal -->
                                        <button @click="openDetailsModal(Application, Application.calculated_earnings)"
                                                class="px-3 py-1.5 text-xs font-medium text-[#92BA56] bg-[#92BA56]/10 hover:bg-[#92BA56]/20 rounded-md border border-[#92BA56]/30 transition-colors">
                                            View
                                        </button>

                                        <!-- Open Interview Modal -->
                                        <button @click="openInterviewModal(Application, getInterviews(Application))"
                                                class="px-3 py-1.5 text-xs font-medium text-amber-400 bg-amber-500/10 hover:bg-amber-500/20 rounded-md border border-amber-500/20 transition-colors">
                                            Interviews (<span x-text="getInterviews(Application).length"></span>)
                                        </button>

                                        <a :href="'/job-applications/' + Application.id + '/edit'" class="px-3 py-1.5 text-xs font-medium text-gray-300 bg-gray-700/50 hover:bg-gray-700 hover:text-white rounded-md border border-gray-600/40 transition-colors">
                                            Edit
                                        </a>

                                        <form :action="'/job-applications/' + Application.id" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Delete record?')" class="px-3 py-1.5 text-xs font-medium text-rose-400 bg-rose-500/10 hover:bg-rose-500/20 rounded-md border border-rose-500/20 transition-colors">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <template x-if="FilteredApplications.length === 0">
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                    No job applications match your search or filter parameters.
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- Information & Earnings Breakdown Modal -->
    <div x-show="IsDetailsModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="IsDetailsModalOpen" x-transition.opacity @click="IsDetailsModalOpen = false" class="fixed inset-0 bg-gray-950/80 backdrop-blur-sm transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="IsDetailsModalOpen" x-transition 
                 class="inline-block align-bottom bg-gray-800 border border-gray-700/80 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full p-6">
                
                <div class="flex items-start justify-between border-b border-gray-700/60 pb-4">
                    <div>
                        <h3 class="text-xl font-bold text-white" x-text="SelectedApp.company_name"></h3>
                        <p class="text-sm text-[#92BA56] font-medium mt-0.5" x-text="SelectedApp.position"></p>
                    </div>
                    <button @click="IsDetailsModalOpen = false" class="text-gray-400 hover:text-white">&times;</button>
                </div>

                <div class="py-6 space-y-6">
                    <!-- Earnings Projection Grid -->
                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Estimated Compensation Breakdown</h4>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div class="bg-gray-900/60 p-3 rounded-xl border border-gray-700/50">
                                <span class="text-[11px] text-gray-400 block font-medium">Hourly Rate</span>
                                <span class="text-base font-bold text-white" x-text="(SelectedApp.currency || '') + ' ' + (Earnings.Hourly || '0.00')"></span>
                            </div>
                            <div class="bg-gray-900/60 p-3 rounded-xl border border-gray-700/50">
                                <span class="text-[11px] text-gray-400 block font-medium">Daily Rate</span>
                                <span class="text-base font-bold text-white" x-text="(SelectedApp.currency || '') + ' ' + (Earnings.Daily || '0.00')"></span>
                            </div>
                            <div class="bg-gray-900/60 p-3 rounded-xl border border-gray-700/50">
                                <span class="text-[11px] text-gray-400 block font-medium">Weekly Rate</span>
                                <span class="text-base font-bold text-white" x-text="(SelectedApp.currency || '') + ' ' + (Earnings.Weekly || '0.00')"></span>
                            </div>
                            <div class="bg-gray-900/60 p-3 rounded-xl border border-gray-700/50">
                                <span class="text-[11px] font-semibold text-[#92BA56] block">Monthly Rate</span>
                                <span class="text-base font-bold text-[#92BA56]" x-text="(SelectedApp.currency || '') + ' ' + (Earnings.Monthly || '0.00')"></span>
                            </div>
                        </div>
                    </div>

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

                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Job Description</h4>
                        <p class="text-xs text-gray-300 leading-relaxed bg-gray-900/50 p-3 rounded-lg border border-gray-700/40 max-h-36 overflow-y-auto whitespace-pre-line" x-text="SelectedApp.description"></p>
                    </div>

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

                <div class="border-t border-gray-700/60 pt-4 flex items-center justify-between">
                    <template x-if="SelectedApp.ApplicationURL">
                        <a :href="SelectedApp.ApplicationURL" target="_blank" class="text-xs font-semibold text-[#92BA56] hover:underline">View Listing &rarr;</a>
                    </template>
                    <button @click="IsDetailsModalOpen = false" class="px-4 py-2 text-xs font-semibold bg-gray-700 text-gray-200 rounded-lg ml-auto">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Interview Log & History Modal -->
    <div x-show="IsInterviewModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="IsInterviewModalOpen" @click="IsInterviewModalOpen = false" class="fixed inset-0 bg-gray-950/80 backdrop-blur-sm transition-opacity"></div>

            <div class="inline-block align-bottom bg-gray-800 border border-gray-700/80 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full p-6">
                
                <div class="flex items-center justify-between border-b border-gray-700/60 pb-4">
                    <div>
                        <h3 class="text-lg font-bold text-white">Interview Timeline</h3>
                        <p class="text-xs text-gray-400" x-text="SelectedApp.company_name + ' - ' + SelectedApp.position"></p>
                    </div>
                    <button @click="IsInterviewModalOpen = false" class="text-gray-400 hover:text-white">&times;</button>
                </div>

                <!-- Existing Interview Log Entries -->
                <div class="py-4 space-y-3 max-h-60 overflow-y-auto">
                    <template x-if="InterviewsList.length === 0">
                        <p class="text-xs text-gray-400 text-center py-4">No interviews logged for this role yet.</p>
                    </template>

                    <template x-for="Interview in InterviewsList" :key="Interview.id">
                        <div class="bg-gray-900/60 p-3 rounded-lg border border-gray-700/50 flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-sm text-white" x-text="Interview.interview_type"></span>
                                    <span class="text-[10px] uppercase font-semibold px-2 py-0.5 rounded"
                                          :class="{
                                              'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20': Interview.status === 'Completed',
                                              'bg-amber-500/10 text-amber-400 border border-amber-500/20': Interview.status === 'Scheduled',
                                              'bg-rose-500/10 text-rose-400 border border-rose-500/20': Interview.status === 'Cancelled'
                                          }" x-text="Interview.status"></span>
                                </div>
                                <p class="text-xs text-gray-400 mt-1" x-text="Interview.scheduled_at ? new Date(Interview.scheduled_at).toLocaleString() : 'Date N/A'"></p>
                                <p class="text-xs text-gray-300 mt-1" x-text="Interview.notes || 'No notes added'"></p>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Form to Log New Interview -->
                <form :action="'/job-applications/' + SelectedApp.id + '/interviews'" method="POST" class="border-t border-gray-700/60 pt-4 space-y-3">
                    @csrf
                    <h4 class="text-xs font-bold text-[#92BA56] uppercase tracking-wider">Log New Interview Round</h4>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] text-gray-400 font-semibold mb-1">Interview Type *</label>
                            <select name="interview_type" required class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-1.5 text-xs text-white">
                                <option value="HR Screening">HR Screening</option>
                                <option value="Technical Assessment">Technical Assessment</option>
                                <option value="Technical Interview">Technical Interview</option>
                                <option value="Final / Executive">Final / Executive</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] text-gray-400 font-semibold mb-1">Scheduled Date & Time *</label>
                            <input type="datetime-local" name="scheduled_at" required class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-1.5 text-xs text-white">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] text-gray-400 font-semibold mb-1">Status *</label>
                            <select name="status" required class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-1.5 text-xs text-white">
                                <option value="Scheduled">Scheduled</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] text-gray-400 font-semibold mb-1">Notes / Feedback</label>
                            <input type="text" name="notes" placeholder="Interviewer name, tech stack..." class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-1.5 text-xs text-white">
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="IsInterviewModalOpen = false" class="px-3 py-1.5 text-xs font-medium text-gray-400 hover:text-white">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 text-xs font-semibold text-gray-900 bg-[#92BA56] hover:bg-[#82a74a] rounded-lg">Save Log</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Import CSV Modal -->
    <div x-show="IsImportModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="IsImportModalOpen" @click="IsImportModalOpen = false" class="fixed inset-0 bg-gray-950/80 backdrop-blur-sm transition-opacity"></div>

            <div class="inline-block align-bottom bg-gray-800 border border-gray-700/80 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-6">
                
                <div class="flex items-center justify-between border-b border-gray-700/60 pb-4 mb-4">
                    <h3 class="text-lg font-bold text-white">Import Job Applications</h3>
                    <button @click="IsImportModalOpen = false" class="text-gray-400 hover:text-white">&times;</button>
                </div>

                <form action="{{ route('job-applications.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Select CSV File *</label>
                        <input type="file" name="csv_file" accept=".csv" required 
                               class="w-full text-xs text-gray-300 bg-gray-900 border border-gray-700 rounded-lg p-2.5 focus:border-[#92BA56]">
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="IsImportModalOpen = false" class="px-4 py-2 text-xs font-medium text-gray-400 hover:text-white">Cancel</button>
                        <button type="submit" class="px-5 py-2 text-xs font-semibold text-gray-900 bg-[#92BA56] hover:bg-[#82a74a] rounded-lg">Upload & Import</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        function PageData() {
            return {
                AllApplications: @json($JobApplications),
                SearchQuery: '',
                JobTypeFilter: '',
                StatusFilter: '',
                IsDetailsModalOpen: false,
                IsInterviewModalOpen: false,
                IsImportModalOpen: false,
                SelectedApp: {},
                Earnings: {},
                InterviewsList: [],

                getInterviews(App) {
                    return App.interviews || App.Interviews || [];
                },

                get FilteredApplications() {
    return this.AllApplications.filter(App => {
        const Query = this.SearchQuery.toLowerCase().trim();
        
        const MatchesSearch = !Query || 
            (App.company_name && App.company_name.toLowerCase().includes(Query)) ||
            (App.position && App.position.toLowerCase().includes(Query)) ||
            (App.city && App.city.toLowerCase().includes(Query)) ||
            (App.source && App.source.toLowerCase().includes(Query));

        const MatchesJobType = !this.JobTypeFilter || App.job_type === this.JobTypeFilter;
        
        // Handle "Interviewed" status filter specifically
        let MatchesStatus = true;
        if (this.StatusFilter === 'Interviewed') {
            const InterviewCount = this.getInterviews(App).length;
            MatchesStatus = InterviewCount > 0 || App.has_been_interviewed || App.status === 'Interviewed';
        } else if (this.StatusFilter) {
            MatchesStatus = App.status === this.StatusFilter;
        }
        return MatchesSearch && MatchesJobType && MatchesStatus;
    });
},

                openDetailsModal(App, Calculated) {
                    this.SelectedApp = App;
                    this.Earnings = Calculated || {};
                    this.IsDetailsModalOpen = true;
                },
                openInterviewModal(App, Interviews) {
                    this.SelectedApp = App;
                    this.InterviewsList = Interviews || [];
                    this.IsInterviewModalOpen = true;
                }
            }
        }
    </script>
</body>
</html>