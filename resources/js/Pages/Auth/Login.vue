<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    status: String
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Iniciar Sesión" />

    <div class="min-h-screen bg-[#050508] flex flex-col justify-center items-center px-4 relative overflow-hidden font-sans">
        <div class="absolute top-[-20%] right-[-10%] w-[60%] h-[60%] bg-blue-600/15 rounded-full blur-[150px] pointer-events-none"></div>
        <div class="absolute bottom-[-20%] left-[-10%] w-[60%] h-[60%] bg-indigo-600/15 rounded-full blur-[150px] pointer-events-none"></div>

        <div class="w-full max-w-md relative z-10">
            <div class="backdrop-blur-3xl bg-white/[0.02] border border-white/10 rounded-[2.5rem] p-10 shadow-2xl relative overflow-hidden">
                
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 mb-4 shadow-lg shadow-blue-500/30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-extrabold tracking-tight text-white">Bienvenido de nuevo</h2>
                    <p class="text-white/40 text-xs mt-1">Ingresa tus credenciales para reservar tus puestos</p>
                </div>

                <div v-if="status" class="mb-4 font-medium text-sm text-green-400 text-center">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-white/40 uppercase tracking-widest block">Correo Electrónico</label>
                        <input 
                            type="email" 
                            v-model="form.email" 
                            required 
                            autofocus 
                            autocomplete="username"
                            placeholder="ejemplo@correo.com"
                            class="w-full bg-white/5 border border-white/10 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 rounded-xl px-4 py-3.5 text-sm text-white placeholder-white/20 transition-all outline-none"
                        />
                        <p v-if="form.errors.email" class="text-xs text-red-400 mt-1">{{ form.errors.email }}</p>
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <label class="text-xs font-semibold text-white/40 uppercase tracking-widest block">Contraseña</label>
                            <Link :href="route('password.request')" class="text-xs text-blue-400 hover:text-blue-300 transition-colors">
                                ¿La olvidaste?
                            </Link>
                        </div>
                        <input 
                            type="password" 
                            v-model="form.password" 
                            required 
                            autocomplete="current-password"
                            placeholder="••••••••••••"
                            class="w-full bg-white/5 border border-white/10 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 rounded-xl px-4 py-3.5 text-sm text-white placeholder-white/20 transition-all outline-none"
                        />
                        <p v-if="form.errors.password" class="text-xs text-red-400 mt-1">{{ form.errors.password }}</p>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input 
                                type="checkbox" 
                                v-model="form.remember" 
                                class="rounded bg-white/5 border-white/10 text-blue-600 focus:ring-0 focus:ring-offset-0 cursor-pointer"
                            />
                            <span class="text-xs text-white/40 group-hover:text-white/60 transition-colors">Recordarme en este equipo</span>
                        </label>
                    </div>

                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold py-4 rounded-xl shadow-lg shadow-blue-500/20 transform transition-all active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed text-sm"
                    >
                        <span v-if="form.processing">Procesando...</span>
                        <span v-else>Iniciar Sesión</span>
                    </button>
                </form>

                <div class="mt-8 text-center border-t border-white/5 pt-6">
                    <p class="text-xs text-white/40">
                        ¿No tienes una cuenta? 
                        <Link :href="route('register')" class="text-blue-400 hover:text-blue-300 font-semibold ml-1 transition-colors">
                            Regístrate aquí
                        </Link>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>