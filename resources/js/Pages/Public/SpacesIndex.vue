<script setup> 
import Navbar from '@/Components/Navbar.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    spaces: {
        type: Array,
        default: () => [
            { id: 1, name: 'Gran Auditorio Central', type: 'Auditorio', description: 'Espacio principal para conferencias magistrales y eventos académicos de gran escala.', capacity: 500, price_per_hour: 150, slug: 'gran-auditorio' },
            { id: 2, name: 'Sala de Conferencias A', type: 'Sala', description: 'Ideal para reuniones de departamento y presentaciones interactivas.', capacity: 50, price_per_hour: 45, slug: 'sala-a' },
            { id: 3, name: 'Teatro Universitario', type: 'Teatro', description: 'Equipado con tecnología acústica de punta para eventos culturales.', capacity: 300, price_per_hour: 120, slug: 'teatro-u' },
        ]
    },
    filters: Object,
    upcomingEventsMap: { type: Object, default: () => ({}) }
});
</script>

<template>
    <Head title="Auditorios Disponibles" />

    <div class="min-h-screen bg-[#050505] text-white relative overflow-hidden flex flex-col">
        <div class="absolute top-[-10%] left-[-5%] w-[60%] h-[60%] bg-blue-600/20 rounded-full blur-[120px] animate-pulse pointer-events-none"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[50%] h-[50%] bg-indigo-700/20 rounded-full blur-[150px] pointer-events-none"></div>
        <div class="absolute top-[20%] right-[10%] w-[30%] h-[30%] bg-purple-500/10 rounded-full blur-[100px] pointer-events-none"></div>

        <Navbar />

        <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 relative z-10 w-full">
            
            <header class="mb-20 text-center space-y-4">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10 backdrop-blur-md mb-4">
                    <span class="w-2 h-2 bg-blue-500 rounded-full animate-ping"></span>
                    <span class="text-xs font-bold uppercase tracking-[0.2em] text-blue-400">Espacios Disponibles</span>
                </div>
                <h1 class="text-5xl md:text-6xl font-bold text-white tracking-tight">
                    Explora nuestros <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-400">Auditorios</span>
                </h1>
                <p class="max-w-2xl mx-auto text-lg text-white/50 leading-relaxed">
                    Selecciona uno de nuestros modernos espacios diseñados para potenciar tus conferencias y eventos académicos con tecnología de vanguardia.
                </p>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                <div 
                    v-for="space in spaces" 
                    :key="space.id" 
                    class="group relative backdrop-blur-2xl bg-white/[0.03] rounded-[2.5rem] border border-white/10 overflow-hidden hover:bg-white/[0.06] hover:border-white/20 transition-all duration-500 shadow-2xl flex flex-col"
                >
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-600/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>

                    <div class="p-8 relative z-10 flex-1">
                        <div class="flex justify-between items-start mb-6">
                            <span class="px-4 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-widest bg-white/5 border border-white/10 text-white/70 group-hover:text-white group-hover:border-white/30 transition-all">
                                {{ space.type }}
                            </span>
                            <div class="w-10 h-10 rounded-2xl bg-white/5 flex items-center justify-center border border-white/5 group-hover:scale-110 transition-transform duration-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                        </div>
                        
                        <h3 class="text-2xl font-bold text-white group-hover:text-blue-400 transition-colors duration-300">
                            {{ space.name }}
                        </h3>
                        
                        <p class="mt-4 text-white/40 text-sm leading-relaxed line-clamp-3 group-hover:text-white/60 transition-colors">
                            {{ space.description }}
                        </p>

                        <div v-if="upcomingEventsMap[space.id] && upcomingEventsMap[space.id].length > 0" class="mt-4">
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest bg-blue-500/10 text-blue-400 border border-blue-500/20 px-3 py-1.5 rounded-full">
                                <span class="w-1.5 h-1.5 bg-blue-400 rounded-full animate-pulse"></span>
                                {{ upcomingEventsMap[space.id][0].event_name }}
                            </span>
                            <span class="ml-2 text-[10px] text-white/30">
                                {{ new Date(upcomingEventsMap[space.id][0].start_time).toLocaleDateString('es-ES', { day: 'numeric', month: 'short' }) }}
                            </span>
                        </div>

                        <div class="mt-8 pt-6 border-t border-white/5 flex items-center justify-between">
                            <div class="flex flex-col">
                                <span class="text-[10px] uppercase tracking-widest text-white/30 font-bold">Capacidad</span>
                                <span class="text-lg font-semibold text-white/90">{{ space.capacity }} <span class="text-xs font-normal text-white/40">pax</span></span>
                            </div>
                            <div v-if="space.price_per_hour" class="flex flex-col items-end">
                                <span class="text-[10px] uppercase tracking-widest text-white/30 font-bold">Tarifa</span>
                                <span class="text-2xl font-bold text-blue-400">
                                    ${{ parseFloat(space.price_per_hour || 0).toLocaleString() }}<span class="text-xs text-white/30 font-normal ml-1">/hr</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="p-8 pt-0 relative z-10">
                        <Link 
                            :href="route('spaces.public.show', space.slug)" 
                            class="flex items-center justify-center gap-2 w-full bg-white text-black hover:bg-blue-50 font-bold py-4 rounded-2xl transition-all active:scale-[0.98] group/btn shadow-xl"
                        >
                            Ver Disponibilidad
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform group-hover/btn:translate-x-1 transition-transform" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </Link>
                    </div>
                </div>
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