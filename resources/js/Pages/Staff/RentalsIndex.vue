<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    rentals: { type: Array, required: true }
});

const page = usePage();
const flash = computed(() => page.props.flash);
</script>

<template>
    <Head title="Mis Alquileres" />

    <div class="min-h-screen bg-[#050508] text-white py-8 px-4 md:px-12 relative overflow-hidden font-sans">
        <div class="absolute top-[-10%] right-[-10%] w-[50%] h-[50%] bg-blue-600/10 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[50%] h-[50%] bg-indigo-600/10 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto relative z-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-10 gap-6">
                <div>
                    <h1 class="text-4xl font-bold tracking-tight text-white">Mis <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-400">Alquileres</span></h1>
                    <p class="text-white/40 text-sm uppercase tracking-[0.2em] mt-2 font-medium">Gestión de auditorios alquilados</p>
                </div>
            </div>

            <div v-if="flash?.message" class="mb-6 px-6 py-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-sm font-medium">{{ flash.message }}</div>
            <div v-if="flash?.error" class="mb-6 px-6 py-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm font-medium">{{ flash.error }}</div>

            <div class="flex flex-wrap gap-2 mb-8">
                <Link :href="route('spaces.public.index')" class="px-5 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-white/70 hover:text-white transition-all text-sm font-medium">
                    <span class="flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Volver al Dashboard
                    </span>
                </Link>
                <div class="w-px h-7 bg-white/10 self-center"></div>
                <Link :href="route('staff.rentals.index')" class="px-5 py-2.5 rounded-xl bg-blue-600/20 border border-blue-500/30 text-blue-300 font-medium text-sm">Mis Alquileres</Link>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div v-for="r in rentals" :key="r.id" class="backdrop-blur-2xl bg-white/[0.03] border border-white/10 rounded-[2rem] p-6 hover:bg-white/[0.06] transition-all group">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="font-bold text-white group-hover:text-blue-400 transition-colors">{{ r.event_name }}</h3>
                            <p class="text-xs text-white/40 mt-0.5">{{ r.space_name }}</p>
                        </div>
                        <span :class="r.status === 'confirmada' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-blue-500/10 text-blue-400 border-blue-500/20'" class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest border">
                            {{ r.status }}
                        </span>
                    </div>
                    <div class="space-y-1.5 text-xs text-white/50 mb-5">
                        <div class="flex justify-between"><span>Inicio</span><span class="text-white/70 font-medium">{{ new Date(r.start_time).toLocaleString('es-ES') }}</span></div>
                        <div class="flex justify-between"><span>Fin</span><span class="text-white/70 font-medium">{{ new Date(r.end_time).toLocaleString('es-ES') }}</span></div>
                        <div class="flex justify-between"><span>Asientos asignados</span><span class="text-blue-400 font-bold">{{ r.assigned }}</span></div>
                    </div>
                    <div class="flex gap-2">
                        <Link v-if="r.has_event" :href="route('staff.events.manage', r.event_id)" class="flex items-center justify-center gap-2 flex-1 py-3 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/20 text-emerald-400 text-xs font-bold transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            Gestionar Evento
                        </Link>
                    </div>
                </div>

                <div v-if="rentals.length === 0" class="col-span-full backdrop-blur-2xl bg-white/[0.03] border border-white/10 rounded-[2.5rem] p-12 text-center">
                    <div class="w-16 h-16 rounded-3xl bg-white/5 flex items-center justify-center border border-white/5 mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white/20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <p class="text-white/30 font-medium">No tienes alquileres activos.</p>
                    <Link :href="route('spaces.public.index')" class="inline-block mt-4 px-6 py-3 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-bold hover:bg-blue-500/20 transition-all">Ver Auditorios Disponibles</Link>
                </div>
            </div>
        </div>
    </div>
</template>
