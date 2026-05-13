<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    users: { type: Array, required: true },
    canManage: { type: Boolean, default: false }
});

const page = usePage();
const flash = computed(() => page.props.flash);

const roleLabels = { admin: 'Admin', staff: 'Staff', user: 'Usuario' };
const roleColors = {
    admin: 'bg-purple-500/10 text-purple-400 border-purple-500/20',
    staff: 'bg-amber-500/10 text-amber-400 border-amber-500/20',
    user: 'bg-blue-500/10 text-blue-400 border-blue-500/20'
};

const updatingUserId = ref(null);

const changeRole = (user, newRole) => {
    if (newRole === user.role) return;
    updatingUserId.value = user.id;
    router.put(route('admin.users.update', user.id), { role: newRole }, {
        preserveScroll: true,
        onFinish: () => { updatingUserId.value = null; }
    });
};

const deleteUser = (user) => {
    if (confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
        router.delete(route('admin.users.destroy', user.id), { preserveScroll: true });
    }
};
</script>

<template>
    <Head title="Gestión de Usuarios" />

    <div class="min-h-screen bg-[#050508] text-white py-8 px-4 md:px-12 relative overflow-hidden font-sans">
        <div class="absolute top-[-10%] right-[-10%] w-[50%] h-[50%] bg-indigo-600/10 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[50%] h-[50%] bg-blue-600/10 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto relative z-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-10 gap-6">
                <div>
                    <h1 class="text-4xl font-bold tracking-tight text-white">
                        Gestión de <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-400">Usuarios</span>
                    </h1>
                    <p class="text-white/40 text-sm uppercase tracking-[0.2em] mt-2 font-medium">Panel de Control Administrativo</p>
                </div>
            </div>

            <div v-if="flash?.message" class="mb-6 px-6 py-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-sm font-medium">
                {{ flash.message }}
            </div>
            <div v-if="flash?.error" class="mb-6 px-6 py-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm font-medium">
                {{ flash.error }}
            </div>

            <div class="flex flex-wrap gap-2 mb-8">
                <Link :href="route('spaces.public.index')" class="px-5 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-white/70 hover:text-white transition-all text-sm font-medium">
                    <span class="flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Volver al Dashboard
                    </span>
                </Link>
                <div class="w-px h-7 bg-white/10 self-center"></div>
                <template v-if="$page.props.auth.user?.role === 'admin'">
                    <Link v-if="$page.component !== 'Admin/Spaces/Index'" :href="route('admin.spaces.index')" class="px-5 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-white/70 hover:text-white transition-all text-sm font-medium">Espacios</Link>
                </template>
                <Link v-if="$page.component !== 'Admin/Users/Index'" :href="route('admin.users.index')" class="px-5 py-2.5 rounded-xl bg-blue-600/20 border border-blue-500/30 text-blue-300 font-medium text-sm">Usuarios</Link>
                <Link :href="route('admin.reservations.index')" class="px-5 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-white/70 hover:text-white transition-all text-sm font-medium">Reservas y Asientos</Link>
            </div>

            <div class="backdrop-blur-2xl bg-white/[0.03] border border-white/10 rounded-[2.5rem] overflow-hidden shadow-2xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white/[0.02] border-b border-white/5">
                                <th class="py-6 px-8 text-[10px] uppercase tracking-[0.2em] text-white/30 font-bold">ID</th>
                                <th class="py-6 px-8 text-[10px] uppercase tracking-[0.2em] text-white/30 font-bold">Nombre</th>
                                <th class="py-6 px-8 text-[10px] uppercase tracking-[0.2em] text-white/30 font-bold">Email</th>
                                <th class="py-6 px-8 text-[10px] uppercase tracking-[0.2em] text-white/30 font-bold">Rol</th>
                                <th class="py-6 px-8 text-[10px] uppercase tracking-[0.2em] text-white/30 font-bold">Registro</th>
                                <th class="py-6 px-8 text-[10px] uppercase tracking-[0.2em] text-white/30 font-bold text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <tr v-for="user in props.users" :key="user.id" class="group hover:bg-white/[0.02] transition-all duration-300">
                                <td class="py-6 px-8"><span class="text-sm font-medium text-white/70">#{{ user.id }}</span></td>
                                <td class="py-6 px-8">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500/20 to-purple-500/20 border border-white/10 flex items-center justify-center text-xs font-bold text-white/70">
                                            {{ user.name.charAt(0).toUpperCase() }}
                                        </div>
                                        <div class="font-bold text-white group-hover:text-blue-400 transition-colors">{{ user.name }}</div>
                                    </div>
                                </td>
                                <td class="py-6 px-8"><span class="text-sm text-white/60">{{ user.email }}</span></td>
                                <td class="py-6 px-8">
                                    <span :class="roleColors[user.role] || roleColors.user" class="px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-widest border backdrop-blur-md">
                                        {{ roleLabels[user.role] || user.role }}
                                    </span>
                                </td>
                                <td class="py-6 px-8"><span class="text-sm text-white/50">{{ new Date(user.created_at).toLocaleDateString('es-ES') }}</span></td>
                                <td class="py-6 px-8 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <template v-if="canManage">
                                            <select
                                                :value="user.role"
                                                :disabled="user.id === $page.props.auth.user?.id || updatingUserId === user.id"
                                                @change="changeRole(user, ($event.target).value)"
                                                class="appearance-none px-3 py-2 rounded-xl text-xs font-bold border bg-white/5 border-white/10 text-white/80 hover:text-white hover:border-white/30 transition-all disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400/30"
                                            >
                                                <option value="user" class="bg-[#0a0a0c]">Usuario</option>
                                                <option value="staff" class="bg-[#0a0a0c]">Staff</option>
                                                <option value="admin" class="bg-[#0a0a0c]">Admin</option>
                                            </select>
                                            <button
                                                @click="deleteUser(user)"
                                                :disabled="user.id === $page.props.auth.user?.id"
                                                class="p-2.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/10 text-rose-400 hover:text-rose-300 transition-all disabled:opacity-40 disabled:cursor-not-allowed"
                                                title="Eliminar"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </template>
                                        <span v-else class="text-[10px] text-white/20 italic">Solo lectura</span>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="props.users.length === 0">
                                <td colspan="6" class="py-20 text-center">
                                    <div class="flex flex-col items-center gap-4">
                                        <div class="w-16 h-16 rounded-3xl bg-white/5 flex items-center justify-center border border-white/5">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white/20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 0V6m4.252 1.764a4 4 0 100 0M6 12a4 4 0 100 0M12 19.646a4 4 0 100 0M18 12a4 4 0 100 0M12 8v4m4 4l-2-2m-6-2L8 14" />
                                            </svg>
                                        </div>
                                        <p class="text-white/30 font-medium">No hay usuarios registrados aún.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
