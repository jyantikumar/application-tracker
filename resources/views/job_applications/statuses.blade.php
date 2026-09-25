<x-app-layout>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

    <style>
        [x-cloak] { display: none !important; }

        /* Dark theme for DataTables */
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
        table.dataTable tbody tr {
            background-color: transparent !important;
        }
        table.dataTable tbody tr:hover {
            background-color: rgba(31, 41, 55, 0.4) !important;
        }
    </style>

    <div class="flex flex-col min-h-screen" x-data="statusesPage"
         x-effect="document.body.classList.toggle('overflow-hidden', EditOpen)"
         @keydown.escape.window="closeEdit()">

        <main class="lg:pl-72 flex-1 max-w-full w-full px-4 sm:px-4 lg:px-4 py-8 space-y-6">

            @if($errors->any())
                <div class="alert bg-rose-500/10 border border-rose-500/30 text-rose-400 shadow-md" role="alert">
                    <ul class="list-disc list-inside text-xs space-y-0.5">
                        @foreach($errors->all() as $Error)
                            <li>{{ $Error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Page Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-white tracking-tight">Application Statuses</h1>
                    <p class="text-gray-400 text-sm mt-0.5">Manage the pipeline stages you use to track applications.</p>
                </div>

                <div class="badge bg-gray-800/80 border border-gray-700/80 text-gray-300 text-xs py-3.5 px-3">
                    Total Statuses: <span class="ml-1 font-bold text-white">{{ $Statuses->count() }}</span>
                </div>
            </div>

            <!-- Add Status Form -->
            <div class="card bg-gray-900/60 border border-gray-800 shadow-xl p-4">
                <form method="POST" action="{{ route('application-statuses.store') }}" class="flex flex-col sm:flex-row items-stretch gap-3">
                    @csrf
                    <input type="text" name="name" required maxlength="100"
                           value="{{ old('name') }}"
                           placeholder="e.g. Applied, Interviewing, Offered…"
                           class="flex-1 bg-gray-800/80 border border-gray-700/80 rounded-lg px-3 py-2 text-sm text-white placeholder-gray-500 focus:border-[#92BA56] focus:outline-none">
                    <button type="submit"
                            class="btn btn-sm bg-[#92BA56] border-0 text-gray-950 hover:bg-[#82a74a] whitespace-nowrap">
                        + Add Status
                    </button>
                </form>
            </div>

            <!-- Statuses DataTable -->
            <div class="card bg-gray-900/60 border border-gray-800 shadow-xl p-4 overflow-hidden">
                <div class="overflow-x-auto">
                    <table id="StatusesTable" class="table w-full text-left">
                        <thead class="bg-gray-800/80 text-xs uppercase tracking-wider text-gray-400 border-b border-gray-700/60">
                            <tr>
                                <th class="py-4 px-6">Name</th>
                                <th class="py-4 px-6">Created</th>
                                <th class="py-4 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800/60">
                            @foreach($Statuses as $Status)
                                <tr>
                                    <td class="py-4 px-6">
                                        <span class="font-semibold text-white">{{ $Status->name }}</span>
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap" data-order="{{ $Status->created_at->timestamp }}">
                                        <span class="text-xs text-gray-400">{{ $Status->created_at->format('M j, Y') }}</span>
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap text-right">
                                        <div class="inline-flex items-center gap-2">
                                            <button type="button"
                                                    class="js-edit-status btn btn-sm btn-outline border-[#92BA56]/40 text-[#92BA56] hover:bg-[#92BA56] hover:text-gray-950"
                                                    data-id="{{ $Status->id }}"
                                                    data-name="{{ $Status->name }}"
                                                    title="Edit status">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Edit
                                            </button>

                                            <form action="{{ route('application-statuses.destroy', $Status) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Delete the status &quot;{{ $Status->name }}&quot;?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline border-rose-500/40 text-rose-400 hover:bg-rose-500 hover:text-gray-950"
                                                        title="Delete status">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </main>

        <!-- Alpine Edit Modal -->
        <div x-show="EditOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
            <div x-show="EditOpen" x-transition.opacity @click="closeEdit()"
                 class="absolute inset-0 bg-gray-950/80 backdrop-blur-sm"></div>

            <div x-show="EditOpen" x-transition
                 class="relative z-10 max-w-md w-full bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl p-6">

                <div class="flex items-start justify-between border-b border-gray-800 pb-4">
                    <div>
                        <h3 class="text-lg font-bold text-white">Edit Status</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Update the name of this status.</p>
                    </div>
                    <button type="button" @click="closeEdit()" class="btn btn-text btn-circle btn-sm text-gray-400 hover:text-white">&times;</button>
                </div>

                <form :action="'/application-statuses/' + EditId" method="POST" class="pt-4 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs text-gray-400 font-semibold mb-1">Name *</label>
                        <input type="text" name="name" required maxlength="100"
                               x-model="EditName"
                               class="w-full bg-gray-800 border border-gray-700/80 rounded-lg px-3 py-2 text-sm text-white focus:border-[#92BA56] focus:outline-none">
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="closeEdit()"
                                class="btn btn-sm btn-outline border-gray-700 text-gray-300 hover:bg-gray-800 hover:text-white">
                            Cancel
                        </button>
                        <button type="submit"
                                class="btn btn-sm bg-[#92BA56] border-0 text-gray-950 hover:bg-[#82a74a]">
                            Save Changes
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>

    <!-- Vendor Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <script>
        // ---------- Alpine component ----------
        document.addEventListener('alpine:init', () => {
            Alpine.data('statusesPage', () => ({
                EditOpen: false,
                EditId: null,
                EditName: '',

                openEdit(id, name) {
                    this.EditId = id;
                    this.EditName = name;
                    this.EditOpen = true;
                },
                closeEdit() {
                    this.EditOpen = false;
                    this.EditId = null;
                    this.EditName = '';
                },
            }));
        });

        // ---------- DataTables + delegated edit handler ----------
        document.addEventListener('DOMContentLoaded', function () {
            if (window.jQuery && jQuery.fn.dataTable) {
                jQuery('#StatusesTable').DataTable({
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    ordering: true,
                    responsive: true,
                    columnDefs: [{ orderable: false, targets: -1 }],
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search statuses...",
                        lengthMenu: "Show _MENU_ entries",
                        emptyTable: "No statuses yet. Add one above to get started.",
                        zeroRecords: "No matching statuses found."
                    }
                });
            }

            // Delegated: survives every DataTables redraw.
            document.addEventListener('click', function (e) {
                var btn = e.target.closest && e.target.closest('.js-edit-status');
                if (!btn) return;
                e.preventDefault();

                // Reach the Alpine component root (the div with x-data="statusesPage").
                var root = btn.closest('[x-data]');
                if (!root || !window.Alpine) return;

                var data = Alpine.$data(root);
                data.openEdit(btn.getAttribute('data-id'), btn.getAttribute('data-name'));
            });
        });
    </script>
</x-app-layout>