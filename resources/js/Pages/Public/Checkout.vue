<script setup>
import { ref, computed } from 'vue';
import { router, Head } from '@inertiajs/vue3';

const props = defineProps({ 
    space: {
        type: Object,
        default: () => ({ id: 1, name: 'Gran Auditorio Central', price_per_hour: 15000 })
    }, 
    seats: {
        type: Array,
        default: () => ['A-1', 'A-2']
    },
    event: {
        type: Object,
        default: null,
    },
    ticketPrice: {
        type: Number,
        default: 15000,
    }
});

const loading = ref(false);

const calculatedTotal = computed(() => {
    return props.seats.length * props.ticketPrice;
});

const confirmPayment = () => {
    loading.value = true;
    
    setTimeout(() => {
        router.post(route('reservations.store'), {
            space_id: props.space.id,
            seats: props.seats,
            event_id: props.event?.id ?? null,
        }, {
            onFinish: () => {
                loading.value = false;
            }
        });
    }, 2000);
};
</script>

<template>
    <Head title="Finalizar Pago" />

    <div class="min-h-screen bg-[#050505] text-white flex flex-col items-center justify-center p-6 relative overflow-hidden font-sans">
        <!-- Background Accents -->
        <div class="absolute top-[-10%] right-[-10%] w-[60%] h-[60%] bg-blue-600/10 rounded-full blur-[120px] pointer-events-none animate-pulse"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[60%] h-[60%] bg-indigo-600/10 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="w-full max-w-md relative z-10">
            <!-- Main Card -->
            <div class="backdrop-blur-3xl bg-white/[0.03] border border-white/10 rounded-[2.5rem] p-8 md:p-10 shadow-2xl overflow-visible">
                
                <!-- Header -->
                <div class="text-center mb-10">
                    <h2 class="text-3xl font-bold tracking-tight text-white">Finalizar <span class="text-blue-400">Pago</span></h2>
                    <p v-if="event" class="text-sm text-blue-400 font-semibold mt-2">{{ event.name }}</p>
                    <p class="text-xs text-white/40 uppercase tracking-[0.2em] mt-1 font-medium">{{ space.name }}</p>
                    
                    <div class="mt-6 flex flex-wrap gap-2 justify-center">
                        <span 
                            v-for="seat in seats" 
                            :key="seat" 
                            class="bg-white/5 border border-white/10 text-white/60 text-[10px] px-3 py-1.5 rounded-lg font-bold uppercase tracking-widest"
                        >
                            {{ seat }}
                        </span>
                    </div>
                </div>
                
                <!-- Credit Card Visual (Apple Style) -->
                <div class="relative w-full h-52 bg-gradient-to-br from-gray-800 to-black rounded-[2rem] p-8 mb-10 shadow-2xl overflow-hidden group border border-white/10">
                    <!-- Card Chip -->
                    <div class="w-12 h-9 bg-gradient-to-br from-yellow-200 to-yellow-500 rounded-md mb-6 opacity-80"></div>
                    
                    <div class="flex justify-between items-end">
                        <div>
                            <div class="text-xl font-mono tracking-[0.25em] text-white/90 mb-4">**** **** **** 1234</div>
                            <div class="flex gap-8">
                                <div>
                                    <p class="text-[8px] uppercase font-bold text-white/30 tracking-widest mb-1">Titular</p>
                                    <p class="text-[10px] font-bold text-white/70 uppercase tracking-wider">{{ $page.props.auth?.user?.name || 'Usuario Invitado' }}</p>
                                </div>
                                <div>
                                    <p class="text-[8px] uppercase font-bold text-white/30 tracking-widest mb-1">Expira</p>
                                    <p class="text-[10px] font-bold text-white/70 tracking-wider">12/28</p>
                                </div>
                            </div>
                        </div>
                        <!-- Card Logo -->
                        <div class="flex flex-col items-end">
                            <div class="flex -space-x-3">
                                <div class="w-8 h-8 rounded-full bg-rose-500/80 backdrop-blur-sm"></div>
                                <div class="w-8 h-8 rounded-full bg-blue-500/80 backdrop-blur-sm"></div>
                            </div>
                            <span class="text-[8px] font-bold text-white/20 mt-2 tracking-widest uppercase italic">Premium Access</span>
                        </div>
                    </div>
                    
                    <!-- Card Glow -->
                    <div class="absolute -top-20 -right-20 w-40 h-40 bg-blue-500/10 rounded-full blur-3xl group-hover:bg-blue-500/20 transition-all duration-700"></div>
                </div>

                <!-- Payment Form -->
                <form @submit.prevent="confirmPayment" class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-white/30 ml-2 block tracking-widest">Número de Tarjeta</label>
                        <div class="relative">
                            <input 
                                type="text" 
                                required
                                placeholder="0000 0000 0000 0000" 
                                class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 outline-none focus:ring-2 focus:ring-blue-500/50 transition-all text-white placeholder-white/10" 
                            />
                            <div class="absolute right-5 top-1/2 -translate-y-1/2 text-white/20">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-white/30 ml-2 block tracking-widest">Expiración</label>
                            <input 
                                type="text" 
                                required
                                placeholder="MM/YY" 
                                class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 outline-none focus:ring-2 focus:ring-blue-500/50 transition-all text-white placeholder-white/10" 
                            />
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-white/30 ml-2 block tracking-widest">CVV</label>
                            <input 
                                type="password" 
                                required
                                placeholder="***" 
                                class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 outline-none focus:ring-2 focus:ring-blue-500/50 transition-all text-white placeholder-white/10" 
                            />
                        </div>
                    </div>

                    <!-- Total Summary -->
                    <div class="pt-8 border-t border-white/5 flex justify-between items-end">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-white/30 tracking-widest block mb-1">Total a Pagar</span>
                            <span class="text-3xl font-black text-white tracking-tighter">
                                ${{ calculatedTotal.toLocaleString() }}
                            </span>
                        </div>
                        <span class="text-xs text-blue-400 font-bold uppercase tracking-widest mb-1">COP</span>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        :disabled="loading"
                        class="w-full bg-white text-black hover:bg-blue-50 disabled:opacity-50 py-5 rounded-[1.5rem] font-bold mt-4 transition-all active:scale-95 flex items-center justify-center gap-3 shadow-2xl shadow-white/5"
                    >
                        <span v-if="loading" class="w-5 h-5 border-2 border-black/30 border-t-black rounded-full animate-spin"></span>
                        <svg v-if="!loading" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        {{ loading ? 'Procesando Pago Seguro...' : 'Confirmar y Pagar' }}
                    </button>
                </form>

                <!-- Footer Info -->
                <div class="mt-8 flex items-center justify-center gap-2 text-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span class="text-[10px] font-bold uppercase tracking-widest">Pago Encriptado SSL</span>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes pulse {
    0%, 100% { opacity: 0.1; transform: scale(1); }
    50% { opacity: 0.2; transform: scale(1.05); }
}
.animate-pulse {
    animation: pulse 10s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
