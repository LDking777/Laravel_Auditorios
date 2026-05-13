<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    reservation: { type: Object, required: true },
    assignedSeats: { type: Array, default: () => [] },
    eventName: { type: String, default: 'Evento' },
    capacity: { type: Number, default: 100 },
    users: { type: Array, default: () => [] },
});

const page = usePage();
const flash = computed(() => page.props.flash);

const rows = ['A', 'B', 'C', 'D', 'E', 'F'];
const seatsPerRow = 10;

const assignedMap = computed(() => {
    const map = {};
    props.assignedSeats.forEach(s => { map[s.seat_id] = s; });
    return map;
});

const isAssigned = (seatId) => !!assignedMap.value[seatId];

const getGuest = (seatId) => assignedMap.value[seatId] || null;

const assigningSeat = ref(null);
const form = ref({ guest_name: '', guest_email: '' });
const selectedUserId = ref('');

const startAssign = (seatId) => {
    if (isAssigned(seatId)) return;
    assigningSeat.value = seatId;
    form.value = { guest_name: '', guest_email: '' };
    selectedUserId.value = '';
};

const selectUser = (userId) => {
    if (!userId) { form.value = { guest_name: '', guest_email: '' }; return; }
    const u = props.users.find(x => x.id == userId);
    if (u) { form.value.guest_name = u.name; form.value.guest_email = u.email; }
};

const confirmAssign = () => {
    if (!assigningSeat.value || !form.value.guest_name || !form.value.guest_email) return;
    router.post(route('staff.rentals.assign-seat', props.reservation.id), {
        seat_id: assigningSeat.value,
        guest_name: form.value.guest_name,
        guest_email: form.value.guest_email,
    }, { preserveScroll: true, onSuccess: () => { assigningSeat.value = null; } });
};

const removeGuest = (seatId) => {
    if (confirm('¿Liberar este asiento?')) {
        router.delete(route('staff.rentals.remove-seat', props.reservation.id), {
            data: { seat_id: seatId },
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head :title="'Gestionar ' + eventName" />

    <div class="min-h-screen bg-[#050508] text-white py-8 px-4 md:px-12 relative overflow-hidden font-sans">
        <div class="absolute top-[-10%] right-[-10%] w-[50%] h-[50%] bg-blue-600/10 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[50%] h-[50%] bg-indigo-600/10 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto relative z-10">
            <div class="flex flex-wrap items-center gap-3 mb-8">
                <Link :href="route('staff.rentals.index')" class="inline-flex items-center gap-1.5 text-sm text-white/40 hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Mis Alquileres
                </Link>
                <span class="text-white/20">/</span>
                <span class="text-sm text-white/60">{{ eventName }}</span>
            </div>

            <div v-if="flash?.message" class="mb-6 px-6 py-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-sm font-medium">{{ flash.message }}</div>
            <div v-if="flash?.error" class="mb-6 px-6 py-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm font-medium">{{ flash.error }}</div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <div class="backdrop-blur-2xl bg-white/[0.03] border border-white/10 rounded-[2.5rem] p-8 shadow-2xl">
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h1 class="text-2xl font-bold text-white">{{ eventName }}</h1>
                                <p class="text-xs text-white/40 mt-1">{{ reservation.space_name }} &middot; {{ new Date(reservation.start_time).toLocaleString('es-ES') }} &ndash; {{ new Date(reservation.end_time).toLocaleString('es-ES') }}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-bold text-blue-400">{{ Object.keys(assignedMap).length }} / {{ capacity }}</div>
                                <div class="text-[10px] text-white/30 uppercase tracking-widest">Asientos</div>
                            </div>
                        </div>

                        <div class="flex flex-col items-center mb-6">
                            <div class="w-[80%] h-3 bg-gradient-to-b from-blue-500/30 to-transparent rounded-full blur-sm mb-2"></div>
                            <div class="w-[75%] py-2 bg-white/5 border border-white/10 rounded-xl text-center text-[10px] uppercase tracking-[0.3em] text-white/40 font-bold">Escenario</div>
                        </div>

                        <div class="space-y-3 overflow-x-auto pb-4">
                            <div v-for="row in rows" :key="row" class="flex items-center justify-center gap-3 min-w-[500px]">
                                <span class="w-6 text-sm font-bold text-white/30 text-right mr-2">{{ row }}</span>
                                <div class="flex gap-2">
                                    <button
                                        v-for="col in seatsPerRow"
                                        :key="row + col"
                                        @click="isAssigned(row + col) ? removeGuest(row + col) : startAssign(row + col)"
                                        :class="isAssigned(row + col) ? 'bg-emerald-500/20 border-emerald-500/30 text-emerald-400 cursor-pointer' : 'bg-white/5 hover:bg-white/15 border-white/10 hover:border-blue-400/50 text-white/70'"
                                        class="w-8 h-8 rounded-lg border text-xs font-bold transition-all duration-200 relative group"
                                    >
                                        {{ col }}
                                        <span class="absolute bottom-full mb-2 bg-slate-900 border border-white/10 text-[9px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-20 shadow-xl whitespace-nowrap">
                                            <template v-if="isAssigned(row + col)">{{ getGuest(row + col).guest_name }}</template>
                                            <template v-else>Asiento {{ row }}-{{ col }}</template>
                                        </span>
                                    </button>
                                </div>
                                <span class="w-6 text-sm font-bold text-white/30 text-left ml-2">{{ row }}</span>
                            </div>
                        </div>

                        <div class="flex justify-center gap-6 border-t border-white/5 pt-6 text-xs text-white/50">
                            <div class="flex items-center gap-2"><div class="w-4 h-4 rounded bg-white/5 border border-white/10"></div><span>Disponible</span></div>
                            <div class="flex items-center gap-2"><div class="w-4 h-4 rounded bg-emerald-500/20 border border-emerald-500/30"></div><span>Asignado</span></div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div v-if="assigningSeat" class="backdrop-blur-2xl bg-white/[0.03] border border-white/10 rounded-[2rem] p-6 shadow-2xl">
                        <h3 class="text-sm font-bold text-white mb-1">Asignar <span class="text-blue-400">{{ assigningSeat }}</span></h3>
                        <p class="text-[10px] text-white/30 uppercase tracking-widest mb-5">Selecciona un invitado</p>

                        <div class="space-y-4">
                            <div>
                                <label class="text-[10px] uppercase font-bold text-white/30 block mb-1.5 tracking-widest">Desde lista de usuarios</label>
                                <select @change="selectUser(($event.target).value)" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 appearance-none">
                                    <option value="" class="bg-[#0a0a0c]">Elegir usuario existente...</option>
                                    <option v-for="u in users" :key="u.id" :value="u.id" class="bg-[#0a0a0c]">{{ u.name }} ({{ u.email }})</option>
                                </select>
                            </div>

                            <div class="relative">
                                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-white/5"></div></div>
                                <div class="relative flex justify-center text-[10px]"><span class="bg-[#050508] px-3 text-white/20 uppercase tracking-widest">o manual</span></div>
                            </div>

                            <input v-model="form.guest_name" type="text" placeholder="Nombre del invitado" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-blue-500/50" />
                            <input v-model="form.guest_email" type="email" placeholder="Email del invitado" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-blue-500/50" />

                            <div class="flex gap-2">
                                <button @click="assigningSeat = null" class="flex-1 py-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-white/50 text-xs font-bold transition-all">Cancelar</button>
                                <button @click="confirmAssign" :disabled="!form.guest_name || !form.guest_email" class="flex-1 py-3 rounded-xl bg-blue-500 hover:bg-blue-400 text-black text-xs font-bold transition-all disabled:opacity-40">Asignar</button>
                            </div>
                        </div>
                    </div>

                    <div class="backdrop-blur-2xl bg-white/[0.03] border border-white/10 rounded-[2rem] p-6 shadow-2xl">
                        <h3 class="text-sm font-bold text-white mb-4">Invitados Asignados</h3>
                        <div v-if="assignedSeats.length === 0" class="text-center py-6">
                            <p class="text-white/20 text-xs">Aún no has asignado ningún asiento.</p>
                        </div>
                        <div v-else class="space-y-2 max-h-80 overflow-y-auto">
                            <div v-for="s in assignedSeats" :key="s.seat_id" class="flex items-center justify-between p-3 rounded-xl bg-white/5 border border-white/5 group">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-lg bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-xs font-bold text-blue-400">{{ s.seat_id }}</span>
                                    <div>
                                        <div class="text-xs font-semibold text-white/80">{{ s.guest_name }}</div>
                                        <div class="text-[10px] text-white/30">{{ s.guest_email }}</div>
                                    </div>
                                </div>
                                <button @click="removeGuest(s.seat_id)" class="opacity-0 group-hover:opacity-100 p-1.5 rounded-lg bg-rose-500/10 text-rose-400 hover:bg-rose-500/20 transition-all" title="Liberar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
