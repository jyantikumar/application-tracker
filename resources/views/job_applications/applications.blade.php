<x-app-layout>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

    <style>
        [x-cloak] { display: none !important; }

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
        .js-open-details { cursor: pointer; }
        .js-open-details:focus-visible {
            outline: 2px solid #92BA56;
            outline-offset: -2px;
        }

        .modal-scroll::-webkit-scrollbar { width: 6px; height: 6px; }
        .modal-scroll::-webkit-scrollbar-track { background: transparent; }
        .modal-scroll::-webkit-scrollbar-thumb { background: #374151; border-radius: 3px; }
        .modal-scroll::-webkit-scrollbar-thumb:hover { background: #4B5563; }

        .drawer-panel { overscroll-behavior: contain; }

        .tab-strip::-webkit-scrollbar { display: none; }
        .tab-strip { -ms-overflow-style: none; scrollbar-width: none; }
        @media screen and (max-width: 767px) {
    .dataTables_wrapper {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem;
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        float: none !important;
        width: auto !important;
        text-align: left !important;
        margin-bottom: 0 !important;
        flex: 0 1 auto;
    }

    /* Let the search field take the remaining space */
    .dataTables_wrapper .dataTables_filter {
        flex: 1 1 auto;
        min-width: 0;
    }

    .dataTables_wrapper .dataTables_filter input {
        width: 100% !important;
        max-width: 100%;
        box-sizing: border-box;
    }

    /* Table + info + pagination should always be on their own row */
    .dataTables_wrapper > table,
    .dataTables_wrapper > .dataTables_info,
    .dataTables_wrapper > .dataTables_paginate {
        flex: 0 0 100%;
        width: 100%;
    }
}
    </style>

    @php
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
         x-effect="document.body.classList.toggle('overflow-hidden', IsDetailsModalOpen)"
         @keydown.escape.window="IsDetailsModalOpen = false;">

        <!-- Mobile Header -->

        <!-- Main Content -->
        <main class="lg:pl-72 flex-1 max-w-full w-full px-4 sm:px-4 lg:px-4 py-8 space-y-6">
 
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
                        Total: <span class="ml-1 font-bold text-white">{{ count($JobApplications) }}</span>
                    </div>
                </div>
            </div>

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
             Details — Right-side slide-over with tabs
             ============================================================ -->
        <div x-show="IsDetailsModalOpen" x-cloak
             class="fixed inset-0 z-50"
             role="dialog" aria-modal="true">

            <div x-show="IsDetailsModalOpen" x-transition.opacity @click="IsDetailsModalOpen = false"
                 class="absolute inset-0 bg-gray-950/70 backdrop-blur-sm"></div>

            <div x-show="IsDetailsModalOpen"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full"
                 class="drawer-panel absolute top-0 right-0 h-full w-full sm:w-[680px] lg:w-[760px] bg-gray-900 border-l border-gray-800 shadow-2xl flex flex-col">

                <!-- ═══ Sticky top bar ═══ -->
                <div class="shrink-0 border-b border-gray-800 bg-gray-900/95 backdrop-blur-sm">

                    <!-- Mobile: back arrow -->
                    <div class="sm:hidden flex items-center gap-3 px-4 py-3">
                        <button type="button" @click="IsDetailsModalOpen = false"
                                class="shrink-0 w-10 h-10 -ml-2 flex items-center justify-center rounded-lg text-gray-400 active:bg-gray-800 transition-colors"
                                aria-label="Back">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Desktop: breadcrumb + close -->
                    <div class="hidden sm:flex items-center justify-between px-8 py-4">
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <span>Applications</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            <span class="text-gray-300 font-semibold truncate max-w-[240px]"
                                  x-text="SelectedApp.company_name"></span>
                        </div>
                        <button type="button" @click="IsDetailsModalOpen = false"
                                class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:text-white hover:bg-gray-800 transition-colors"
                                aria-label="Close">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- ═══ Hero identity ═══ -->
                    <div class="px-6 sm:px-8 pt-4 sm:pt-5 pb-5">
                        <div class="flex items-start gap-4">
                            
                            <div class="min-w-0 flex-1">
                                <h2 class="text-lg sm:text-xl font-bold text-white tracking-tight truncate"
                                    x-text="SelectedApp.company_name"></h2>
                                <p class="text-sm text-[#92BA56] font-semibold mt-0.5 truncate"
                                   x-text="SelectedApp.position"></p>
                                <div class="flex flex-wrap items-center gap-2 mt-2.5">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-gray-800/80 border border-gray-700 text-[11px] font-semibold text-gray-300">
                                        <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        <span x-text="SelectedApp.job_type || 'N/A'"></span>
                                    </span>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-gray-800/80 border border-gray-700 text-[11px] font-semibold text-gray-300">
                                        <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span x-text="SelectedApp.city || 'N/A'"></span>
                                    </span>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-gray-800/80 border border-gray-700 text-[11px] font-semibold text-gray-300">
                                        <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span x-text="(SelectedApp.hours_per_week || 40) + ' hrs/week'"></span>
                                    </span>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-gray-800/80 border border-gray-700 text-[11px] font-semibold text-gray-300">
                                        <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span x-text="SelectedApp.source || '—'"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ═══ Tab strip ═══ -->
                    <div class="tab-strip overflow-x-auto border-t border-gray-800">
                        <div class="flex items-center gap-1 px-6 sm:px-8 min-w-max">

                            <button type="button" @click="ActiveTab = 'general'"
                                    class="relative px-4 py-3 text-xs font-bold uppercase tracking-wider border-b-2 transition-colors"
                                    :class="ActiveTab === 'general'
                                        ? 'text-white border-[#92BA56]'
                                        : 'text-gray-500 border-transparent hover:text-gray-300'">
                                General
                            </button>

                            <button type="button" @click="ActiveTab = 'description'"
                                    class="relative px-4 py-3 text-xs font-bold uppercase tracking-wider border-b-2 transition-colors"
                                    :class="ActiveTab === 'description'
                                        ? 'text-white border-[#92BA56]'
                                        : 'text-gray-500 border-transparent hover:text-gray-300'">
                                Job Desc.
                            </button>

                            <button type="button" @click="ActiveTab = 'interviews'"
                                    class="relative px-4 py-3 text-xs font-bold uppercase tracking-wider border-b-2 transition-colors inline-flex items-center gap-2"
                                    :class="ActiveTab === 'interviews'
                                        ? 'text-white border-[#92BA56]'
                                        : 'text-gray-500 border-transparent hover:text-gray-300'">
                                Int.
                                <span class="inline-flex items-center justify-center min-w-[18px] h-[18px] px-1.5 rounded-full text-[10px] font-bold"
                                      :class="ActiveTab === 'interviews'
                                          ? 'bg-[#92BA56] text-gray-950'
                                          : 'bg-gray-800 text-gray-400'"
                                      x-text="(SelectedApp.interviews || []).length"></span>
                            </button>

                        </div>
                    </div>
                </div>

                <!-- ═══ Scrollable body ═══ -->
                <div class="flex-1 overflow-y-auto modal-scroll">

                    <!-- ───────────── TAB: GENERAL ───────────── -->
                    <div x-show="ActiveTab === 'general'" class="px-6 sm:px-8 py-6 space-y-7">

                        <div class="rounded-xl bg-gradient-to-r from-[#92BA56]/5 to-transparent border border-gray-800 px-5 py-4">
                            <h4 class="text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-3">Estimated Compensation</h4>
                            <div class="grid grid-cols-4 gap-3">
                                <div>
                                    <span class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest">Hourly</span>
                                    <span class="block text-sm sm:text-base font-bold text-gray-100 tabular-nums mt-1"
                                          x-text="(SelectedApp.currency || '') + ' ' + (Earnings.Hourly || '0.00')"></span>
                                </div>
                                <div>
                                    <span class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest">Daily</span>
                                    <span class="block text-sm sm:text-base font-bold text-gray-100 tabular-nums mt-1"
                                          x-text="(SelectedApp.currency || '') + ' ' + (Earnings.Daily || '0.00')"></span>
                                </div>
                                <div>
                                    <span class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest">Weekly</span>
                                    <span class="block text-sm sm:text-base font-bold text-gray-100 tabular-nums mt-1"
                                          x-text="(SelectedApp.currency || '') + ' ' + (Earnings.Weekly || '0.00')"></span>
                                </div>
                                <div>
                                    <span class="block text-[10px] font-bold text-[#92BA56] uppercase tracking-widest">Monthly</span>
                                    <span class="block text-sm sm:text-base font-black text-[#92BA56] tabular-nums mt-1"
                                          x-text="(SelectedApp.currency || '') + ' ' + (Earnings.Monthly || '0.00')"></span>
                                </div>
                            </div>
                        </div>

                        <section>
                            <h4 class="text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-3">Assessment</h4>
                            <div class="space-y-4">
                                <div class="flex gap-3">
                                    <div class="shrink-0 w-6 h-6 rounded-md bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <div class="min-w-0 pt-0.5">
                                        <span class="text-[11px] font-bold text-emerald-400 uppercase tracking-widest">Pros</span>
                                        <p class="text-sm text-gray-300 leading-relaxed mt-1"
                                           x-text="SelectedApp.pros || 'None specified'"></p>
                                    </div>
                                </div>
                                <div class="flex gap-3">
                                    <div class="shrink-0 w-6 h-6 rounded-md bg-rose-500/10 border border-rose-500/20 flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </div>
                                    <div class="min-w-0 pt-0.5">
                                        <span class="text-[11px] font-bold text-rose-400 uppercase tracking-widest">Cons</span>
                                        <p class="text-sm text-gray-300 leading-relaxed mt-1"
                                           x-text="SelectedApp.cons || 'None specified'"></p>
                                    </div>
                                </div>
                            </div>
                        </section>

                    </div>

                    <!-- ───────────── TAB: DESCRIPTION ───────────── -->
                    <div x-show="ActiveTab === 'description'" class="px-6 sm:px-8 py-6">

                        <template x-if="SelectedApp.ApplicationURL">
                            <a :href="SelectedApp.ApplicationURL" target="_blank" rel="noopener"
                               class="inline-flex items-center gap-2 mb-5 px-3.5 py-2 text-xs font-semibold text-gray-300 bg-gray-800/60 border border-gray-700 rounded-lg hover:bg-gray-800 hover:text-white transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                Open Original Listing
                            </a>
                        </template>

                        <h4 class="text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-3">Full Description</h4>
                        <p class="text-sm text-gray-300 leading-relaxed whitespace-pre-line"
                           x-text="SelectedApp.description || 'No description provided.'"></p>

                    </div>

                    <!-- ───────────── TAB: INTERVIEWS ───────────── -->
                    <div x-show="ActiveTab === 'interviews'" class="px-6 sm:px-8 py-6 space-y-7">

                        <section>
                            <h4 class="text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-4">Logged Rounds</h4>

                            <template x-if="InterviewsList.length === 0">
                                <div class="rounded-xl border border-dashed border-gray-700/60 bg-gray-800/20 px-6 py-8 text-center">
                                    <svg class="w-8 h-8 text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="text-sm text-gray-500">No interviews logged yet.</p>
                                </div>
                            </template>

                            <div class="relative pl-6 space-y-5">
                                <template x-for="(Interview, Idx) in InterviewsList" :key="Interview.id">
                                    <div class="relative">
                                        <span class="absolute -left-6 top-1 w-3 h-3 rounded-full border-2 border-gray-900"
                                              :class="{
                                                  'bg-emerald-400': Interview.status === 'Completed',
                                                  'bg-amber-400': Interview.status === 'Scheduled',
                                                  'bg-rose-400': Interview.status === 'Cancelled',
                                                  'bg-gray-500': !['Completed','Scheduled','Cancelled'].includes(Interview.status)
                                              }"></span>
                                        <span class="absolute -left-[19px] top-5 bottom-[-20px] w-px bg-gray-800"
                                              x-show="Idx < InterviewsList.length - 1"></span>

                                        <div>
                                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                                <span class="text-sm font-bold text-white" x-text="Interview.interview_type"></span>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide border"
                                                      :class="{
                                                          'bg-emerald-500/10 text-emerald-400 border-emerald-500/25': Interview.status === 'Completed',
                                                          'bg-amber-500/10 text-amber-400 border-amber-500/25': Interview.status === 'Scheduled',
                                                          'bg-rose-500/10 text-rose-400 border-rose-500/25': Interview.status === 'Cancelled',
                                                          'bg-gray-500/10 text-gray-400 border-gray-500/25': !['Completed','Scheduled','Cancelled'].includes(Interview.status)
                                                      }"
                                                      x-text="Interview.status"></span>
                                            </div>
                                            <p class="text-xs text-gray-500 mb-1.5"
                                               x-text="Interview.scheduled_at ? new Date(Interview.scheduled_at).toLocaleString() : 'Date not set'"></p>
                                            <p class="text-sm text-gray-300 leading-relaxed"
                                               x-text="Interview.notes || 'No notes added.'"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </section>

                        <section class="pt-2 border-t border-gray-800">
                            <h4 class="text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-4">Log New Round</h4>

                            <!-- Form: note the id="interview-form" — the Save button in the footer references it -->
                            <form id="interview-form"
                                  :action="'/job-applications/' + SelectedApp.id + '/interviews'"
                                  method="POST"
                                  class="space-y-4">
                                @csrf

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Interview Type <span class="text-rose-400">*</span></label>
                                        <select name="interview_type" required
                                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:border-[#92BA56] focus:ring-1 focus:ring-[#92BA56]/40 focus:outline-none transition-colors">
                                            <option value="HR Screening">HR Screening</option>
                                            <option value="Technical Assessment">Technical Assessment</option>
                                            <option value="Technical Interview">Technical Interview</option>
                                            <option value="Final / Executive">Final / Executive</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Scheduled Date &amp; Time <span class="text-rose-400">*</span></label>
                                        <input type="datetime-local" name="scheduled_at" required
                                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:border-[#92BA56] focus:ring-1 focus:ring-[#92BA56]/40 focus:outline-none transition-colors">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Status <span class="text-rose-400">*</span></label>
                                        <select name="status" required
                                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:border-[#92BA56] focus:ring-1 focus:ring-[#92BA56]/40 focus:outline-none transition-colors">
                                            <option value="Scheduled">Scheduled</option>
                                            <option value="Completed">Completed</option>
                                            <option value="Cancelled">Cancelled</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Notes / Feedback</label>
                                        <input type="text" name="notes" placeholder="Interviewer, tech stack..."
                                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:border-[#92BA56] focus:ring-1 focus:ring-[#92BA56]/40 focus:outline-none transition-colors">
                                    </div>
                                </div>
                            </form>
                        </section>

                    </div>

                </div>

                <!-- ═══ Sticky footer ═══ -->
                <div class="shrink-0 border-t border-gray-800 bg-gray-900/95 backdrop-blur-sm px-6 sm:px-8 py-4">
                    <div class="flex items-center gap-2">

                        <!-- Delete (icon only) -->
                        <form :action="'/job-applications/' + SelectedApp.id" method="POST" class="inline shrink-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this application record?')"
                                    title="Delete"
                                    class="w-11 h-11 inline-flex items-center justify-center text-rose-400 bg-transparent border border-gray-800 rounded-lg hover:bg-rose-500/10 hover:border-rose-500/30 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>

                        <!-- Interviews toggle (shown when NOT on interviews tab) -->
                        <button type="button" @click="ActiveTab = 'interviews'"
                                x-show="ActiveTab !== 'interviews'"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-semibold text-gray-300 bg-gray-800 border border-gray-700 rounded-lg hover:bg-gray-750 hover:text-white transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Interviews
                            <span class="inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full bg-gray-700 text-[10px] font-bold text-gray-200"
                                  x-text="(SelectedApp.interviews || []).length"></span>
                        </button>

                        <!-- Save Interview (shown ONLY on interviews tab) — submits form by ID -->
                        <button type="submit"
                                form="interview-form"
                                x-show="ActiveTab === 'interviews'"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-bold text-gray-950 bg-[#92BA56] rounded-lg hover:bg-[#82a74a] transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Save Interview
                        </button>

                        <!-- Edit (primary) -->
                        <a :href="'/job-applications/' + SelectedApp.id + '/edit'"
                           class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-bold text-gray-950 bg-[#92BA56] rounded-lg hover:bg-[#82a74a] transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            <span class="hidden sm:inline">Edit Application</span>
                            <span class="sm:hidden">Edit</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <script type="application/json" id="applications-data">@json($AllAppPayloads)</script>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('pageData', () => ({
                IsDetailsModalOpen: false,
                IsMobileMenuOpen: false,
                ActiveTab: 'general',
                SelectedApp: {},
                Earnings: {},
                InterviewsList: [],

                openDetailsModal(App, Calculated) {
                    this.SelectedApp = App || {};
                    this.Earnings = Calculated || {};
                    this.InterviewsList = App?.interviews || [];
                    this.ActiveTab = 'general';
                    this.IsDetailsModalOpen = true;
                },
            }));
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var appMap = {};
            try {
                var dataEl = document.getElementById('applications-data');
                if (dataEl) appMap = JSON.parse(dataEl.textContent) || {};
            } catch (err) {
                console.error('Failed to parse applications data:', err);
            }

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

            document.addEventListener('click', function (e) {
                if (e.target.closest('a, button, form, input, select, textarea, label')) return;
                var row = e.target.closest && e.target.closest('.js-open-details');
                if (!row) return;
                openFromRow(row);
            });

            document.addEventListener('keydown', function (e) {
                if (e.key !== 'Enter' && e.key !== ' ') return;
                var row = e.target.closest && e.target.closest('.js-open-details');
                if (!row || e.target !== row) return;
                e.preventDefault();
                openFromRow(row);
            });

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