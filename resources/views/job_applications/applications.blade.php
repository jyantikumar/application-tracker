<x-app-layout>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

    <style>
        [x-cloak] { display: none !important; }

        /* Custom DataTables Styling to Match Dark Layout Theme */
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            background-color: rgba(31, 41, 55, 0.8) !important;
            border: 1px solid rgba(55, 65, 81, 0.8) !important;
            color: #F3F4F6 !important;
            border-radius: 0.5rem !important;
            padding: 0.375rem 0.75rem !important;
            font-size: 0.75rem !important;
            outline: none !important;
        }
        .dataTables_wrapper .dataTables_filter input:focus,
        .dataTables_wrapper .dataTables_length select:focus {
            border-color: #92BA56 !important;
        }
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            color: #9CA3AF !important;
            font-size: 0.75rem !important;
            margin-bottom: 1rem !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            color: #D1D5DB !important;
            border-radius: 0.375rem !important;
            border: none !important;
            font-size: 0.75rem !important;
            padding: 0.25rem 0.625rem !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #92BA56 !important;
            color: #0B1120 !important;
            font-weight: 700 !important;
            border: none !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: rgba(55, 65, 81, 0.8) !important;
            color: #FFFFFF !important;
            border: none !important;
        }
        table.dataTable thead th {
            border-bottom: 1px solid rgba(55, 65, 81, 0.6) !important;
        }
        table.dataTable.no-footer {
            border-bottom: 1px solid rgba(55, 65, 81, 0.6) !important;
        }
        /* Row-click affordance */
        .js-open-details { cursor: pointer; }
        .js-open-details:focus-visible {
            outline: 2px solid #92BA56;
            outline-offset: -2px;
        }
    </style>

    @php
        // Build ONE payload map keyed by application id — parsed once on the client.
        $AllAppPayloads = [];
        foreach ($JobApplications as $Application) {
            $Interviews = collect($Application->interviews ?? $Application->Interviews ?? []);
            $AllAppPayloads[$Application->id] = [
                'id'                  => $Application->id,
                'company_name'        => $Application->company_name,
                'position'            => $Application->position,
                'job_type'            => $Application->job_type,
                'city'                => $Application->city,
                'source'              => $Application->source,
                'currency'            => $Application->currency,
                'hours_per_week'      => $Application->hours_per_week,
                'description'         => $Application->description,
                'pros'                => $Application->pros,
                'cons'                => $Application->cons,
                'ApplicationURL'      => $Application->ApplicationURL,
                'interviews'          => $Interviews->values()->all(),
                'calculated_earnings' => $Application->calculated_earnings ?? [],
            ];
        }
    @endphp

    <div class="flex flex-col min-h-screen" x-data="pageData"
         x-effect="document.body.classList.toggle('overflow-hidden', IsDetailsModalOpen || IsInterviewModalOpen)"
         @keydown.escape.window="IsDetailsModalOpen = false; IsInterviewModalOpen = false;">

        <!-- Mobile Header -->
        <header class="lg:hidden bg-gray-900/80 backdrop-blur-md border-b border-gray-700/60 p-4 sticky top-0 z-40 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="p-1.5 bg-[#92BA56]/20 rounded-lg border border-[#92BA56]/30">
                    <svg class="w-5 h-5 text-[#92BA56]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <span class="font-bold text-lg tracking-tight text-white">Job Tracker</span>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('job-applications.create') }}" class="px-3 py-1.5 text-xs font-semibold text-gray-950 bg-[#92BA56] hover:bg-[#82a74a] rounded-lg transition-colors">+ Log</a>
                <button @click="IsMobileMenuOpen = true" type="button" class="btn btn-square btn-ghost btn-xs text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </header>

        <!-- Main Content Wrapper -->
        <main class="lg:pl-72 flex-1 max-w-full w-full px-4 sm:px-4 lg:px-4 py-8 space-y-6">
 
            <!-- Page Header & Dynamic Filters -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-white tracking-tight">All Job Applications</h1>
                    <p class="text-gray-400 text-sm mt-0.5">Click any row to inspect details and perform actions.</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <select id="JobTypeFilter" class="bg-gray-800/80 border border-gray-700/80 rounded-lg px-3 py-2 text-xs text-gray-200 focus:border-[#92BA56] focus:outline-none">
                        <option value="">All Work Setups</option>
                        @php
                            $WorkSetups = collect($JobApplications)->pluck('job_type')->filter()->unique()->sort();
                        @endphp
                        @foreach($WorkSetups as $Setup)
                            <option value="{{ $Setup }}">{{ $Setup }}</option>
                        @endforeach
                    </select>

                    <select id="StatusFilter" class="bg-gray-800/80 border border-gray-700/80 rounded-lg px-3 py-2 text-xs text-gray-200 focus:border-[#92BA56] focus:outline-none">
    <option value="">All Statuses</option>
    @php
        $Statuses = collect($JobApplications)
            ->pluck('status')
            ->merge($CustomStatuses ?? [])
            ->filter()
            ->unique()
            ->sort()
            ->values();
    @endphp
    @foreach($Statuses as $Status)
        <option value="{{ $Status }}">{{ $Status }}</option>
    @endforeach
</select>

                    <div class="badge bg-gray-800/80 border border-gray-700/80 text-gray-300 text-xs py-3.5 px-3">
                        Total Applications: <span class="ml-1 font-bold text-white">{{ count($JobApplications) }}</span>
                    </div>
                </div>
            </div>

            <!-- DataTables Card Container -->
            <div class="card bg-gray-900/60 border border-gray-800 shadow-xl p-4 overflow-hidden">
                <div class="overflow-x-auto">
                    <table id="ApplicationsTable" class="table table-zebra w-full text-left">
                        <thead class="bg-gray-800/80 text-xs uppercase tracking-wider text-gray-400 border-b border-gray-700/60">
                            <tr>
                                <th class="py-4 px-6">Company &amp; Position</th>
                                <th class="py-4 px-6">Work Setup</th>
                                <th class="py-4 px-6">Compensation</th>
                                <th class="py-4 px-6">Date Applied</th>
                                <th class="py-4 px-6">Interview Status</th>
                                <th class="py-4 px-6">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800/60">
                            @foreach($JobApplications as $Application)
                                @php
                                    $Interviews = collect($Application->interviews ?? $Application->Interviews ?? []);
                                    $CompletedInterviews = $Interviews->where('status', 'Completed')->count();
                                    $ScheduledInterviews = $Interviews->where('status', 'Scheduled')->count();
                                @endphp
                                <tr class="js-open-details group hover:bg-gray-800/40 transition-colors"
                                    data-app-id="{{ $Application->id }}"
                                    tabindex="0"
                                    role="button"
                                    aria-label="View details for {{ $Application->company_name }} — {{ $Application->position }}">

                                    <td class="py-4 px-6">
                                        <div class="font-bold text-base text-white">{{ $Application->company_name }}</div>
                                        <div class="text-[#92BA56] font-semibold text-xs mt-0.5 inline-flex items-center gap-1">
                                            <span>{{ $Application->position }}</span>
                                            <svg class="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity"
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </td>

                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <div class="text-gray-200">{{ $Application->job_type }}</div>
                                        <div class="text-gray-400 text-xs">{{ $Application->city ?? 'N/A' }}</div>
                                    </td>

                                    <td class="py-4 px-6 whitespace-nowrap font-medium">
                                        <span class="text-[#92BA56] font-bold">{{ $Application->formatted_salary ?? 'N/A' }}</span>
                                        @if(!empty($Application->pay_rate_type))
                                            <span class="text-gray-400 text-xs">/ {{ strtolower($Application->pay_rate_type) }}</span>
                                        @endif
                                    </td>

                                    <td class="py-4 px-6 whitespace-nowrap text-xs text-gray-300">
                                        {{ $Application->created_at ? \Carbon\Carbon::parse($Application->created_at)->format('M j, Y') : 'N/A' }}
                                    </td>

                                    <td class="py-4 px-6 whitespace-nowrap">
                                        @if(!empty($Application->has_been_interviewed) || $CompletedInterviews > 0)
                                            <span class="badge bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 gap-1 text-xs font-semibold py-2">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                Interviewed ({{ $CompletedInterviews }})
                                            </span>
                                        @elseif($ScheduledInterviews > 0)
                                            <span class="badge bg-amber-500/10 text-amber-400 border border-amber-500/20 gap-1 text-xs font-semibold py-2">
                                                Scheduled ({{ $ScheduledInterviews }})
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-500">No interviews logged</span>
                                        @endif
                                    </td>

                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <span class="badge bg-gray-800/80 text-gray-300 border border-gray-700 text-xs py-2">{{ $Application->status }}</span>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </main>

        <!-- ============================================================
             Details Modal (Alpine-controlled)
             ============================================================ -->
        <div x-show="IsDetailsModalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             role="dialog" aria-modal="true" aria-labelledby="details-modal-title">
            <div x-show="IsDetailsModalOpen" x-transition.opacity @click="IsDetailsModalOpen = false"
                 class="absolute inset-0 bg-gray-950/80 backdrop-blur-sm"></div>

            <div x-show="IsDetailsModalOpen" x-transition
                 class="relative z-10 max-w-2xl w-full max-h-[90vh] overflow-y-auto bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl p-6">

                <div class="flex items-start justify-between border-b border-gray-800 pb-4">
                    <div class="min-w-0 pr-8">
                        <h3 id="details-modal-title" class="text-xl font-bold text-white truncate"
                            x-text="SelectedApp.company_name"></h3>
                        <p class="text-sm text-[#92BA56] font-medium mt-0.5 truncate"
                           x-text="SelectedApp.position"></p>
                    </div>
                    <button type="button" @click="IsDetailsModalOpen = false"
                            class="btn btn-text btn-circle btn-sm text-gray-400 hover:text-white"
                            aria-label="Close">&times;</button>
                </div>

                <div class="py-6 space-y-6">
                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Estimated Compensation Breakdown</h4>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div class="card bg-gray-800/60 p-3 border border-gray-700/60">
                                <span class="text-[11px] text-gray-400 block font-medium">Hourly Rate</span>
                                <span class="text-base font-bold text-white" x-text="(SelectedApp.currency || '') + ' ' + (Earnings.Hourly || '0.00')"></span>
                            </div>
                            <div class="card bg-gray-800/60 p-3 border border-gray-700/60">
                                <span class="text-[11px] text-gray-400 block font-medium">Daily Rate</span>
                                <span class="text-base font-bold text-white" x-text="(SelectedApp.currency || '') + ' ' + (Earnings.Daily || '0.00')"></span>
                            </div>
                            <div class="card bg-gray-800/60 p-3 border border-gray-700/60">
                                <span class="text-[11px] text-gray-400 block font-medium">Weekly Rate</span>
                                <span class="text-base font-bold text-white" x-text="(SelectedApp.currency || '') + ' ' + (Earnings.Weekly || '0.00')"></span>
                            </div>
                            <div class="card bg-gray-800/60 p-3 border border-gray-700/60">
                                <span class="text-[11px] font-semibold text-[#92BA56] block">Monthly Rate</span>
                                <span class="text-base font-bold text-[#92BA56]" x-text="(SelectedApp.currency || '') + ' ' + (Earnings.Monthly || '0.00')"></span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-sm bg-gray-800/40 p-4 rounded-xl border border-gray-700/60">
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
                        <p class="text-xs text-gray-300 leading-relaxed bg-gray-800/50 p-3 rounded-lg border border-gray-700/60 max-h-36 overflow-y-auto whitespace-pre-line"
                           x-text="SelectedApp.description"></p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div class="alert bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-3 rounded-lg">
                            <div>
                                <span class="font-bold block mb-1">Pros</span>
                                <span class="text-gray-300" x-text="SelectedApp.pros || 'None specified'"></span>
                            </div>
                        </div>
                        <div class="alert bg-rose-500/10 border border-rose-500/20 text-rose-400 p-3 rounded-lg">
                            <div>
                                <span class="font-bold block mb-1">Cons</span>
                                <span class="text-gray-300" x-text="SelectedApp.cons || 'None specified'"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Action Footer -->
                <div class="mt-8 pt-5 border-t border-gray-800 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <template x-if="SelectedApp.ApplicationURL">
                            <a :href="SelectedApp.ApplicationURL" target="_blank"
                               class="btn btn-sm btn-outline border-[#92BA56]/40 text-[#92BA56] hover:bg-[#92BA56] hover:text-gray-950">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                View Listing
                            </a>
                        </template>

                        <button type="button" @click="switchToInterviewModal()"
                                class="btn btn-sm btn-outline border-amber-500/40 text-amber-400 hover:bg-amber-500 hover:text-gray-950">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Interviews (<span x-text="(SelectedApp.interviews || []).length"></span>)
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        <a :href="'/job-applications/' + SelectedApp.id + '/edit'"
                           class="btn btn-sm btn-outline border-gray-700/80 text-gray-300 hover:bg-gray-800 hover:text-white">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Edit
                        </a>

                        <form :action="'/job-applications/' + SelectedApp.id" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this application record?')"
                                    class="btn btn-sm btn-outline border-rose-500/40 text-rose-400 hover:bg-rose-500 hover:text-gray-950">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Delete
                            </button>
                        </form>

                        <button type="button" @click="IsDetailsModalOpen = false"
                                class="btn btn-sm btn-outline border-gray-700 text-gray-300 hover:bg-gray-800 hover:text-white">
                            Close
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- ============================================================
             Interview Modal (Alpine-controlled)
             ============================================================ -->
        <div x-show="IsInterviewModalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             role="dialog" aria-modal="true" aria-labelledby="interview-modal-title">
            <div x-show="IsInterviewModalOpen" x-transition.opacity @click="IsInterviewModalOpen = false"
                 class="absolute inset-0 bg-gray-950/80 backdrop-blur-sm"></div>

            <div x-show="IsInterviewModalOpen" x-transition
                 class="relative z-10 max-w-xl w-full max-h-[90vh] overflow-y-auto bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl p-6">

                <div class="flex items-center justify-between border-b border-gray-800 pb-4">
                    <div class="min-w-0 pr-8">
                        <h3 id="interview-modal-title" class="text-lg font-bold text-white">Interview Timeline</h3>
                        <p class="text-xs text-gray-400 mt-0.5 truncate"
                           x-text="(SelectedApp.company_name || '') + ' — ' + (SelectedApp.position || '')"></p>
                    </div>
                    <button type="button" @click="IsInterviewModalOpen = false"
                            class="btn btn-text btn-circle btn-sm text-gray-400 hover:text-white"
                            aria-label="Close">&times;</button>
                </div>

                <div class="py-4 space-y-3 max-h-60 overflow-y-auto">
                    <template x-if="InterviewsList.length === 0">
                        <p class="text-xs text-gray-400 text-center py-4">No interviews logged for this role yet.</p>
                    </template>

                    <template x-for="Interview in InterviewsList" :key="Interview.id">
                        <div class="card bg-gray-800/60 p-3 border border-gray-700/60 flex flex-row items-center justify-between">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-sm text-white" x-text="Interview.interview_type"></span>
                                    <span class="badge text-[10px] uppercase font-semibold px-2 py-0.5"
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

                <form :action="'/job-applications/' + SelectedApp.id + '/interviews'" method="POST" class="border-t border-gray-800 pt-4 space-y-3">
                    @csrf
                    <h4 class="text-xs font-bold text-[#92BA56] uppercase tracking-wider">Log New Interview Round</h4>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] text-gray-400 font-semibold mb-1">Interview Type *</label>
                            <select name="interview_type" required class="w-full bg-gray-800 border border-gray-700/80 rounded-lg px-3 py-1.5 text-xs text-white focus:border-[#92BA56] focus:outline-none">
                                <option value="HR Screening">HR Screening</option>
                                <option value="Technical Assessment">Technical Assessment</option>
                                <option value="Technical Interview">Technical Interview</option>
                                <option value="Final / Executive">Final / Executive</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] text-gray-400 font-semibold mb-1">Scheduled Date &amp; Time *</label>
                            <input type="datetime-local" name="scheduled_at" required class="w-full bg-gray-800 border border-gray-700/80 rounded-lg px-3 py-1.5 text-xs text-white focus:border-[#92BA56] focus:outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] text-gray-400 font-semibold mb-1">Status *</label>
                            <select name="status" required class="w-full bg-gray-800 border border-gray-700/80 rounded-lg px-3 py-1.5 text-xs text-white focus:border-[#92BA56] focus:outline-none">
                                <option value="Scheduled">Scheduled</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] text-gray-400 font-semibold mb-1">Notes / Feedback</label>
                            <input type="text" name="notes" placeholder="Interviewer name, tech stack..." class="w-full bg-gray-800 border border-gray-700/80 rounded-lg px-3 py-1.5 text-xs text-white focus:border-[#92BA56] focus:outline-none">
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="IsInterviewModalOpen = false"
                                class="btn btn-sm btn-outline border-gray-700 text-gray-300 hover:bg-gray-800 hover:text-white">
                            Cancel
                        </button>
                        <button type="submit"
                                class="btn btn-sm bg-[#92BA56] border-0 text-gray-950 hover:bg-[#82a74a]">
                            Save Log
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>

    <!-- Single JSON blob for all applications — parsed once on the client -->
    <script type="application/json" id="applications-data">@json($AllAppPayloads)</script>

    <!-- Vendor Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <!-- Alpine component registration: MUST run before Alpine.start() -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('pageData', () => ({
                IsDetailsModalOpen: false,
                IsInterviewModalOpen: false,
                IsMobileMenuOpen: false,
                SelectedApp: {},
                Earnings: {},
                InterviewsList: [],

                openDetailsModal(App, Calculated) {
                    this.SelectedApp = App || {};
                    this.Earnings = Calculated || {};
                    this.InterviewsList = [];
                    this.IsInterviewModalOpen = false;
                    this.IsDetailsModalOpen = true;
                },

                openInterviewModal(App, Interviews) {
                    this.SelectedApp = App || {};
                    this.InterviewsList = Interviews || [];
                    this.IsDetailsModalOpen = false;
                    this.IsInterviewModalOpen = true;
                },

                // Close details, swap to interview modal.
                switchToInterviewModal() {
                    this.openInterviewModal(this.SelectedApp, this.SelectedApp.interviews || []);
                },
            }));
        });
    </script>

    <!-- DataTables init + delegated row-click (survives every redraw) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ---------- Parse payload map once ----------
            var appMap = {};
            try {
                var dataEl = document.getElementById('applications-data');
                if (dataEl) appMap = JSON.parse(dataEl.textContent) || {};
            } catch (err) {
                console.error('Failed to parse applications data:', err);
            }

            // ---------- Reach the Alpine component root ----------
            function getState() {
                var root = document.querySelector('[x-data="pageData"]');
                if (!root || !window.Alpine) return null;
                return Alpine.$data(root);
            }

            function openFromRow(row) {
                var id = row.getAttribute('data-app-id');
                var data = id && appMap[id];
                if (!data) return;
                var state = getState();
                if (!state) return;
                state.openDetailsModal(data, data.calculated_earnings || {});
            }

            // ---------- Delegated click: whole-row trigger ----------
            document.addEventListener('click', function (e) {
                // Ignore clicks on interactive children (links, buttons, inputs, forms).
                if (e.target.closest('a, button, form, input, select, textarea, label')) return;

                var row = e.target.closest && e.target.closest('.js-open-details');
                if (!row) return;
                openFromRow(row);
            });

            // ---------- Keyboard: Enter / Space on a focused row ----------
            document.addEventListener('keydown', function (e) {
                if (e.key !== 'Enter' && e.key !== ' ') return;
                var row = e.target.closest && e.target.closest('.js-open-details');
                if (!row || e.target !== row) return; // only when the row itself is focused
                e.preventDefault();
                openFromRow(row);
            });

            // ---------- DataTables ----------
            if (!window.jQuery || !jQuery.fn.dataTable) {
                console.warn('DataTables not loaded — table will render as static HTML.');
                return;
            }

            var Table = jQuery('#ApplicationsTable').DataTable({
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                ordering: true,
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search records...",
                    lengthMenu: "Show _MENU_ entries"
                }
            });

            // Custom filter
            jQuery.fn.dataTable.ext.search.push(
                function (Settings, Data, DataIndex) {
                    var SelectedJobType = jQuery('#JobTypeFilter').val();
                    var SelectedStatus = jQuery('#StatusFilter').val();

                    var JobTypeData = Data[1] || '';
                    var InterviewStatusData = Data[4] || '';
                    var StatusData = Data[5] || '';

                    if (SelectedJobType && !JobTypeData.includes(SelectedJobType)) {
                        return false;
                    }

                    if (SelectedStatus) {
                        if (SelectedStatus === 'Interviewed') {
                            if (!InterviewStatusData.includes('Interviewed') && !StatusData.toLowerCase().includes('interviewed')) {
                                return false;
                            }
                        } else {
                            if (StatusData.trim() !== SelectedStatus) {
                                return false;
                            }
                        }
                    }

                    return true;
                }
            );

            jQuery('#JobTypeFilter, #StatusFilter').on('change', function () {
                Table.draw();
            });
        });
    </script>
</x-app-layout>