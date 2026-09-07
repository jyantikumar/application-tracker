<x-app-layout active="dashboard">
    <div class="py-6 bg-[#0B1120] min-h-screen text-gray-100">
        <!-- Expanded container to fill width -->
        <div class="w-full lg:pl-72 px-4 sm:px-6 space-y-6">

            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center justify-between">
                    <span>{{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-200">&times;</button>
                </div>
            @endif

            <!-- KPI Cards Grid (Fills full width) -->
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3">
                <div class="bg-gray-800/60 border border-gray-700/60 rounded-xl p-3 text-center">
                    <span class="block text-[11px] font-semibold text-gray-400 uppercase">Total Application</span>
                    <span class="text-2xl font-black text-white mt-1 block">{{ $TotalApplications }}</span>
                </div>
                <div class="bg-gray-800/60 border border-gray-700/60 rounded-xl p-3 text-center">
                    <span class="block text-[11px] font-semibold text-gray-400 uppercase">Interviewed Roles</span>
                    <span class="text-2xl font-black text-amber-400 mt-1 block">{{ $InterviewsDone }}</span>
                </div>
                <div class="bg-gray-800/60 border border-gray-700/60 rounded-xl p-3 text-center">
                    <span class="block text-[11px] font-semibold text-gray-400 uppercase">Applied</span>
                    <span class="text-2xl font-black text-blue-400 mt-1 block">{{ $AppliedCount }}</span>
                </div>
                <div class="bg-gray-800/60 border border-gray-700/60 rounded-xl p-3 text-center">
                    <span class="block text-[11px] font-semibold text-gray-400 uppercase">Under Review</span>
                    <span class="text-2xl font-black text-indigo-400 mt-1 block">{{ $UnderReviewCount }}</span>
                </div>
                <div class="bg-gray-800/60 border border-gray-700/60 rounded-xl p-3 text-center">
                    <span class="block text-[11px] font-semibold text-gray-400 uppercase">Interviewing</span>
                    <span class="text-2xl font-black text-purple-400 mt-1 block">{{ $CurrentlyInterviewing }}</span>
                </div>
                <div class="bg-gray-800/60 border border-gray-700/60 rounded-xl p-3 text-center">
                    <span class="block text-[11px] font-semibold text-gray-400 uppercase">Hired</span>
                    <span class="text-2xl font-black text-[#92BA56] mt-1 block">{{ $HiredCount }}</span>
                </div>
                <div class="bg-gray-800/60 border border-gray-700/60 rounded-xl p-3 text-center">
                    <span class="block text-[11px] font-semibold text-gray-400 uppercase">Rejected</span>
                    <span class="text-2xl font-black text-rose-400 mt-1 block">{{ $RejectedCount }}</span>
                </div>
                <div class="bg-gray-800/60 border border-gray-700/60 rounded-xl p-3 text-center">
                    <span class="block text-[11px] font-semibold text-gray-400 uppercase">Waiting Response</span>
                    <span class="text-2xl font-black text-sky-400 mt-1 block">{{ $WaitingResponse }}</span>
                </div>
            </div>

            <!-- Top Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-gray-800/40 border border-gray-700/60 rounded-xl p-5">
                    <h3 class="text-sm font-bold text-gray-200 mb-4 uppercase">Applications by Source</h3>
                    <div class="h-64"><canvas id="SourceChart"></canvas></div>
                </div>
                <div class="bg-gray-800/40 border border-gray-700/60 rounded-xl p-5">
                    <h3 class="text-sm font-bold text-gray-200 mb-4 uppercase">Applications over Time</h3>
                    <div class="h-64"><canvas id="DateChart"></canvas></div>
                </div>
                <div class="bg-gray-800/40 border border-gray-700/60 rounded-xl p-5">
                    <h3 class="text-sm font-bold text-gray-200 mb-4 uppercase">Applications by Work Setup</h3>
                    <div class="h-64 flex items-center justify-center"><canvas id="WorkSetupChart"></canvas></div>
                </div>
            </div>

            <!-- Location Chart -->
            <div class="bg-gray-800/40 border border-gray-700/60 rounded-xl p-5">
                <h3 class="text-sm font-bold text-gray-200 mb-4 uppercase">Applications by Location</h3>
                <div class="h-64"><canvas id="LocationChart"></canvas></div>
            </div>

            <!-- Top 10 Recent Applications Table -->
            <div class="bg-gray-800/40 border border-gray-700/60 rounded-xl overflow-hidden shadow-xl">
                <div class="p-4 border-b border-gray-700/60 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-white">Top 10 Recent Applications</h3>
                        <p class="text-xs text-gray-400">Latest logged records</p>
                    </div>
                    <a href="{{ route('job-applications.applications') }}" class="text-xs font-semibold text-[#92BA56] hover:underline">View All &rarr;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-300">
                        <thead class="bg-gray-800/80 border-b border-gray-700/60 text-xs uppercase text-gray-400">
                            <tr>
                                <th class="px-6 py-4">Company & Position</th>
                                <th class="px-6 py-4">Setup & City</th>
                                <th class="px-6 py-4">Compensation</th>
                                <th class="px-6 py-4">Date Applied</th>
                                <th class="px-6 py-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700/40">
                            @forelse($RecentApplications as $Application)
                                <tr class="hover:bg-gray-800/50">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-white">{{ $Application->company_name }}</div>
                                        <div class="text-gray-400 text-xs">{{ $Application->position }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div>{{ $Application->job_type }}</div>
                                        <div class="text-gray-400 text-xs">{{ $Application->city }}</div>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-[#92BA56]">{{ $Application->formatted_salary }}</td>
                                    <td class="px-6 py-4 text-xs text-gray-300">
                                        {{ $Application->created_at ? $Application->created_at->format('M d, Y') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-medium border bg-gray-700/50 text-gray-300 border-gray-600/30">
                                            {{ $Application->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-400">No applications recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Chart.js Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
    <script>
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