<x-app-layout active="dashboard">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">

    <style>
        /* Custom DataTables Styling */
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
        table.dataTable thead th,
        table.dataTable.no-footer {
            border-bottom: 1px solid rgba(55, 65, 81, 0.6) !important;
        }
    </style>

    <div class="flex flex-col min-h-screen bg-[#0B1120] text-gray-100">

        <!-- Main Content Wrapper -->
        <main class="w-full lg:pl-72 flex-1 px-4 sm:px-6 py-6 space-y-6">

            @if ($message = session('success'))
                <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center justify-between shadow-md">
                    <span>{{ $message }}</span>
                    <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-200">&times;</button>
                </div>
            @endif

            <!-- KPI Cards Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3">
                <div class="bg-gray-800/60 border border-gray-700/60 rounded-xl p-3 text-center shadow-md">
                    <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Total Application</span>
                    <span class="text-2xl font-black text-white mt-1 block">{{ $TotalApplications }}</span>
                </div>
                <div class="bg-gray-800/60 border border-gray-700/60 rounded-xl p-3 text-center shadow-md">
                    <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Interviewed Roles</span>
                    <span class="text-2xl font-black text-amber-400 mt-1 block">{{ $InterviewsDone }}</span>
                </div>
                <div class="bg-gray-800/60 border border-gray-700/60 rounded-xl p-3 text-center shadow-md">
                    <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Applied</span>
                    <span class="text-2xl font-black text-blue-400 mt-1 block">{{ $AppliedCount }}</span>
                </div>
                <div class="bg-gray-800/60 border border-gray-700/60 rounded-xl p-3 text-center shadow-md">
                    <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Under Review</span>
                    <span class="text-2xl font-black text-indigo-400 mt-1 block">{{ $UnderReviewCount }}</span>
                </div>
                <div class="bg-gray-800/60 border border-gray-700/60 rounded-xl p-3 text-center shadow-md">
                    <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Interviewing</span>
                    <span class="text-2xl font-black text-purple-400 mt-1 block">{{ $CurrentlyInterviewing }}</span>
                </div>
                <div class="bg-gray-800/60 border border-gray-700/60 rounded-xl p-3 text-center shadow-md">
                    <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Hired</span>
                    <span class="text-2xl font-black text-[#92BA56] mt-1 block">{{ $HiredCount }}</span>
                </div>
                <div class="bg-gray-800/60 border border-gray-700/60 rounded-xl p-3 text-center shadow-md">
                    <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Rejected</span>
                    <span class="text-2xl font-black text-rose-400 mt-1 block">{{ $RejectedCount }}</span>
                </div>
                <div class="bg-gray-800/60 border border-gray-700/60 rounded-xl p-3 text-center shadow-md">
                    <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Waiting Response</span>
                    <span class="text-2xl font-black text-sky-400 mt-1 block">{{ $WaitingResponse }}</span>
                </div>

                <!-- Custom Statuses (If user created any additional statuses) -->
                @if (isset($StatusCounts) && count($StatusCounts) > 0)
                    @foreach ($StatusCounts as $StatusName =>$StatusCount)
                        @if (!in_array($StatusName, ['Applied', 'Under Review', 'Currently Interviewing', 'Hired', 'Rejected', 'Waiting response']))
                            <div class="bg-gray-800/60 border border-gray-700/60 rounded-xl p-3 text-center shadow-md">
                                <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider truncate" title="{{ $StatusName }}">{{ $StatusName }}</span>
                                <span class="text-2xl font-black text-[#92BA56] mt-1 block">{{ $StatusCount }}</span>
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>
            

            <!-- Top Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-gray-800/40 border border-gray-700/60 rounded-xl p-5 shadow-xl">
                    <h3 class="text-sm font-bold text-gray-200 mb-4 uppercase tracking-wider">Applications by Source</h3>
                    <div class="h-64"><canvas id="SourceChart"></canvas></div>
                </div>
                <div class="bg-gray-800/40 border border-gray-700/60 rounded-xl p-5 shadow-xl">
                    <h3 class="text-sm font-bold text-gray-200 mb-4 uppercase tracking-wider">Applications over Time</h3>
                    <div class="h-64"><canvas id="DateChart"></canvas></div>
                </div>
                <div class="bg-gray-800/40 border border-gray-700/60 rounded-xl p-5 shadow-xl">
                    <h3 class="text-sm font-bold text-gray-200 mb-4 uppercase tracking-wider">Applications by Work Setup</h3>
                    <div class="h-64 flex items-center justify-center"><canvas id="WorkSetupChart"></canvas></div>
                </div>
               
            </div>
             <div class="bg-gray-800/40 border border-gray-700/60 rounded-xl p-5 shadow-xl">
                <h3 class="text-sm font-bold text-gray-200 mb-4 uppercase tracking-wider">Applications by Location</h3>
                <div class="h-64"><canvas id="LocationChart"></canvas></div>
            </div>

            <!-- Location Chart -->
            

            <!-- Top 10 Recent Applications Table -->
            <div class="bg-gray-800/40 border border-gray-700/60 rounded-xl p-4 overflow-hidden shadow-xl">
                <div class="pb-4 border-b border-gray-700/60 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-white">Top 10 Recent Applications</h3>
                        <p class="text-xs text-gray-400">Latest logged records</p>
                    </div>
                    <a href="{{ route('job-applications.applications') }}" class="text-xs font-semibold text-[#92BA56] hover:underline">View All &rarr;</a>
                </div>
                <div class="overflow-x-auto pt-4">
                    <table id="RecentApplicationsTable" class="w-full text-left text-sm text-gray-300">
                        <thead class="bg-gray-800/80 border-b border-gray-700/60 text-xs uppercase text-gray-400">
                            <tr>
                                <th class="py-4 px-6">Company & Position</th>
                                <th class="py-4 px-6">Setup & City</th>
                                <th class="py-4 px-6">Compensation</th>
                                <th class="py-4 px-6">Date Applied</th>
                                <th class="py-4 px-6">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700/40">
                            @if (count($RecentApplications) > 0)
                                @foreach ($RecentApplications as $Application)
                                    <tr class="hover:bg-gray-800/50 transition-colors">
                                        <td class="py-4 px-6">
                                            <div class="font-semibold text-white">{{ $Application->company_name }}</div>
                                            <div class="text-gray-400 text-xs mt-0.5">{{ $Application->position }}</div>
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <div class="text-gray-200">{{ $Application->job_type }}</div>
                                            <div class="text-gray-400 text-xs">{{ $Application->city ?? 'N/A' }}</div>
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap font-medium text-[#92BA56]">
                                            {{ $Application->formatted_salary }}
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap text-xs text-gray-300">
                                            {{ $Application->created_at ? $Application->created_at->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <span class="px-2.5 py-1 rounded-full text-xs font-medium border bg-gray-700/50 text-gray-300 border-gray-600/30">
                                                {{ $Application->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="py-8 px-6 text-center text-gray-400">No applications recorded yet.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <!-- Chart.js Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
    
    <!-- DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function () {$('#RecentApplicationsTable').DataTable({
                pageLength: 5,
                lengthMenu: [5, 10, 25],
                ordering: false,
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search recent...",
                    lengthMenu: "Show _MENU_ entries"
                }
            });
        });

        const SourceData = @json($ApplicationsBySource);
        const DateData = @json($ApplicationsByDate);
        const WorkSetupData = @json($ApplicationsByJobType);
        const LocationData = @json($ApplicationsByLocation);

        Chart.register(ChartDataLabels);

        new Chart(document.getElementById('SourceChart'), {
            type: 'bar',
            data: { labels: Object.keys(SourceData), datasets: [{ data: Object.values(SourceData), backgroundColor: '#92BA56', borderRadius: 4 }] },
            options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, datalabels: { display: false } }, scales: { x: { ticks: { color: '#9ca3af' }, grid: { color: '#374151' } }, y: { ticks: { color: '#9ca3af' }, grid: { display: false } } } }
        });

        new Chart(document.getElementById('DateChart'), {
            type: 'line',
            data: { labels: Object.keys(DateData), datasets: [{ data: Object.values(DateData), borderColor: '#38bdf8', backgroundColor: 'rgba(56, 189, 248, 0.1)', borderWidth: 2, tension: 0.4, fill: true }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, datalabels: { display: false } }, scales: { x: { ticks: { color: '#9ca3af' }, grid: { color: '#374151' } }, y: { ticks: { color: '#9ca3af' }, grid: { color: '#374151' } } } }
        });

        new Chart(document.getElementById('WorkSetupChart'), {
            type: 'doughnut',
            data: { labels: Object.keys(WorkSetupData), datasets: [{ data: Object.values(WorkSetupData), backgroundColor: ['#92BA56', '#38bdf8', '#7832a8', '#f59e0b', '#f43f5e'], borderWidth: 2, borderColor: '#1f2937' }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { color: '#d1d5db', padding: 12 } }, datalabels: { color: '#ffffff', font: { weight: 'bold', size: 11 }, formatter: (Val, Ctx) => { const Tot = Ctx.chart.data.datasets[0].data.reduce((a, b) => a + Number(b), 0); return Tot ? `${((Val / Tot) * 100).toFixed(1)}%` : '0%'; } } }, cutout: '60%' }
        });

        new Chart(document.getElementById('LocationChart'), {
            type: 'bar',
            data: { labels: Object.keys(LocationData), datasets: [{ data: Object.values(LocationData), backgroundColor: '#38bdf8', borderRadius: 4 }] },
            options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, datalabels: { display: false } }, scales: { x: { ticks: { color: '#9ca3af' }, grid: { color: '#374151' } }, y: { ticks: { color: '#9ca3af' }, grid: { display: false } } } }
        });
    </script>
</x-app-layout>