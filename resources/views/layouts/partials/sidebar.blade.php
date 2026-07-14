{{-- ═══════════════════════════════════════════════════════
     SIDEBAR — Navigation latérale COPTAN
     Responsive : overlay mobile / fixe desktop
═══════════════════════════════════════════════════════ --}}

<aside id="sidebar"
       class="fixed top-0 left-0 h-full z-40 flex flex-col
              transition-all duration-300 ease-in-out
              w-64 -translate-x-full
              lg:translate-x-0 lg:static lg:z-auto"
       style="background-color: #1A3A6B;">

    {{-- ── LOGO & NOM ÉCOLE ─────────────────────────────────── --}}
    <div class="flex items-center gap-3 px-4 py-4
                border-b border-white/10">
        <img src="{{ asset('images/logo.jpg') }}"
             alt="COPTAN"
             class="w-10 h-10 flex-shrink-0">
        {{-- <img src="{{ asset('images/logo.jpg') }}"
             alt="COPTAN"
             class="w-10 h-10 object-contain rounded-full
                    ring-2 ring-white/30 flex-shrink-0"> --}}
        <div class="overflow-hidden">
            <p class="text-white font-bold text-sm leading-tight truncate">
                COPTAN
            </p>
            <p class="text-white/60 text-xs truncate">
                Gestion Scolaire
            </p>
        </div>
        {{-- Bouton fermer sidebar (mobile) --}}
        <button onclick="toggleSidebar()"
                class="ml-auto text-white/60 hover:text-white lg:hidden">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- ── NAVIGATION SCROLLABLE ────────────────────────────────── --}}
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1
                scrollbar-thin scrollbar-thumb-white/20">

        {{-- Dashboard --}}
        <x-sidebar-item
            icon="home"
            label="Tableau de bord"
            :href="auth()->user()->getDashboardRoute()"
            :active="request()->routeIs('*.dashboard')" />

        {{-- ── ACADÉMIQUE ────────────────────────────────────────── --}}
        @canany(['view-classes', 'manage-classes'])
        <p class="px-3 pt-4 pb-1 text-white/40 text-xs font-semibold
                  uppercase tracking-wider">
            Académique
        </p>

        @can('manage-academic-years')
        <x-sidebar-item
            icon="calendar"
            label="Années scolaires"
            href="{{ route('academic-years.index') }}"
            :active="request()->routeIs('academic-years.*')" />
        @endcan

        <x-sidebar-item
            icon="building"
            label="Sections & Classes"
            href="{{ route('classes.index') }}"
            {{-- :active="request()->routeIs('class-groups.*')" /> --}}
            :active="request()->routeIs('classes.*')" />

        @can('manage-subjects')
        <x-sidebar-item
            icon="book"
            label="Matières"
            href="{{ route('subjects.index') }}"
            :active="request()->routeIs('subjects.*')" />
        @endcan

        <x-sidebar-item
            icon="clock"
            label="Emploi du temps"
            href="{{ route('timetable.index') }}"
            :active="request()->routeIs('timetable.index')" />

        <x-sidebar-item 
            icon="calendar" 
            label="Mon emploi du temps"
            href="{{ route('timetable.teacher') }}"
            :active="request()->routeIs('timetable.teacher')" />
        @endcanany

        {{-- ── ÉLÈVES ────────────────────────────────────────────── --}}
        @can('view-students')
        <p class="px-3 pt-4 pb-1 text-white/40 text-xs font-semibold
                  uppercase tracking-wider">
            Élèves
        </p>

        <x-sidebar-item
            icon="users"
            {{-- icon="academic-cap" --}}
            label="Élèves"
            href="{{ route('students.index') }}"
            :active="request()->routeIs('students.*') && !request()->routeIs('students.documents.*') 
                  && !request()->routeIs('students.create')
                  && !request()->routeIs('students.enroll')" />

        @can('manage-enrollments')
        <x-sidebar-item
            icon="user-plus"
            label="Inscriptions"
            href="{{ route ('students.create') }}"
            :active="request()->routeIs('students.create', 'students.enroll')" />
        @endcan
        @endcan

        {{-- ── DOCUMENTS (hors finances) ─────────────────────────── --}}
        @can('view-students')
        <p class="px-3 pt-4 pb-1 text-white/40 text-xs font-semibold
                  uppercase tracking-wider">
            Documents
        </p>

        <x-sidebar-item
            icon="document"
            label="Impressions élèves"
            href="{{ route('students.documents.index') }}"
            :active="request()->routeIs('students.documents.*')" />
        @endcan

        {{-- ── PERSONNEL ─────────────────────────────────────────── --}}
        @can('view-staff')
        <p class="px-3 pt-4 pb-1 text-white/40 text-xs font-semibold
                  uppercase tracking-wider">
            Personnel
        </p>

        <x-sidebar-item
            icon="briefcase"
            {{-- icon="users" --}}
            {{-- label="Personnel" --}}
            label="Enseignants & Staff"
            href="{{ route('staff.index') }}"
            :active="request()->routeIs('staff.*') && !request()->routeIs('staff.salaries') && !request()->routeIs('staff.salary.edit')" />
        
        <x-sidebar-item
            icon="bank"
            label="Salaires"
            href="{{ route('staff.salaries') }}"
            :active="request()->routeIs('staff.salaries', 'staff.salary.edit')" />
            
        

        @can('manage-academic-years')
        <p class="px-3 pt-4 pb-1 text-white/40 text-xs font-semibold
                  uppercase tracking-wider">
            évaluations
        </p>

        <x-sidebar-item
            icon="eye"
            label="Vue Globale"
            href="{{ route('grades.index')}}"
            :active="request()->routeIs('grades.index')" />
        @endcan

        @canany(['view-grades', 'enter-grades'])
        <x-sidebar-item
            icon="search"
            label="Consultation Notes"
            href="{{ route('grades.notes')}}"
            :active="request()->routeIs('grades.notes')" />
        @endcanany

        @can('enter-grades')
        <x-sidebar-item
            icon="pencil"
            label="Saisie des notes"
            href="{{ route('grades.entry.form')}}"
            :active="request()->routeIs('grades.entry*')" />
        @endcan

        {{-- @can('validate-grades')
        <x-sidebar-item
            icon="check-circle"
            label="Validation des notes"
            href="#"
            :active="request()->routeIs('grades.validate*')" />
        @endcan --}}

        @can('view-bulletins')
        <x-sidebar-item
            icon="document"
            label="Bulletins"
            href="{{ route('bulletins.index') }}"
            :active="request()->routeIs('bulletins.*')" />
        @endcan
        @endcanany

        {{-- ── PRÉSENCES ─────────────────────────────────────────── --}}
        @can('view-absences')
        <p class="px-3 pt-4 pb-1 text-white/40 text-xs font-semibold
                  uppercase tracking-wider">
            Présences
        </p>

        @can('manage-absences')
        <x-sidebar-item
            icon="clipboard"
            label="Appel du jour"
            href="#"
            :active="request()->routeIs('attendance.*')" />
        @endcan

        <x-sidebar-item
            icon="x-circle"
            label="Absences"
            href="{{ route('absences.index') }}"
            :active="request()->routeIs('absences.*')" />
        @endcan

        {{-- ── FINANCES ──────────────────────────────────────────── --}}
        @can('view-finances')
            <p class="px-3 pt-4 pb-1 text-white/40 text-xs font-semibold
                    uppercase tracking-wider">
                Finances
            </p>

            @if(auth()->user()->hasAnyRole(['directeur', 'super-admin']))
            <x-sidebar-item
                icon="currency-dollar"
                label="Gestion Globale"
                href="{{ route('finances.global') }}"
                :active="request()->routeIs('finances.global')" />
            @endif
                
            <x-sidebar-item
                icon="currency-dollar"
                label="Finances"
                href="{{ route('finances.index') }}"
                :active="request()->routeIs('finances.index', 'finances.class-students')" />
                
            <x-sidebar-item
                icon="cash"
                label="Paiements"
                href="{{ route('finances.payments') }}"
                :active="request()->routeIs('finances.payments', 'finances.student')" />

            @can('configure-fees')
            <x-sidebar-item
                icon="cog"
                label="Config. des frais"
                href="{{ route('finances.fees-list') }}"
                :active="request()->routeIs('finances.fees-list', 'finances.fees')" />
            @endcan

            <x-sidebar-item
                icon="chart-bar"
                label="Rapports financiers"
                href="{{ route('finances.reports') }}"
                :active="request()->routeIs('finances.reports*')" />
        @endcan

        {{-- ── DISCIPLINE ────────────────────────────────────────── --}}
        @can('view-discipline')
        <p class="px-3 pt-4 pb-1 text-white/40 text-xs font-semibold
                  uppercase tracking-wider">
            Discipline
        </p>

        <x-sidebar-item
            icon="shield"
            label="Incidents"
            href="{{ route('discipline.index') }}"
            :active="request()->routeIs('discipline.*')" />
        @endcan

        {{-- ── COMMUNICATION ─────────────────────────────────────── --}}
        <p class="px-3 pt-4 pb-1 text-white/40 text-xs font-semibold
                  uppercase tracking-wider">
            Communication
        </p>

        <x-sidebar-item
            {{-- icon="bell" --}}
            {{-- icon="phone" --}}
            icon="speakerphone"
            label="Annonces"
            href="{{ route('communication.announcements.index') }}"
            {{-- :active="request()->routeIs('announcements.*')" /> --}}
            :active="request()->routeIs('communication.announcements.*')" />

        <x-sidebar-item
            icon="mail"
            label="Messagerie"
            href="{{ route('communication.messages.index') }}"
            {{-- :active="request()->routeIs('messages.*')" /> --}}
            :active="request()->routeIs('communication.messages.*')" />

        @can('manage-parent-communication')
        <x-sidebar-item
            icon="chat"
            label="Messages Parents"
            href="{{ route('communication.parents.index') }}"
            :active="request()->routeIs('communication.parents.*')" />
        @endcan

        {{-- ── ADMINISTRATION ────────────────────────────────────── --}}
        @canany(['manage-settings', 'manage-users'])
        <p class="px-3 pt-4 pb-1 text-white/40 text-xs font-semibold
                  uppercase tracking-wider">
            Administration
        </p>

        @can('manage-users')
        <x-sidebar-item
            icon="user-group"
            label="Comptes utilisateurs"
            href="{{ route('users.index') }}"
            :active="request()->routeIs('users.*')" />
        @endcan

        @can('manage-settings')
        <x-sidebar-item
            icon="adjustments"
            label="Paramètres"
            href="{{ route('settings.index') }}"
            :active="request()->routeIs('settings.*')" />
        @endcan
        @endcanany

    </nav>

    {{-- ── INFOS UTILISATEUR (bas de sidebar) ─────────────────── --}}
    @php $sideUser = auth()->user(); @endphp
    <div class="border-t border-white/10 px-3 py-3">
        <div class="flex items-center gap-3">
            @if($sideUser->photo || $sideUser->staff?->photo)
                <img src="{{ $sideUser->photo_url }}"
                     alt="{{ $sideUser->name }}"
                     class="w-9 h-9 rounded-full object-cover flex-shrink-0 border border-white/20">
            @else
                <div class="w-9 h-9 rounded-full flex items-center justify-center
                            flex-shrink-0 font-bold text-sm"
                     style="background-color: #C8A415; color: #1A3A6B;">
                    {{ strtoupper(substr($sideUser->name, 0, 2)) }}
                </div>
            @endif
            <div class="flex-1 min-w-0">
                <p class="text-white text-sm font-medium truncate">
                    {{ $sideUser->name }}
                </p>
                <p class="text-white/50 text-xs truncate">
                    {{ ucfirst(auth()->user()->getRoleNames()->first() ?? '') }}
                </p>
            </div>
            {{-- Déconnexion --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        title="Déconnexion"
                        class="text-white/50 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7
                                 a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

</aside>
