<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    reservations: {
        type: Array,
        default: () => [
            { 
                id: 1, 
                user_name: 'Juan Pérez', 
                status: 'Confirmada', 
                start_time: '2026-05-10T09:00:00', 
                space: { name: 'Gran Auditorio Central', type: 'Auditorio' },
                notes: '{"asientos_reservados": ["A-1", "A-2"]}'
            },
            { 
                id: 2, 
                user_name: 'Juan Pérez', 
                status: 'Pendiente', 
                start_time: '2026-05-15T14:00:00', 
                space: { name: 'Sala de Conferencias A', type: 'Sala' },
                notes: '{"asientos_reservados": ["C-5"]}'
            }
        ]
    }
});

const getSeatsList = (notes) => {
    try {
        if (!notes) return 'N/A';
        const parsed = typeof notes === 'string' ? JSON.parse(notes) : notes;
        if (parsed.asientos_reservados && Array.isArray(parsed.asientos_reservados)) {
            return parsed.asientos_reservados.join(', ');
        }
        return 'N/A';
    } catch (e) { return 'N/A'; }
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' });
};

const formatTime = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
};
</script>

<template>
    <Head title="Mis Boletos" />

    <div class="min-h-screen bg-[#050505] text-white py-12 px-4 sm:px-6 lg:px-8 relative font-sans">
        <!-- Background Accents -->
        <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-b from-blue-600/5 to-transparent pointer-events-none"></div>

        <div class="max-w-5xl mx-auto relative z-10">
            <!-- Header -->
            <div class="flex justify-between items-end mb-12">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Mis <span class="text-blue-400">Boletos</span></h1>
                    <p class="text-white/30 text-xs uppercase tracking-widest mt-1">Pases de acceso digital</p>
                </div>
                <Link :href="route('events.public.index')" class="text-xs font-bold text-white/40 hover:text-white transition-colors border-b border-white/10 pb-1">
                    Ver más eventos
                </Link>
            </div>

            <!-- Grid de Tickets Compactos -->
            <div v-if="reservations.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div 
                    v-for="reservation in reservations" 
                    :key="reservation.id" 
                    class="relative group"
                >
                    <!-- Ticket Card -->
                    <div class="backdrop-blur-xl bg-white/[0.03] border border-white/10 rounded-3xl overflow-hidden hover:bg-white/[0.06] transition-all duration-300 shadow-xl">
                        
                        <!-- Top Section (Info) -->
                        <div class="p-6 pb-4">
                            <div class="flex justify-between items-start mb-4">
                                <span 
                                    :class="reservation.status === 'Confirmada' ? 'text-emerald-400 bg-emerald-400/10' : 'text-blue-400 bg-blue-400/10'"
                                    class="text-[9px] font-bold uppercase tracking-widest px-2 py-1 rounded-md border border-white/5"
                                >
                                    {{ reservation.status }}
                                </span>
                                <span class="text-[10px] font-mono text-white/20">#{{ reservation.id }}098</span>
                            </div>
                            
                            <p v-if="reservation.event_name" class="text-xs font-semibold text-blue-400 mb-1">{{ reservation.event_name }}</p>
                            <h3 class="text-lg font-bold text-white line-clamp-1 group-hover:text-blue-400 transition-colors">
                                {{ reservation.space?.name }}
                            </h3>
                            <p class="text-[10px] text-white/30 uppercase tracking-wider mt-1">{{ reservation.space?.type }}</p>
                        </div>

                        <!-- Divider with notches -->
                        <div class="relative flex items-center px-4">
                            <div class="absolute left-[-10px] w-5 h-5 bg-[#050505] border border-white/10 rounded-full"></div>
                            <div class="w-full border-t border-dashed border-white/10"></div>
                            <div class="absolute right-[-10px] w-5 h-5 bg-[#050505] border border-white/10 rounded-full"></div>
                        </div>

                        <!-- Bottom Section (Details) -->
                        <div class="p-6 pt-4 bg-white/[0.01]">
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div>
                                    <p class="text-[9px] uppercase font-bold text-white/20 tracking-widest">Fecha</p>
                                    <p class="text-sm font-bold">{{ formatDate(reservation.start_time) }}</p>
                                </div>
                                <div>
                                    <p class="text-[9px] uppercase font-bold text-white/20 tracking-widest">Hora</p>
                                    <p class="text-sm font-bold">{{ formatTime(reservation.start_time) }}</p>
                                </div>
                            </div>

                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-[9px] uppercase font-bold text-white/20 tracking-widest">Asientos</p>
                                    <p class="text-xs font-mono font-bold text-blue-400">{{ getSeatsList(reservation.notes) }}</p>
                                </div>
                                <!-- Mini QR Placeholder -->
                                <div class="w-10 h-10 bg-white rounded-lg p-1 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-full h-full text-black" viewBox="0 0 100 100" fill="currentColor">
                                        <path d="M10 10h30v30H10zM15 15h20v20H15zM60 10h30v30H60zM65 15h20v20H65zM10 60h30v30H10zM15 65h20v20H15zM45 10h10v10H45zM45 25h10v10H45zM10 45h10v10H10zM25 45h10v10H25zM45 45h10v10H45zM60 45h10v10H60zM75 45h10v10H75zM45 60h10v10H45zM45 75h10v10H45zM60 60h10v10H60zM75 60h10v10H75zM60 75h10v10H60zM75 75h10v10H75z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-20 bg-white/[0.02] border border-white/10 rounded-[2rem]">
                <p class="text-white/20 text-sm">No tienes reservas activas.</p>
                <Link :href="route('spaces.public.index')" class="inline-block mt-4 text-blue-400 text-xs font-bold uppercase tracking-widest hover:text-blue-300 transition-colors">
                    Explorar Auditorios
                </Link>
            </div>
        </div>
    </div>
</template>
