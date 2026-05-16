<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    space: Object,
    upcomingEvents: { type: Array, default: () => [] },
    dbReservedSeats: { type: Array, default: () => [] },
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const isStaff = computed(() => user.value?.role === 'staff');
const flashError = computed(() => page.props.flash?.error);

const rows = ['A', 'B', 'C', 'D', 'E', 'F'];
const seatsPerRow = 10;

const pricePerSeat = computed(() => {
    return props.space.price_per_hour ? parseFloat(props.space.price_per_hour) : 15000;
});

const pad = (n) => String(n).padStart(2, '0');
const formatDateTime = (d) => {
    return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
};

const getDefaultStart = () => {
    const d = new Date();
    d.setHours(d.getHours() + 1, 0, 0, 0);
    return formatDateTime(d);
};
const getDefaultEnd = () => {
    const d = new Date();
    d.setHours(d.getHours() + 3, 0, 0, 0);
    return formatDateTime(d);
};

const staffStart = ref(getDefaultStart());
const staffEnd = ref(getDefaultEnd());

const staffHours = computed(() => {
    const s = new Date(staffStart.value);
    const e = new Date(staffEnd.value);
    const diff = (e - s) / (1000 * 60 * 60);
    return diff > 0 ? Math.round(diff * 10) / 10 : 0;
});

const canSubmit = computed(() => staffHours.value >= 1);

const staffTotal = computed(() => {
    return staffHours.value * pricePerSeat.value;
});

const submitStaffRental = () => {
    if (staffHours.value < 1) return;
    router.get(route('staff.checkout'), {
        space_id: props.space.id,
        start_time: staffStart.value,
        end_time: staffEnd.value,
    });
};
</script>

<template>
    <Head :title="space.name" />

    <div class="min-h-screen bg-[#050508] text-white py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden font-sans">
        <div class="absolute top-[-10%] right-[-10%] w-[50%] h-[50%] bg-indigo-600/10 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[50%] h-[50%] bg-blue-600/10 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="max-w-6xl mx-auto relative z-10">
            <Link 
                :href="route('spaces.public.index')" 
                class="inline-flex items-center gap-2 text-sm text-white/40 hover:text-white mb-8 transition-colors"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver al listado de auditorios
            </Link>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div :class="isStaff ? 'lg:col-span-2' : 'lg:col-span-3'" class="backdrop-blur-2xl bg-white/[0.03] border border-white/10 rounded-[2.5rem] p-8 shadow-2xl">
                    <div class="border-b border-white/5 pb-6 mb-8">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-500/10 text-blue-400 border border-blue-500/20 mb-3">
                            {{ space.type }}
                        </span>
                        <h1 class="text-3xl font-extrabold tracking-tight">{{ space.name }}</h1>
                        <p class="text-white/50 text-sm mt-2 leading-relaxed">{{ space.description }}</p>
                    </div>

                    <div v-if="!isStaff" class="mb-8">
                        <div v-if="upcomingEvents.length > 0" class="bg-blue-500/5 border border-blue-500/10 rounded-2xl p-5">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></span>
                                <span class="text-[10px] uppercase tracking-widest text-blue-400 font-bold">Próximos Eventos</span>
                            </div>
                            <div class="space-y-3">
                                <div v-for="event in upcomingEvents" :key="event.id" class="flex items-center justify-between bg-white/5 rounded-xl px-4 py-3 border border-white/5">
                                    <div>
                                        <div class="text-sm font-semibold text-white">{{ event.event_name }}</div>
                                        <div class="text-[10px] text-white/40 mt-0.5">Organizado por {{ event.organized_by }}</div>
                                    </div>
                                    <div class="text-right text-[10px] text-white/50">
                                        <div>{{ new Date(event.start_time).toLocaleDateString('es-ES', { day: 'numeric', month: 'short' }) }}</div>
                                        <div>{{ new Date(event.start_time).toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' }) }} - {{ new Date(event.end_time).toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' }) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="bg-white/5 border border-dashed border-white/10 rounded-2xl p-8 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-3 text-white/10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="text-white/30 text-sm">No hay eventos disponibles en este auditorio.</p>
                        </div>
                    </div>

                    <template v-if="isStaff">
                        <div class="flex flex-col items-center mb-12">
                            <div class="w-[80%] h-4 bg-gradient-to-b from-blue-500/40 to-transparent rounded-full blur-sm mb-2"></div>
                            <div class="w-[75%] py-2 bg-white/5 border border-white/10 rounded-xl text-center text-[10px] uppercase tracking-[0.3em] text-white/40 font-bold shadow-inner">
                                Escenario / Pantalla Principal
                            </div>
                        </div>

                        <div class="space-y-4 mb-8 overflow-x-auto pb-4">
                            <div v-for="row in rows" :key="row" class="flex items-center justify-center gap-3 min-w-[500px]">
                                <span class="w-6 text-sm font-bold text-white/30 text-right mr-2">{{ row }}</span>

                                <div class="flex gap-2">
                                    <div 
                                        v-for="col in seatsPerRow" 
                                        :key="row + col"
                                        class="w-8 h-8 rounded-lg border bg-white/5 border-white/10 text-white/50 text-xs font-bold flex items-center justify-center"
                                    >
                                        {{ col }}
                                    </div>
                                </div>

                                <span class="w-6 text-sm font-bold text-white/30 text-left ml-2">{{ row }}</span>
                            </div>
                        </div>

                    </template>
                </div>

                <template v-if="isStaff">
                    <div v-if="flashError" class="mb-4 backdrop-blur-2xl bg-rose-500/10 border border-rose-500/20 rounded-2xl p-4">
                        <p class="text-rose-300 text-xs">{{ flashError }}</p>
                    </div>
                    <div class="backdrop-blur-2xl bg-white/[0.03] border border-white/10 rounded-[2.5rem] p-8 shadow-2xl flex flex-col justify-between h-fit">
                        <div>
                            <h2 class="text-xl font-bold border-b border-white/5 pb-4 mb-6">Detalles de la Reserva</h2>

                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-[10px] font-bold uppercase tracking-widest mb-4">Organizador</div>

                            <div class="space-y-4">
                                <div>
                                    <label class="text-[10px] uppercase font-bold text-white/30 block mb-1.5 tracking-widest">Inicio del Evento</label>
                                    <input v-model="staffStart" type="datetime-local" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50" />
                                </div>
                                <div>
                                    <label class="text-[10px] uppercase font-bold text-white/30 block mb-1.5 tracking-widest">Fin del Evento</label>
                                    <input v-model="staffEnd" type="datetime-local" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50" />
                                </div>
                                <div class="bg-white/5 rounded-xl p-4 border border-white/5 space-y-2">
                                    <div class="flex justify-between text-xs"><span class="text-white/40">Duración</span><span class="font-bold text-white" :class="staffHours < 1 ? 'text-red-400' : ''">{{ staffHours < 1 ? 'Horario inválido' : staffHours + ' hora' + (staffHours !== 1 ? 's' : '') }}</span></div>
                                    <div class="flex justify-between text-xs"><span class="text-white/40">Tarifa por hora</span><span class="font-bold text-blue-400">${{ pricePerSeat.toLocaleString() }} COP</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-white/5 space-y-6">
                            <div class="flex justify-between items-end">
                                <span class="text-sm text-white/40">Total estimado:</span>
                                <span class="text-3xl font-extrabold tracking-tight text-white">${{ staffTotal.toLocaleString() }} <span class="text-xs text-white/40 font-normal">COP</span></span>
                            </div>
                            <button @click="submitStaffRental" :disabled="!canSubmit" class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-400 hover:to-indigo-500 text-white font-bold py-4 rounded-2xl shadow-lg shadow-blue-500/20 transform transition-all active:scale-[0.98] disabled:opacity-30 disabled:cursor-not-allowed disabled:transform-none">
                                <span class="flex items-center justify-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    Alquilar Auditorio Completo
                                </span>
                            </button>
                            <p class="text-[10px] text-white/20 text-center">El pago se calcula por hora. Gestionarás los asientos después.</p>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<style scoped>
button:active {
    transform: scale(0.95);
}
</style>