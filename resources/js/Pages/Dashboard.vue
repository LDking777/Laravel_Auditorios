<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';

// Obtenemos los datos del usuario logueado desde las propiedades compartidas de Inertia
const user = usePage().props.auth.user;
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Panel de Control
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4">Hola, {{ user.name }}</h3>
                    
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500">Tu rol actual es:</span>
                        <span 
                            :class="[
                                user.role === 'admin' 
                                    ? 'bg-red-100 text-red-800 border-red-200' 
                                    : 'bg-green-100 text-green-800 border-green-200'
                            ]"
                            class="px-3 py-1 rounded-full text-xs font-semibold border"
                        >
                            {{ user.role.toUpperCase() }}
                        </span>
                    </div>

                    <div v-if="user.role === 'admin'" class="mt-6 p-4 bg-blue-50 rounded-xl border border-blue-100">
                        <p class="text-sm text-blue-800 mb-3">Tienes permisos de Administrador para gestionar los auditorios.</p>
                        <a 
                            :href="route('admin.spaces.index')" 
                            class="inline-block bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2 rounded-lg transition-colors"
                        >
                            Ir al Panel de Auditorios
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>