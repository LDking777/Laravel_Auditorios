<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    reservations: { type: Array, required: true }
});

const page = usePage();
const flash = computed(() => page.props.flash);

const statusColors = {
    confirmada: 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
    pendiente: 'bg-blue-500/10 text-blue-400 border-blue-500/20',
    cancelada: 'bg-rose-500/10 text-rose-400 border-rose-500/20',
    rechazada: 'bg-rose-500/10 text-rose-400 border-rose-500/20',
};

const deleteReservation = (reservation) => {
    if (confirm('¿Estás seguro de que deseas liberar esta reserva? Los asientos serán liberados automáticamente.')) {
        router.delete(route('admin.reservations.destroy', reservation.id), { preserveScroll: true });
    }
};
</script>

<template>
    <Head title="Monitoreo de Reservas" />

    <div class="min-h-screen bg-[#050508] text-white py-8 px-4 md:px-12 relative overflow-hidden font-sans">
        <div class="absolute top-[-10%] right-[-10%] w-[50%] h-[50%] bg-indigo-600/10 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[50%] h-[50%] bg-blue-600/10 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto relative z-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-10 gap-6">
                <div>
                    <h1 class="text-4xl font-bold tracking-tight text-white">
                        Monitoreo de <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-400">Reservas</span>
                    </h1>
                    <p class="text-white/40 text-sm uppercase tracking-[0.2em] mt-2 font-medium">Asientos comprados por cliente</p>
                </div>
                <div class="flex items-center gap-3 text-xs text-white/40">
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-400/60"></span>{{ props.reservations.length }} reservas</span>
                </div>
            </div>

            <div v-if="flash?.message" class="mb-6 px-6 py-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-sm font-medium">
                {{ flash.message }}
            </div>
            <div v-if="flash?.error" class="mb-6 px-6 py-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm font-medium">
                {{ flash.error }}
            </div>

            <div class="flex flex-wrap gap-2 mb-8">
                <Link :href="route('spaces.public.index')" class="px-5 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-white/70 hover:text-white transition-all text-sm font-medium">
                    <span class="flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Volver al Dashboard
                    </span>
                </Link>
                <div class="w-px h-7 bg-white/10 self-center"></div>
                <template v-if="$page.props.auth.user?.role === 'admin'">
                    <Link v-if="$page.component !== 'Admin/Spaces/Index'" :href="route('admin.spaces.index')" class="px-5 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-white/70 hover:text-white transition-all text-sm font-medium">Espacios</Link>
                    <Link v-if="$page.component !== 'Admin/Users/Index'" :href="route('admin.users.index')" class="px-5 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-white/70 hover:text-white transition-all text-sm font-medium">Usuarios</Link>
                </template>
                <Link :href="route('admin.reservations.index')" class="px-5 py-2.5 rounded-xl bg-blue-600/20 border border-blue-500/30 text-blue-300 font-medium text-sm">Reservas y Asientos</Link>
            </div>

            <div class="backdrop-blur-2xl bg-white/[0.03] border border-white/10 rounded-[2.5rem] overflow-hidden shadow-2xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white/[0.02] border-b border-white/5">
                                <th class="py-6 px-8 text-[10px] uppercase tracking-[0.2em] text-white/30 font-bold">#</th>
                                <th class="py-6 px-8 text-[10px] uppercase tracking-[0.2em] text-white/30 font-bold">Auditorio</th>
                                <th class="py-6 px-8 text-[10px] uppercase tracking-[0.2em] text-white/30 font-bold">Cliente</th>
                                <th class="py-6 px-8 text-[10px] uppercase tracking-[0.2em] text-white/30 font-bold">Asientos</th>
                                <th class="py-6 px-8 text-[10px] uppercase tracking-[0.2em] text-white/30 font-bold">Estado</th>
                                <th class="py-6 px-8 text-[10px] uppercase tracking-[0.2em] text-white/30 font-bold">Fecha</th>
                                <th class="py-6 px-8 text-[10px] uppercase tracking-[0.2em] text-white/30 font-bold text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <tr v-for="r in props.reservations" :key="r.id" class="group hover:bg-white/[0.02] transition-all duration-300">
                                <td class="py-6 px-8"><span class="text-sm font-medium text-white/70">#{{ r.id }}</span></td>
                                <td class="py-6 px-8">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500/10 to-purple-500/10 border border-white/5 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white/50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                        </div>
                                        <div class="font-bold text-white group-hover:text-blue-400 transition-colors">{{ r.space_name }}</div>
                                    </div>
                                </td>
                                <td class="py-6 px-8">
                                    <div class="text-sm">
                                        <div class="font-semibold text-white/90">{{ r.user_name }}</div>
                                        <div class="text-white/40 text-xs flex items-center gap-1 mt-0.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                            {{ r.user_email }}
                                        </div>
                                    </div>
                                </td>
                                <td class="py-6 px-8">
                                    <div class="flex flex-wrap gap-1.5 max-w-xs">
                                        <span
                                            v-for="(seat, idx) in r.seats"
                                            :key="idx"
                                            class="px-2.5 py-1 rounded-lg text-[11px] font-bold font-mono bg-blue-500/10 border border-blue-500/20 text-blue-300"
                                        >
                                            {{ seat }}
                                        </span>
                                        <span v-if="r.seats.length === 0" class="text-white/30 text-xs italic">Sin asientos</span>
                                    </div>
                                </td>
                                <td class="py-6 px-8">
                                    <span :class="statusColors[r.status] || 'bg-white/5 text-white/50 border-white/10'" class="px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-widest border backdrop-blur-md">
                                        {{ r.status }}
                                    </span>
                                </td>
                                <td class="py-6 px-8">
                                    <div class="text-sm text-white/50">{{ new Date(r.created_at).toLocaleDateString('es-ES') }}</div>
                                    <div class="text-[10px] text-white/25">{{ new Date(r.created_at).toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' }) }}</div>
                                </td>
                                <td class="py-6 px-8 text-right">
                                    <button
                                        @click="deleteReservation(r)"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold transition-all border bg-rose-500/10 hover:bg-rose-500/20 border-rose-500/10 text-rose-400 hover:text-rose-300 active:scale-[0.97]"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Liberar
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="props.reservations.length === 0">
                                <td colspan="7" class="py-20 text-center">
                                    <div class="flex flex-col items-center gap-4">
                                        <div class="w-16 h-16 rounded-3xl bg-white/5 flex items-center justify-center border border-white/5">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white/20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5h6M9 9h6m-6 4h6m-6 4h6M5 8h14M5 16h14" />
                                            </svg>
                                        </div>
                                        <p class="text-white/30 font-medium">No hay reservas registradas aún.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
