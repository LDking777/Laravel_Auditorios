<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Registrarse" />

    <div class="min-h-screen bg-[#050508] flex flex-col justify-center items-center px-4 relative overflow-hidden font-sans">
        <div class="absolute top-[-20%] left-[-10%] w-[60%] h-[60%] bg-blue-600/15 rounded-full blur-[150px] pointer-events-none"></div>
        <div class="absolute bottom-[-20%] right-[-10%] w-[60%] h-[60%] bg-indigo-600/15 rounded-full blur-[150px] pointer-events-none"></div>

        <div class="w-full max-w-md relative z-10 my-8">
            <div class="backdrop-blur-3xl bg-white/[0.02] border border-white/10 rounded-[2.5rem] p-10 shadow-2xl relative overflow-hidden">
                
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 mb-4 shadow-lg shadow-blue-500/30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-extrabold tracking-tight text-white">Crear Cuenta</h2>
                    <p class="text-white/40 text-xs mt-1">Regístrate para empezar a reservar tus puestos</p>
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-white/40 uppercase tracking-widest block">Nombre Completo</label>
                        <input 
                            type="text" 
                            v-model="form.name" 
                            required 
                            autofocus 
                            autocomplete="name"
                            placeholder="Tu nombre completo"
                            class="w-full bg-white/5 border border-white/10 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 rounded-xl px-4 py-3 text-sm text-white placeholder-white/20 transition-all outline-none"
                        />
                        <p v-if="form.errors.name" class="text-xs text-red-400 mt-1">{{ form.errors.name }}</p>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-white/40 uppercase tracking-widest block">Correo Electrónico</label>
                        <input 
                            type="email" 
                            v-model="form.email" 
                            required 
                            autocomplete="username"
                            placeholder="ejemplo@correo.com"
                            class="w-full bg-white/5 border border-white/10 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 rounded-xl px-4 py-3 text-sm text-white placeholder-white/20 transition-all outline-none"
                        />
                        <p v-if="form.errors.email" class="text-xs text-red-400 mt-1">{{ form.errors.email }}</p>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-white/40 uppercase tracking-widest block">Contraseña</label>
                        <input 
                            type="password" 
                            v-model="form.password" 
                            required 
                            autocomplete="new-password"
                            placeholder="••••••••••••"
                            class="w-full bg-white/5 border border-white/10 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 rounded-xl px-4 py-3 text-sm text-white placeholder-white/20 transition-all outline-none"
                        />
                        <p v-if="form.errors.password" class="text-xs text-red-400 mt-1">{{ form.errors.password }}</p>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-white/40 uppercase tracking-widest block">Confirmar Contraseña</label>
                        <input 
                            type="password" 
                            v-model="form.password_confirmation" 
                            required 
                            autocomplete="new-password"
                            placeholder="••••••••••••"
                            class="w-full bg-white/5 border border-white/10 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 rounded-xl px-4 py-3 text-sm text-white placeholder-white/20 transition-all outline-none"
                        />
                        <p v-if="form.errors.password_confirmation" class="text-xs text-red-400 mt-1">{{ form.errors.password_confirmation }}</p>
                    </div>

                    <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="flex items-start gap-2 mt-2">
                        <input 
                            id="terms" 
                            type="checkbox" 
                            v-model="form.terms" 
                            required 
                            class="rounded mt-0.5 bg-white/5 border-white/10 text-blue-600 focus:ring-0 focus:ring-offset-0 cursor-pointer"
                        />
                        <label for="terms" class="text-xs text-white/40 leading-relaxed cursor-pointer">
                            Acepto los <a target="_blank" :href="route('terms.show')" class="underline text-blue-400 hover:text-blue-300">Términos de servicio</a> y la <a target="_blank" :href="route('policy.show')" class="underline text-blue-400 hover:text-blue-300">Política de privacidad</a>
                        </label>
                    </div>

                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold py-4 rounded-xl shadow-lg shadow-blue-500/20 transform transition-all active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed text-sm mt-4"
                    >
                        <span v-if="form.processing">Creando cuenta...</span>
                        <span v-else>Registrarse</span>
                    </button>
                </form>

                <div class="mt-8 text-center border-t border-white/5 pt-6">
                    <p class="text-xs text-white/40">
                        ¿Ya tienes una cuenta? 
                        <Link :href="route('login')" class="text-blue-400 hover:text-blue-300 font-semibold ml-1 transition-colors">
                            Inicia sesión aquí
                        </Link>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>