<aside
    class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full border-r border-[var(--gov-border)] bg-[var(--gov-navy)] text-white transition md:static md:translate-x-0 no-print"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
>
    <div class="flex h-full flex-col">
        <div class="border-b border-white/10 px-3 py-3">
            <p class="text-[10px] font-semibold uppercase tracking-wider text-white/70">Modules</p>
        </div>

        @php
            $routeExists = fn (string $name): bool => \Illuminate\Support\Facades\Route::has($name);
        @endphp

        <nav class="flex-1 space-y-1 px-2 py-3 text-xs">
            <x-admin-sidebar-item route="admin.dashboard" label="Dashboard">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9v9a1 1 0 01-1 1h-5v-7H9v7H4a1 1 0 01-1-1z"/>
                </svg>
            </x-admin-sidebar-item>

            @can('log_postal_intake')
                <x-admin-sidebar-item
                    route="admin.postal-intake.index"
                    active="admin.postal-intake.*"
                    label="Postal Intake"
                    :disabled="!$routeExists('admin.postal-intake.index')"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8h18v10H3z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l9 6 9-6"/>
                    </svg>
                </x-admin-sidebar-item>
            @endcan

            @can('scrutinize_applications')
                <x-admin-sidebar-item
                    route="admin.scrutiny.index"
                    active="admin.scrutiny.*"
                    label="Scrutiny"
                    :disabled="!$routeExists('admin.scrutiny.index')"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                    </svg>
                </x-admin-sidebar-item>
            @endcan

            <x-admin-sidebar-item
                route="admin.posts.index"
                active="admin.posts.*"
                label="Recruitment Config"
                :disabled="!$routeExists('admin.posts.index')"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15a3 3 0 100-6 3 3 0 000 6z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 01-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09a1.65 1.65 0 00-1-1.51 1.65 1.65 0 00-1.82.33l-.06.06A2 2 0 013.4 18.9l.06-.06A1.65 1.65 0 003.8 17a1.65 1.65 0 00-1.51-1H2a2 2 0 010-4h.09a1.65 1.65 0 001.51-1 1.65 1.65 0 00-.33-1.82l-.06-.06A2 2 0 014.9 3.4l.06.06A1.65 1.65 0 006.8 3.8a1.65 1.65 0 001-1.51V2a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06A2 2 0 0120.6 5.1l-.06.06A1.65 1.65 0 0019.4 7c0 .67.26 1.3.33 1.82z"/>
                </svg>
            </x-admin-sidebar-item>

            <x-admin-sidebar-item
                route="admin.shortlists.index"
                active="admin.shortlists.*"
                label="Shortlisting"
                :disabled="!$routeExists('admin.shortlists.index')"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h10M4 18h16"/>
                </svg>
            </x-admin-sidebar-item>

            <x-admin-sidebar-item
                route="admin.skill-tests.index"
                active="admin.skill-tests.*"
                label="Skill Test & Interview"
                :disabled="!$routeExists('admin.skill-tests.index')"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 21h8M12 17v4M6 3h12v10H6z"/>
                </svg>
            </x-admin-sidebar-item>

            <x-admin-sidebar-item
                route="admin.merit.index"
                active="admin.merit.*"
                label="Merit Engine"
                :disabled="!$routeExists('admin.merit.index')"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2l3 7h7l-5.5 4.2L18 21l-6-3.8L6 21l1.5-7.8L2 9h7l3-7z"/>
                </svg>
            </x-admin-sidebar-item>

            <x-admin-sidebar-item
                label="Experience Marking"
                :disabled="true"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10M7 11h10M7 15h6"/>
                    <rect x="4" y="3" width="16" height="18" rx="2" ry="2"/>
                </svg>
            </x-admin-sidebar-item>

            <x-admin-sidebar-item
                route="admin.appointment-orders.index"
                active="admin.appointment-orders.*"
                label="Appointment Orders"
                :disabled="!$routeExists('admin.appointment-orders.index')"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/>
                    <rect x="3" y="4" width="18" height="16" rx="2" ry="2"/>
                </svg>
            </x-admin-sidebar-item>

            <x-admin-sidebar-item
                label="Admit Cards"
                :disabled="true"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="5" width="18" height="14" rx="2" ry="2"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 9h6M7 13h10"/>
                </svg>
            </x-admin-sidebar-item>

            <x-admin-sidebar-item
                route="admin.demand-drafts.index"
                active="admin.demand-drafts.*"
                label="Demand Drafts"
                :disabled="!$routeExists('admin.demand-drafts.index')"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="6" width="20" height="12" rx="2" ry="2"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 10h6M6 14h10"/>
                </svg>
            </x-admin-sidebar-item>

            <x-admin-sidebar-item
                route="admin.notifications.index"
                active="admin.notifications.*"
                label="Advertisement PDFs"
                :disabled="!$routeExists('admin.notifications.index')"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 3v5h5"/>
                </svg>
            </x-admin-sidebar-item>

            <x-admin-sidebar-item
                label="Downloads"
                :disabled="true"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l-4-4m4 4l4-4"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 21h14"/>
                </svg>
            </x-admin-sidebar-item>

            <x-admin-sidebar-item
                route="admin.reports.index"
                active="admin.reports.*"
                label="Reports & Analytics"
                :disabled="!$routeExists('admin.reports.index')"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 19V5m6 14V9m6 10v-6m4 6H2"/>
                </svg>
            </x-admin-sidebar-item>

            @can('manage_grievances')
                <x-admin-sidebar-item
                    route="admin.grievances.index"
                    active="admin.grievances.*"
                    label="Grievances"
                    :disabled="!$routeExists('admin.grievances.index')"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4h16v12H7l-3 3V4z"/>
                    </svg>
                </x-admin-sidebar-item>
            @endcan
        </nav>

        <div class="border-t border-white/10 px-3 py-2 text-[10px] text-white/60">
            Version 1.0
        </div>
    </div>
</aside>
