<script setup>
import { ref, computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    space: { type: Object, required: true },
    start_time: { type: String, required: true },
    end_time: { type: String, required: true },
    hours: { type: Number, required: true },
    total: { type: Number, required: true },
});

const page = usePage();
const loading = ref(false);
const flashError = computed(() => page.props.flash?.error);

const form = ref({
    event_name: '',
    event_description: '',
    event_banner: '',
    ticket_price: 15000,
});

const confirmPayment = () => {
    loading.value = true;

    router.post(route('staff.rentals.store'), {
        space_id: props.space.id,
        start_time: props.start_time,
        end_time: props.end_time,
        event_name: form.value.event_name,
        event_description: form.value.event_description,
        event_banner: form.value.event_banner,
        ticket_price: form.value.ticket_price,
    }, {
        preserveScroll: true,
        onError: () => { loading.value = false; },
        onFinish: () => { loading.value = false; },
    });
};
</script>

<template>
    <Head title="Alquiler de Auditorio" />

    <div class="min-h-screen bg-[#050505] text-white flex flex-col items-center justify-center p-6 relative overflow-hidden font-sans">
        <div class="absolute top-[-10%] right-[-10%] w-[60%] h-[60%] bg-indigo-600/10 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[60%] h-[60%] bg-blue-600/10 rounded-full blur-[120px] pointer-events-none"></div>

        <div v-if="flashError" class="w-full max-w-lg mb-4 relative z-10">
            <div class="backdrop-blur-2xl bg-rose-500/10 border border-rose-500/20 rounded-2xl p-4">
                <p class="text-rose-300 text-xs">{{ flashError }}</p>
            </div>
        </div>
        <div v-if="page.props.errors && Object.keys(page.props.errors).length" class="w-full max-w-lg mb-4 relative z-10">
            <div class="backdrop-blur-2xl bg-rose-500/10 border border-rose-500/20 rounded-2xl p-4">
                <p v-for="(msg, key) in page.props.errors" :key="key" class="text-rose-300 text-xs">{{ msg }}</p>
            </div>
        </div>

        <div class="w-full max-w-lg relative z-10">
            <div class="backdrop-blur-3xl bg-white/[0.03] border border-white/10 rounded-[2.5rem] p-8 md:p-10 shadow-2xl">
                <div class="text-center mb-8">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-[10px] font-bold uppercase tracking-widest mb-4">Organizador</div>
                    <h2 class="text-3xl font-bold tracking-tight text-white">Nuevo <span class="text-blue-400">Evento</span></h2>
                    <p class="text-xs text-white/40 uppercase tracking-[0.2em] mt-2 font-medium">{{ space.name }}</p>
                </div>

                <div class="bg-white/5 rounded-2xl p-6 border border-white/5 mb-8 space-y-4">
                    <div class="flex justify-between text-sm"><span class="text-white/40">Inicio</span><span class="font-bold text-white">{{ new Date(start_time).toLocaleString('es-ES') }}</span></div>
                    <div class="flex justify-between text-sm"><span class="text-white/40">Fin</span><span class="font-bold text-white">{{ new Date(end_time).toLocaleString('es-ES') }}</span></div>
                    <div class="h-px bg-white/5"></div>
                    <div class="flex justify-between text-sm"><span class="text-white/40">Duración</span><span class="font-bold text-white">{{ hours }} hora{{ hours !== 1 ? 's' : '' }}</span></div>
                    <div class="flex justify-between text-sm"><span class="text-white/40">Tarifa por hora</span><span class="font-bold text-blue-400">${{ Number(space.price_per_hour).toLocaleString() }} COP</span></div>
                    <div class="h-px bg-white/5"></div>
                    <div class="flex justify-between text-sm"><span class="text-white/40">Total Alquiler</span><span class="font-bold text-white text-lg">${{ Number(total).toLocaleString() }} COP</span></div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="text-[10px] uppercase font-bold text-white/30 block mb-2 tracking-widest">Nombre del Evento <span class="text-red-400">*</span></label>
                        <input v-model="form.event_name" type="text" placeholder="Ej: Conferencia Anual Tech" class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 outline-none focus:ring-2 focus:ring-blue-500/50 transition-all text-white placeholder-white/10" />
                    </div>

                    <div>
                        <label class="text-[10px] uppercase font-bold text-white/30 block mb-2 tracking-widest">Descripción</label>
                        <textarea v-model="form.event_description" placeholder="Describe tu evento..." rows="3" class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 outline-none focus:ring-2 focus:ring-blue-500/50 transition-all text-white placeholder-white/10 resize-none"></textarea>
                    </div>

                    <div>
                        <label class="text-[10px] uppercase font-bold text-white/30 block mb-2 tracking-widest">URL del Banner (opcional)</label>
                        <input v-model="form.event_banner" type="text" placeholder="https://..." class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 outline-none focus:ring-2 focus:ring-blue-500/50 transition-all text-white placeholder-white/10" />
                    </div>

                    <div>
                        <label class="text-[10px] uppercase font-bold text-white/30 block mb-2 tracking-widest">Precio por Asiento (COP) <span class="text-red-400">*</span></label>
                        <input v-model.number="form.ticket_price" type="number" min="0" class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 outline-none focus:ring-2 focus:ring-blue-500/50 transition-all text-white" />
                    </div>
                </div>

                <button @click="confirmPayment" :disabled="loading || !form.event_name" class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-400 hover:to-indigo-500 text-white disabled:opacity-50 py-5 rounded-[1.5rem] font-bold mt-8 transition-all active:scale-95 flex items-center justify-center gap-3 shadow-2xl shadow-blue-500/20">
                    <span v-if="loading" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ loading ? 'Procesando...' : 'Pagar y Publicar Evento' }}
                </button>

                <div class="mt-6 flex items-center justify-center gap-2 text-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span class="text-[10px] font-bold uppercase tracking-widest">Pago Corporativo Seguro</span>
                </div>
            </div>
        </div>
    </div>
</template>
