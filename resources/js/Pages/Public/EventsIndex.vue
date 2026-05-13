<script setup>
import Navbar from '@/Components/Navbar.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    events: { type: Array, default: () => [] }
});
</script>

<template>
    <Head title="Eventos Disponibles" />

    <div class="min-h-screen bg-[#050505] text-white relative overflow-hidden flex flex-col">
        <div class="absolute top-[-10%] left-[-5%] w-[60%] h-[60%] bg-blue-600/15 rounded-full blur-[120px] animate-pulse pointer-events-none"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[50%] h-[50%] bg-indigo-700/15 rounded-full blur-[150px] pointer-events-none"></div>
        <div class="absolute top-[20%] right-[10%] w-[30%] h-[30%] bg-purple-500/10 rounded-full blur-[100px] pointer-events-none"></div>

        <Navbar />

        <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 relative z-10 w-full">
            <header class="mb-20 text-center space-y-4">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10 backdrop-blur-md mb-4">
                    <span class="w-2 h-2 bg-blue-500 rounded-full animate-ping"></span>
                    <span class="text-xs font-bold uppercase tracking-[0.2em] text-blue-400">Eventos Universitarios</span>
                </div>
                <h1 class="text-5xl md:text-6xl font-bold text-white tracking-tight">
                    Próximos <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-400">Eventos</span>
                </h1>
                <p class="max-w-2xl mx-auto text-lg text-white/50 leading-relaxed">
                    Descubre los eventos académicos y culturales programados en nuestros auditorios.
                </p>
            </header>

            <div v-if="events.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                <div
                    v-for="event in events"
                    :key="event.id"
                    class="group relative backdrop-blur-2xl bg-white/[0.03] rounded-[2.5rem] border border-white/10 overflow-hidden hover:bg-white/[0.06] hover:border-white/20 transition-all duration-500 shadow-2xl flex flex-col"
                >
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-600/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>

                    <div class="p-8 relative z-10 flex-1">
                        <h3 class="text-3xl font-extrabold text-white group-hover:text-blue-400 transition-colors duration-300 leading-tight">
                            {{ event.name }}
                        </h3>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <span class="px-4 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-widest bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                {{ event.space_type }}
                            </span>
                            <span class="px-4 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-widest bg-green-500/10 text-green-400 border border-green-500/20">
                                ${{ Number(event.ticket_price).toLocaleString() }}
                            </span>
                        </div>

                        <div class="mt-5 flex items-center gap-2 text-sm text-white/50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            {{ event.space_name }}
                        </div>

                        <div class="mt-4 flex items-start gap-2 text-sm text-white/50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white/30 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <div>{{ new Date(event.start_time).toLocaleDateString('es-ES', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) }}</div>
                                <div class="text-white/30">{{ new Date(event.start_time).toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' }) }} - {{ new Date(event.end_time).toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' }) }}</div>
                            </div>
                        </div>

                    </div>

                    <div class="p-8 pt-0 relative z-10">
                        <Link
                            :href="route('events.public.show', event.slug)"
                            class="flex items-center justify-center gap-2 w-full bg-white text-black hover:bg-blue-50 font-bold py-4 rounded-2xl transition-all active:scale-[0.98] group/btn shadow-xl"
                        >
                            Comprar Boletos
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform group-hover/btn:translate-x-1 transition-transform" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </Link>
                    </div>
                </div>
            </div>

            <div v-else class="text-center py-32">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-6 text-white/10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="text-white/30 text-lg">No hay eventos programados actualmente.</p>
                <p class="text-white/20 text-sm mt-2">Vuelve pronto para descubrir nuevos eventos.</p>
            </div>

            <footer class="mt-20 text-center">
                <p class="text-white/20 text-sm">
                    &copy; 2026 Sistema de Gestión de Espacios Universitarios. <br>
                    Diseñado con estándares de excelencia académica.
                </p>
            </footer>
        </main>
    </div>
</template>

<style scoped>
@keyframes pulse {
    0%, 100% { opacity: 0.2; transform: scale(1); }
    50% { opacity: 0.4; transform: scale(1.05); }
}
.animate-pulse {
    animation: pulse 10s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
