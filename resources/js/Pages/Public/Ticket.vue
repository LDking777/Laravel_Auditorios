<template>
    <Head title="Tu Boleto Digital" />

    <div class="min-h-screen bg-[#050508] text-white py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden font-sans flex items-center justify-center">
        <div class="absolute top-[-10%] right-[-10%] w-[50%] h-[50%] bg-indigo-600/10 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[50%] h-[50%] bg-blue-600/10 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="max-w-md w-full relative z-10">
            <Link 
                :href="route('events.public.index')" 
                class="inline-flex items-center gap-2 text-xs text-white/40 hover:text-white mb-6 transition-colors"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver a eventos
            </Link>

            <div class="bg-white/[0.02] border border-white/10 rounded-[2.5rem] overflow-hidden shadow-2xl backdrop-blur-xl">
                
                <div class="p-8 text-center border-b border-dashed border-white/10">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 mb-4 tracking-widest uppercase">
                        {{ reservation.status || 'Confirmado' }}
                    </span>
                    <p v-if="eventName" class="text-sm font-bold text-blue-400 mb-1">{{ eventName }}</p>
                    <h1 class="text-2xl font-black tracking-tight">{{ reservation.space?.name || 'Acceso al Auditorio' }}</h1>
                    <p class="text-white/40 text-xs mt-1">{{ reservation.space?.type || 'Espacio Reservado' }}</p>
                </div>

                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-white/30 block tracking-wider">Fecha</span>
                            <span class="text-sm font-semibold mt-1 block">{{ formatDate(reservation.start_time) }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-white/30 block tracking-wider">Hora de Entrada</span>
                            <span class="text-sm font-semibold mt-1 block">{{ formatTime(reservation.start_time) }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-white/30 block tracking-wider">Titular</span>
                            <span class="text-sm font-semibold mt-1 block truncate">{{ reservation.user_name }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-white/30 block tracking-wider">Asientos</span>
                            <span class="text-sm font-bold mt-1 block text-blue-400 font-mono">
                                {{ getSeatsList(reservation.notes) }}
                            </span>
                        </div>
                    </div>

                    <div class="relative py-4">
                        <div class="absolute left-[-41px] top-1/2 -translate-y-1/2 w-6 h-6 bg-[#050508] border-r border-white/10 rounded-full"></div>
                        <div class="absolute right-[-41px] top-1/2 -translate-y-1/2 w-6 h-6 bg-[#050508] border-l border-white/10 rounded-full"></div>
                        <div class="w-full border-t border-dashed border-white/10"></div>
                    </div>

                    <div class="flex flex-col items-center justify-center pt-2">
                        <div class="bg-white p-4 rounded-2xl shadow-xl mb-4">
                            <svg class="w-28 h-28 text-slate-950" viewBox="0 0 100 100" fill="currentColor">
                                <rect x="10" y="10" width="15" height="15"/>
                                <rect x="35" y="10" width="30" height="5"/>
                                <rect x="75" y="10" width="15" height="15"/>
                                <rect x="10" y="35" width="5" height="30"/>
                                <rect x="25" y="35" width="15" height="15"/>
                                <rect x="50" y="35" width="15" height="25"/>
                                <rect x="75" y="35" width="15" height="15"/>
                                <rect x="10" y="75" width="15" height="15"/>
                                <rect x="35" y="75" width="25" height="15"/>
                                <rect x="75" y="75" width="15" height="15"/>
                            </svg>
                        </div>
                        <span class="text-[10px] uppercase font-bold text-white/30 tracking-widest block">Código de Reserva</span>
                        <span class="font-mono text-sm font-semibold text-white/70 mt-1">#ADB-{{ reservation.id }}098</span>
                    </div>
                </div>

                <div class="px-8 pb-8">
                    <button 
                        @click="window.print()" 
                        class="w-full bg-white/5 hover:bg-white/10 text-white border border-white/10 py-3 rounded-xl text-xs font-semibold tracking-wide transition-all uppercase active:scale-95"
                    >
                        Imprimir / Guardar PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';

// Recibimos la reserva individual que el controlador está intentando renderizar
defineProps({
    reservation: {
        type: Object,
        required: true
    },
    eventName: {
        type: String,
        default: null,
    }
});

// Helper para extraer los asientos del campo 'notes' de la BD
const getSeatsList = (notes) => {
    try {
        if (!notes) return 'N/A';
        const parsed = typeof notes === 'string' ? JSON.parse(notes) : notes;
        if (parsed.asientos_reservados && Array.isArray(parsed.asientos_reservados)) {
            return parsed.asientos_reservados.join(', ');
        }
        return 'N/A';
    } catch (e) {
        return 'N/A';
    }
};

// Formateadores de fecha y hora estéticos
const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' });
};

const formatTime = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' }) + ' HS';
};

// Permite llamar a window.print() directamente en el template
const windowObj = typeof window !== 'undefined' ? window : null;
</script>