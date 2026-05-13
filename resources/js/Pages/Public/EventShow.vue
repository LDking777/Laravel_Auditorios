<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    event: Object,
    dbReservedSeats: Array,
});

const rows = ['A', 'B', 'C', 'D', 'E', 'F'];
const seatsPerRow = 10;

const reservedSeats = ref(props.dbReservedSeats || []);
const selectedSeats = ref([]);

const toggleSeat = (seatId) => {
    if (reservedSeats.value.includes(seatId)) return;
    if (selectedSeats.value.includes(seatId)) {
        selectedSeats.value = selectedSeats.value.filter(id => id !== seatId);
    } else {
        selectedSeats.value.push(seatId);
    }
};

const totalPrice = computed(() => {
    return selectedSeats.value.length * props.event.ticket_price;
});

const submitReservation = () => {
    if (selectedSeats.value.length === 0) return;
    router.get(route('reservations.checkout'), {
        space_id: props.event.space.id,
        seats: selectedSeats.value,
        event_id: props.event.id,
    });
};
</script>

<template>
    <Head :title="event.name" />

    <div class="min-h-screen bg-[#050508] text-white py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden font-sans">
        <div class="absolute top-[-10%] right-[-10%] w-[50%] h-[50%] bg-blue-600/10 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[50%] h-[50%] bg-indigo-600/10 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="max-w-6xl mx-auto relative z-10">
            <Link 
                :href="route('events.public.index')" 
                class="inline-flex items-center gap-2 text-sm text-white/40 hover:text-white mb-8 transition-colors"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver a eventos
            </Link>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 backdrop-blur-2xl bg-white/[0.03] border border-white/10 rounded-[2.5rem] p-8 shadow-2xl">
                    <div class="border-b border-white/5 pb-6 mb-8">
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-blue-500/10 text-blue-400 border border-blue-500/20 mb-3">
                            <span class="w-1.5 h-1.5 bg-blue-400 rounded-full animate-pulse"></span>
                            {{ event.space.name }}
                        </span>
                        <h1 class="text-3xl font-extrabold tracking-tight">{{ event.name }}</h1>
                        <p class="text-white/50 text-sm mt-2 leading-relaxed">{{ event.description }}</p>
                        <div class="flex items-center gap-4 mt-4 text-xs text-white/40">
                            <div class="flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                {{ new Date(event.start_time).toLocaleDateString('es-ES', { weekday: 'long', day: 'numeric', month: 'long' }) }}
                            </div>
                            <div class="flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                {{ new Date(event.start_time).toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' }) }} - {{ new Date(event.end_time).toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' }) }}
                            </div>
                        </div>
                    </div>

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
                                <button 
                                    v-for="col in seatsPerRow" 
                                    :key="row + col"
                                    @click="toggleSeat(row + col)"
                                    :disabled="reservedSeats.includes(row + col)"
                                    :class="[
                                        reservedSeats.includes(row + col)
                                            ? 'bg-red-500/20 border-red-500/30 text-red-400 cursor-not-allowed'
                                            : selectedSeats.includes(row + col)
                                                ? 'bg-blue-600 border-blue-400 text-white shadow-lg shadow-blue-500/50 scale-105'
                                                : 'bg-white/5 hover:bg-white/15 border-white/10 hover:border-white/30 text-white/70'
                                    ]"
                                    class="w-8 h-8 rounded-lg border text-xs font-bold transition-all duration-200 flex items-center justify-center relative group"
                                >
                                    {{ col }}
                                    <span class="absolute bottom-full mb-2 bg-slate-900 border border-white/10 text-[9px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-20 shadow-xl whitespace-nowrap">
                                        Asiento {{ row }}-{{ col }}
                                    </span>
                                </button>
                            </div>
                            <span class="w-6 text-sm font-bold text-white/30 text-left ml-2">{{ row }}</span>
                        </div>
                    </div>

                    <div class="flex justify-center gap-6 border-t border-white/5 pt-6 text-xs text-white/50">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded bg-white/5 border border-white/10"></div>
                            <span>Disponible</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded bg-blue-600 border border-blue-400"></div>
                            <span>Seleccionado</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded bg-red-500/20 border border-red-500/30"></div>
                            <span>Ocupado</span>
                        </div>
                    </div>
                </div>

                <div class="backdrop-blur-2xl bg-white/[0.03] border border-white/10 rounded-[2.5rem] p-8 shadow-2xl flex flex-col justify-between h-fit">
                    <div>
                        <h2 class="text-xl font-bold border-b border-white/5 pb-4 mb-6">Compra de Boletos</h2>

                        <div class="bg-white/5 rounded-2xl p-4 border border-white/5 space-y-3">
                            <div class="flex justify-between text-xs">
                                <span class="text-white/40">Auditorio:</span>
                                <span class="font-bold">{{ event.space.name }}</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-white/40">Precio por asiento:</span>
                                <span class="font-bold text-blue-400">${{ Number(event.ticket_price).toLocaleString() }} COP</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-white/40">Tipo:</span>
                                <span class="font-bold">{{ event.space.type }}</span>
                            </div>
                        </div>

                        <div class="space-y-2 mt-4">
                            <label class="text-xs font-semibold text-white/40 uppercase tracking-widest block">Asientos elegidos</label>
                            <div v-if="selectedSeats.length > 0" class="flex flex-wrap gap-2 max-h-[120px] overflow-y-auto">
                                <span v-for="seat in selectedSeats" :key="seat" class="text-xs font-bold bg-blue-600/20 border border-blue-500/30 text-blue-300 px-3 py-1.5 rounded-lg">{{ seat }}</span>
                            </div>
                            <p v-else class="text-sm text-white/30 italic">No has seleccionado ningún asiento todavía.</p>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-white/5 space-y-6">
                        <div class="flex justify-between items-end">
                            <span class="text-sm text-white/40">Total estimado:</span>
                            <span class="text-3xl font-extrabold tracking-tight text-white">${{ totalPrice.toLocaleString() }} <span class="text-xs text-white/40 font-normal">COP</span></span>
                        </div>
                        <button @click="submitReservation" :disabled="selectedSeats.length === 0" class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-400 hover:to-indigo-500 text-white font-bold py-4 rounded-2xl shadow-lg shadow-blue-500/20 transform transition-all active:scale-[0.98] disabled:opacity-30 disabled:cursor-not-allowed disabled:transform-none">
                            Proceder al Pago
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
button:active {
    transform: scale(0.95);
}
</style>
