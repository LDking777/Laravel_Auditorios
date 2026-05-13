<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    event: Object,
    tickets: Array,
    occupiedSeats: Array,
    totalRevenue: Number,
    ticketsSold: Number,
});

const rows = ['A', 'B', 'C', 'D', 'E', 'F'];
const seatsPerRow = 10;
const assignedSeats = ref(props.occupiedSeats);
const guests = ref(props.tickets);

const guestName = ref('');
const guestEmail = ref('');
const selectedSeat = ref('');

const seatColor = (seatId) => {
    if (assignedSeats.value.includes(seatId)) return 'bg-blue-500/20 border-blue-500/30 text-blue-400';
    return 'bg-white/5 border-white/10 text-white/50';
};

const assignSeat = () => {
    if (!guestName.value || !selectedSeat.value) return;
    router.post(route('staff.events.assign-seat', props.event.id), {
        seat_id: selectedSeat.value,
        guest_name: guestName.value,
        guest_email: guestEmail.value,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            guestName.value = '';
            guestEmail.value = '';
            selectedSeat.value = '';
        },
    });
};

const removeSeat = (reservationId) => {
    if (!confirm('¿Liberar este asiento?')) return;
    router.delete(route('staff.events.remove-seat', props.event.id), {
        data: { reservation_id: reservationId },
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="'Gestionar: ' + event.name" />

    <div class="min-h-screen bg-[#050508] text-white py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden font-sans">
        <div class="absolute top-[-10%] right-[-10%] w-[50%] h-[50%] bg-blue-600/10 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[50%] h-[50%] bg-indigo-600/10 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto relative z-10">
            <Link :href="route('staff.rentals.index')" class="inline-flex items-center gap-2 text-sm text-white/40 hover:text-white mb-8 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Volver a Mis Alquileres
            </Link>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Stats sidebar -->
                <div class="lg:col-span-1 space-y-4">
                    <div class="backdrop-blur-2xl bg-white/[0.03] border border-white/10 rounded-[2.5rem] p-6 shadow-2xl">
                        <h3 class="text-lg font-bold border-b border-white/5 pb-3 mb-4">{{ event.name }}</h3>
                        <div class="space-y-3 text-xs">
                            <div class="flex justify-between"><span class="text-white/40">Entradas vendidas</span><span class="font-bold text-blue-400">{{ ticketsSold }}</span></div>
                            <div class="flex justify-between"><span class="text-white/40">Ingresos totales</span><span class="font-bold text-green-400">${{ Number(totalRevenue).toLocaleString() }} COP</span></div>
                            <div class="flex justify-between"><span class="text-white/40">Precio por asiento</span><span class="font-bold">${{ Number(event.ticket_price).toLocaleString() }} COP</span></div>
                            <div class="flex justify-between"><span class="text-white/40">Auditorio</span><span class="font-bold">{{ event.space.name }}</span></div>
                            <div class="h-px bg-white/5"></div>
                            <div><span class="text-white/40">Inicio</span><p class="font-bold mt-0.5">{{ new Date(event.start_time).toLocaleString('es-ES') }}</p></div>
                            <div><span class="text-white/40">Fin</span><p class="font-bold mt-0.5">{{ new Date(event.end_time).toLocaleString('es-ES') }}</p></div>
                        </div>
                    </div>

                    <!-- Guest assignment form -->
                    <div class="backdrop-blur-2xl bg-white/[0.03] border border-white/10 rounded-[2.5rem] p-6 shadow-2xl">
                        <h4 class="text-xs font-bold uppercase tracking-widest text-white/50 mb-4">Asignar Asiento Manual</h4>
                        <div class="space-y-3">
                            <input v-model="guestName" type="text" placeholder="Nombre del invitado" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-blue-500/50 text-white placeholder-white/10" />
                            <input v-model="guestEmail" type="email" placeholder="Email (opcional)" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-blue-500/50 text-white placeholder-white/10" />
                            <input v-model="selectedSeat" type="text" placeholder="Ej: A5" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-blue-500/50 text-white placeholder-white/10 uppercase" />
                            <button @click="assignSeat" :disabled="!guestName || !selectedSeat" class="w-full bg-blue-500 hover:bg-blue-400 text-black font-bold py-3 rounded-xl text-xs transition-all disabled:opacity-30">Asignar Asiento</button>
                        </div>
                    </div>
                </div>

                <!-- Seat map -->
                <div class="lg:col-span-2 backdrop-blur-2xl bg-white/[0.03] border border-white/10 rounded-[2.5rem] p-8 shadow-2xl">
                    <h3 class="text-lg font-bold border-b border-white/5 pb-4 mb-6">Mapa de Asientos</h3>

                    <div class="flex flex-col items-center mb-8">
                        <div class="w-[80%] h-4 bg-gradient-to-b from-blue-500/40 to-transparent rounded-full blur-sm mb-2"></div>
                        <div class="w-[75%] py-2 bg-white/5 border border-white/10 rounded-xl text-center text-[10px] uppercase tracking-[0.3em] text-white/40 font-bold shadow-inner">Escenario</div>
                    </div>

                    <div class="space-y-3 mb-6 overflow-x-auto pb-4">
                        <div v-for="row in rows" :key="row" class="flex items-center justify-center gap-3 min-w-[500px]">
                            <span class="w-6 text-sm font-bold text-white/30 text-right mr-2">{{ row }}</span>
                            <div class="flex gap-2">
                                <div v-for="col in seatsPerRow" :key="row + col"
                                    :class="seatColor(row + col)"
                                    class="w-8 h-8 rounded-lg border text-[10px] font-bold flex items-center justify-center"
                                >{{ col }}</div>
                            </div>
                            <span class="w-6 text-sm font-bold text-white/30 text-left ml-2">{{ row }}</span>
                        </div>
                    </div>

                    <div class="flex justify-center gap-6 border-t border-white/5 pt-4 text-xs text-white/50">
                        <div class="flex items-center gap-2"><div class="w-4 h-4 rounded bg-white/5 border border-white/10"></div><span>Disponible</span></div>
                        <div class="flex items-center gap-2"><div class="w-4 h-4 rounded bg-blue-500/20 border border-blue-500/30"></div><span>Ocupado</span></div>
                    </div>
                </div>

                <!-- Guest list -->
                <div class="lg:col-span-1 backdrop-blur-2xl bg-white/[0.03] border border-white/10 rounded-[2.5rem] p-6 shadow-2xl">
                    <h4 class="text-xs font-bold uppercase tracking-widest text-white/50 mb-4">Invitados ({{ guests.length }})</h4>
                    <div class="space-y-2 max-h-[500px] overflow-y-auto pr-1">
                        <div v-for="g in guests" :key="g.id" class="bg-white/5 rounded-xl p-3 border border-white/5">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-xs font-semibold text-white">{{ g.user_name }}</p>
                                    <p class="text-[10px] text-white/30">{{ g.user_email }}</p>
                                </div>
                                <button @click="removeSeat(g.id)" class="text-[10px] text-red-400 hover:text-red-300">Liberar</button>
                            </div>
                            <div class="flex flex-wrap gap-1 mt-2">
                                <span v-for="seat in g.seats" :key="seat" class="text-[9px] font-bold bg-blue-500/20 text-blue-400 border border-blue-500/20 px-2 py-0.5 rounded">{{ seat }}</span>
                            </div>
                        </div>
                        <p v-if="guests.length === 0" class="text-sm text-white/30 italic text-center py-8">No hay invitados registrados.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
