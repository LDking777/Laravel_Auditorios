<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

// Obtenemos los datos del usuario autenticado desde las propiedades globales de Inertia 
const page = usePage();
const user = computed(() => page.props.auth.user);

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <nav class="sticky top-0 z-50 w-full border-b border-white/10 bg-[#050508]/60 backdrop-blur-xl transition-all duration-300">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                
                <div class="flex items-center gap-8">
                    <Link :href="user && (user.role === 'staff' || user.role === 'admin') ? route('spaces.public.index') : route('events.public.index')" class="flex items-center gap-2 group">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 shadow-lg shadow-blue-500/20 group-hover:scale-105 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <span class="text-lg font-bold tracking-tight text-white group-hover:text-blue-400 transition-colors">
                            Audita<span class="text-blue-500">Book</span>
                        </span>
                    </Link>

                    <div class="hidden sm:flex items-center gap-1" v-if="user">
                        <template v-if="user.role !== 'staff' && user.role !== 'admin'">
                            <Link 
                                :href="route('events.public.index')" 
                                class="text-sm font-medium px-4 py-2 rounded-xl transition-all"
                                :class="$page.component === 'Public/EventsIndex' ? 'text-blue-400 bg-blue-500/10' : 'text-white/60 hover:text-white hover:bg-white/5'"
                            >
                                Eventos
                            </Link>
                            <Link 
                                :href="route('reservations.user.index')" 
                                class="text-sm font-medium px-4 py-2 rounded-xl transition-all"
                                :class="$page.component === 'Public/MyReservations' ? 'text-blue-400 bg-white/5' : 'text-white/60 hover:text-white hover:bg-white/5'"
                            >
                                Mis Boletos
                            </Link>
                        </template>
                    </div>
                </div>

                <div class="flex items-center gap-4" v-if="user">
                    
                    <div class="hidden md:flex flex-col items-end">
                        <span class="text-sm font-semibold text-white">{{ user.name }}</span>
                        
                        <span 
                            :class="[
                                user.role === 'admin' ? 'bg-red-500/10 text-red-400 border-red-500/20' : 
                                user.role === 'staff' ? 'bg-amber-500/10 text-amber-400 border-amber-500/20' : 
                                'bg-green-500/10 text-green-400 border-green-500/20'
                            ]"
                            class="mt-0.5 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-full border"
                        >
                            {{ user.role }}
                        </span>
                    </div>

                    <div class="hidden md:block h-8 w-[1px] bg-white/10"></div>

                    <div class="flex items-center gap-2">
                        <Link 
                            v-if="user.role !== 'staff' && user.role !== 'admin'"
                            :href="route('events.public.index')" 
                            class="sm:hidden flex h-9 w-9 items-center justify-center rounded-xl bg-white/5 border border-white/10 text-white/70 hover:text-white"
                            title="Eventos"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </Link>
                        <Link 
                            v-if="user.role !== 'staff'"
                            :href="route('reservations.user.index')" 
                            class="sm:hidden flex h-9 w-9 items-center justify-center rounded-xl bg-white/5 border border-white/10 text-white/70 hover:text-white"
                            title="Mis Boletos"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                        </Link>

                        <Link 
                            v-if="user.role === 'admin'"
                            :href="route('admin.spaces.index')"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 text-white px-4 py-2 text-xs font-bold transition-all active:scale-[0.98]"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Panel Admin
                        </Link>

                        <Link 
                            v-if="user.role === 'staff'"
                            :href="route('staff.rentals.index')"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-blue-500/10 border border-blue-500/20 hover:bg-blue-500/20 text-blue-400 px-4 py-2 text-xs font-bold transition-all active:scale-[0.98]"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Mis Alquileres
                        </Link>

                        <button 
                            @click="logout"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-red-500/10 border border-red-500/20 hover:bg-red-500/20 text-red-400 px-4 py-2 text-xs font-bold transition-all active:scale-[0.98]"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Salir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>