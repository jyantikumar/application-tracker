<x-app-layout>
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
                    
                    <!-- Updated button with pure Tailwind -->
                    <button type="submit"
                            class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-gray-950 bg-[#92BA56] rounded-lg hover:bg-[#82a74a] transition-colors whitespace-nowrap">
                        + Add Status
                    </button>
                </form>
            </div>

            <!-- Statuses DataTable -->
            <div class="card bg-gray-900/60 border border-gray-800 shadow-xl p-4 overflow-hidden">
                <div class="overflow-x-auto [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
                    <table id="StatusesTable" class="w-full text-left text-sm text-gray-300">
                        <thead class="bg-gray-800/80 text-xs uppercase tracking-wider text-gray-400 border-b border-gray-700/60">
                            <tr>
                                <th class="py-4 px-6 font-semibold">Name</th>
                                <th class="py-4 px-6 font-semibold">Created</th>
                                <th class="py-4 px-6 font-semibold text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800/60">
                            @foreach($Statuses as $Status)
                                <tr class="hover:bg-gray-800/40 transition-colors">
                                    <td class="py-4 px-6">
                                        <span class="font-semibold text-white">{{ $Status->name }}</span>
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap" data-order="{{ $Status->created_at->timestamp }}">
                                        <span class="text-xs text-gray-400">{{ $Status->created_at->format('M j, Y') }}</span>
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap text-right">
                                        <div class="inline-flex items-center gap-2">
                                            <button type="button"
                                                    class="js-edit-status inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-[#92BA56] bg-transparent border border-[#92BA56]/40 rounded-lg hover:bg-[#92BA56] hover:text-gray-950 transition-colors"
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
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-rose-400 bg-transparent border border-rose-500/40 rounded-lg hover:bg-rose-500 hover:text-gray-950 transition-colors"
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
                    <button type="button" @click="closeEdit()" class="text-gray-400 hover:text-white transition-colors text-xl leading-none">&times;</button>
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
                                class="px-3 py-1.5 text-xs font-semibold text-gray-300 bg-transparent border border-gray-700 rounded-lg hover:bg-gray-800 hover:text-white transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-3 py-1.5 text-xs font-bold text-gray-950 bg-[#92BA56] rounded-lg hover:bg-[#82a74a] transition-colors">
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
                    dom: '<"flex flex-col sm:flex-row items-center justify-between gap-4 mb-4"lf>rt<"flex flex-col sm:flex-row items-center justify-between gap-4 mt-4"ip>',
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search statuses...",
                        lengthMenu: "Show _MENU_ entries",
                        emptyTable: "No statuses yet. Add one above to get started.",
                        zeroRecords: "No matching statuses found.",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        paginate: {
                            previous: "Previous",
                            next: "Next"
                        }
                    },
                    initComplete: function () {
                        const wrapper = this.api().table().container();
                        jQuery(wrapper).find('.dataTables_length select').addClass('bg-gray-800/80 border border-gray-700/80 rounded-lg px-3 py-1.5 text-xs text-gray-200 focus:border-[#92BA56] focus:outline-none');
                        jQuery(wrapper).find('.dataTables_filter input').addClass('bg-gray-800/80 border border-gray-700/80 rounded-lg px-3 py-1.5 text-xs text-gray-200 placeholder-gray-500 focus:border-[#92BA56] focus:outline-none ml-2');
                        jQuery(wrapper).find('.dataTables_info').addClass('text-xs text-gray-400');
                        jQuery(wrapper).find('.dataTables_paginate').addClass('flex items-center gap-1');
                        jQuery(wrapper).find('.paginate_button').addClass('px-2.5 py-1 text-xs text-gray-300 rounded-md hover:bg-gray-800 hover:text-white transition-colors cursor-pointer');
                        jQuery(wrapper).find('.paginate_button.current').addClass('bg-[#92BA56] text-gray-950 font-bold hover:bg-[#82a74a]');
                    }
                });
            }

            document.addEventListener('click', function (e) {
                var btn = e.target.closest && e.target.closest('.js-edit-status');
                if (!btn) return;
                e.preventDefault();

                var root = btn.closest('[x-data]');
                if (!root || !window.Alpine) return;

                var data = Alpine.$data(root);
                data.openEdit(btn.getAttribute('data-id'), btn.getAttribute('data-name'));
            });
        });
    </script>
</x-app-layout>