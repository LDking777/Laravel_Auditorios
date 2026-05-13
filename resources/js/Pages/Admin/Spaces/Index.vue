<script setup>
import { ref } from 'vue';
import { Link, useForm, router, Head } from '@inertiajs/vue3';

// Props enviadas desde el SpaceController (index)
const props = defineProps({
  spaces: {
    type: Array,
    required: true
  }
});

// Estado para abrir/cerrar el modal de formulario (Crear y Editar)
const isModalOpen = ref(false);
const isEditing = ref(false);
const currentSpaceId = ref(null);

// Formulario reactivo optimizado con Inertia
const form = useForm({
  name: '',
  type: '',
  capacity: '',
  description: '',
  price_per_hour: '',
  is_active: true
});

// Abrir modal para Crear
const openCreateModal = () => {
  isEditing.value = false;
  currentSpaceId.value = null;
  form.reset();
  form.clearErrors();
  isModalOpen.value = true;
};

// Abrir modal para Editar y precargar datos
const openEditModal = (space) => {
  isEditing.value = true;
  currentSpaceId.value = space.id;
  form.clearErrors();
  
  form.name = space.name;
  form.type = space.type;
  form.capacity = space.capacity;
  form.description = space.description;
  form.price_per_hour = space.price_per_hour;
  form.is_active = !!space.is_active; // Forzar booleano
  
  isModalOpen.value = true;
};

// Guardar (Procesa tanto Store como Update)
const submitForm = () => {
  if (isEditing.value) {
    form.put(route('admin.spaces.update', currentSpaceId.value), {
      onSuccess: () => closeModal()
    });
  } else {
    form.post(route('admin.spaces.store'), {
      onSuccess: () => closeModal()
    });
  }
};

// Eliminar Auditorio con Confirmación Estética
const deleteSpace = (spaceId) => {
  if (confirm('¿Estás seguro de que deseas eliminar este auditorio permanentemente?')) {
    router.delete(route('admin.spaces.destroy', spaceId));
  }
};

const closeModal = () => {
  isModalOpen.value = false;
  form.reset();
};
</script>

<template>
  <Head title="Gestión de Espacios" />

  <div class="min-h-screen bg-[#050505] text-white p-6 md:p-12 font-sans relative overflow-hidden">
    <!-- Background Accents -->
    <div class="absolute top-[-10%] left-[-5%] w-[50%] h-[50%] bg-blue-600/10 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-5%] w-[50%] h-[50%] bg-purple-600/10 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto relative z-10">
      <!-- Header Section -->
      <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-12 gap-6">
        <div>
          <h1 class="text-4xl font-bold tracking-tight text-white">
            Gestión de <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-400">Espacios</span>
          </h1>
          <p class="text-white/40 text-sm uppercase tracking-[0.2em] mt-2 font-medium">Panel de Control Administrativo</p>
        </div>
        
        <button 
          @click="openCreateModal"
          class="group flex items-center gap-2 px-6 py-3.5 bg-white text-black font-bold rounded-2xl shadow-xl hover:bg-blue-50 transition-all active:scale-95"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform group-hover:rotate-90 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
          </svg>
          Nuevo Auditorio
        </button>
      </div>

      <!-- Navigation Menu -->
      <div class="flex flex-wrap gap-2 mb-8">
        <Link :href="route('spaces.public.index')" class="px-5 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-white/70 hover:text-white transition-all text-sm font-medium">
          <span class="flex items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Volver al Dashboard
          </span>
        </Link>
        <div class="w-px h-7 bg-white/10 self-center"></div>
        <Link v-if="$page.component !== 'Admin/Spaces/Index'" :href="route('admin.spaces.index')" class="px-5 py-2.5 rounded-xl bg-blue-600/20 border border-blue-500/30 text-blue-300 font-medium text-sm">Espacios</Link>
        <Link v-if="$page.component !== 'Admin/Users/Index'" :href="route('admin.users.index')" class="px-5 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-white/70 hover:text-white transition-all text-sm font-medium">Usuarios</Link>
        <Link v-if="$page.component !== 'Admin/Reservations/Index'" :href="route('admin.reservations.index')" class="px-5 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-white/70 hover:text-white transition-all text-sm font-medium">Reservas y Asientos</Link>
      </div>

      <!-- Table Container -->
      <div class="backdrop-blur-2xl bg-white/[0.03] border border-white/10 rounded-[2.5rem] overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-white/[0.02] border-b border-white/5">
                <th class="py-6 px-8 text-[10px] uppercase tracking-[0.2em] text-white/30 font-bold">Auditorio / Espacio</th>
                <th class="py-6 px-8 text-[10px] uppercase tracking-[0.2em] text-white/30 font-bold">Tipo</th>
                <th class="py-6 px-8 text-[10px] uppercase tracking-[0.2em] text-white/30 font-bold text-center">Capacidad</th>
                <th class="py-6 px-8 text-[10px] uppercase tracking-[0.2em] text-white/30 font-bold">Tarifa/Hora</th>
                <th class="py-6 px-8 text-[10px] uppercase tracking-[0.2em] text-white/30 font-bold text-center">Estado</th>
                <th class="py-6 px-8 text-[10px] uppercase tracking-[0.2em] text-white/30 font-bold text-right">Acciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
              <tr 
                v-for="space in props.spaces" 
                :key="space.id"
                class="group hover:bg-white/[0.02] transition-all duration-300"
              >
                <td class="py-6 px-8">
                  <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center border border-white/5 group-hover:border-white/20 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <div class="font-bold text-white group-hover:text-blue-400 transition-colors">{{ space.name }}</div>
                        <div class="text-xs text-white/30 line-clamp-1 mt-0.5">{{ space.description }}</div>
                    </div>
                  </div>
                </td>
                
                <td class="py-6 px-8">
                  <span class="px-3 py-1 text-[10px] font-bold uppercase tracking-widest rounded-lg bg-white/5 border border-white/10 text-white/50">
                    {{ space.type }}
                  </span>
                </td>
                
                <td class="py-6 px-8 text-center">
                  <span class="text-sm font-medium text-white/70">{{ space.capacity }} <span class="text-[10px] text-white/30">pax</span></span>
                </td>
                
                <td class="py-6 px-8">
                  <span class="text-sm font-bold text-blue-400">
                    ${{ Number(space.price_per_hour || 0).toLocaleString('es-ES') }}
                  </span>
                </td>
                
                <td class="py-6 px-8 text-center">
                  <span 
                    :class="space.is_active 
                      ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' 
                      : 'bg-rose-500/10 text-rose-400 border-rose-500/20'" 
                    class="px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-widest border backdrop-blur-md"
                  >
                    {{ space.is_active ? 'Activo' : 'Inactivo' }}
                  </span>
                </td>
                
                <td class="py-6 px-8 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <button 
                        @click="openEditModal(space)"
                        class="p-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/5 text-white/40 hover:text-white transition-all"
                        title="Editar"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                    <button 
                        @click="deleteSpace(space.id)"
                        class="p-2.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/10 text-rose-400 hover:text-rose-300 transition-all"
                        title="Eliminar"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                  </div>
                </td>
              </tr>
              
              <tr v-if="props.spaces.length === 0">
                <td colspan="6" class="py-20 text-center">
                  <div class="flex flex-col items-center gap-4">
                    <div class="w-16 h-16 rounded-3xl bg-white/5 flex items-center justify-center border border-white/5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white/20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                    </div>
                    <p class="text-white/30 font-medium">No hay auditorios creados aún.</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal Glassmorphism -->
    <div 
      v-if="isModalOpen" 
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-md transition-all duration-500"
    >
      <div class="relative w-full max-w-xl overflow-hidden rounded-[2.5rem] border border-white/20 bg-[#0a0a0c]/80 backdrop-blur-3xl p-10 shadow-2xl animate-in zoom-in-95 duration-300">
        
        <div class="flex items-center justify-between mb-10">
          <div>
            <h3 class="text-2xl font-bold text-white">
                {{ isEditing ? 'Editar' : 'Nuevo' }} <span class="text-blue-400">Auditorio</span>
            </h3>
            <p class="text-white/40 text-xs uppercase tracking-widest mt-1">Completa los detalles del espacio</p>
          </div>
          <button 
            @click="closeModal" 
            class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-white/40 hover:text-white hover:bg-white/10 transition-all"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <form @submit.prevent="submitForm" class="space-y-6">
          
          <div class="space-y-2">
            <label class="text-[10px] font-bold uppercase tracking-widest text-white/40 ml-2">Nombre del Espacio</label>
            <input 
              v-model="form.name" 
              type="text" 
              placeholder="Ej: Auditorio Magno Apple"
              class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all"
            />
            <span v-if="form.errors.name" class="text-rose-400 text-xs mt-1 block ml-2">{{ form.errors.name }}</span>
          </div>

          <div class="grid grid-cols-2 gap-6">
            <div class="space-y-2">
              <label class="text-[10px] font-bold uppercase tracking-widest text-white/40 ml-2">Tipo</label>
              <input 
                v-model="form.type" 
                type="text" 
                placeholder="Ej: Acústico"
                class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all"
              />
              <span v-if="form.errors.type" class="text-rose-400 text-xs mt-1 block ml-2">{{ form.errors.type }}</span>
            </div>
            
            <div class="space-y-2">
              <label class="text-[10px] font-bold uppercase tracking-widest text-white/40 ml-2">Estado</label>
              <select 
                v-model="form.is_active" 
                class="w-full bg-[#1a1a1c] border border-white/10 rounded-2xl px-5 py-4 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all appearance-none"
              >
                <option :value="true">Activo</option>
                <option :value="false">Inactivo</option>
              </select>
              <span v-if="form.errors.is_active" class="text-rose-400 text-xs mt-1 block ml-2">{{ form.errors.is_active }}</span>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-6">
            <div class="space-y-2">
              <label class="text-[10px] font-bold uppercase tracking-widest text-white/40 ml-2">Capacidad</label>
              <input 
                v-model="form.capacity" 
                type="number" 
                placeholder="120"
                class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all"
              />
              <span v-if="form.errors.capacity" class="text-rose-400 text-xs mt-1 block ml-2">{{ form.errors.capacity }}</span>
            </div>

            <div class="space-y-2">
              <label class="text-[10px] font-bold uppercase tracking-widest text-white/40 ml-2">Tarifa/Hora ($)</label>
              <input 
                v-model="form.price_per_hour" 
                type="number" 
                step="0.01"
                placeholder="45000"
                class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all"
              />
              <span v-if="form.errors.price_per_hour" class="text-rose-400 text-xs mt-1 block ml-2">{{ form.errors.price_per_hour }}</span>
            </div>
          </div>

          <div class="space-y-2">
            <label class="text-[10px] font-bold uppercase tracking-widest text-white/40 ml-2">Descripción</label>
            <textarea 
              v-model="form.description" 
              rows="3"
              placeholder="Detalles premium del espacio..."
              class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all resize-none"
            ></textarea>
            <span v-if="form.errors.description" class="text-rose-400 text-xs mt-1 block ml-2">{{ form.errors.description }}</span>
          </div>

          <div class="flex justify-end gap-4 pt-8 border-t border-white/5">
            <button 
              type="button" 
              @click="closeModal" 
              class="px-8 py-4 rounded-2xl bg-white/5 hover:bg-white/10 border border-white/5 text-white/60 text-sm font-bold transition-all"
            >
              Cancelar
            </button>
            <button 
              type="submit" 
              :disabled="form.processing"
              class="px-8 py-4 rounded-2xl bg-white text-black font-bold text-sm transition-all hover:bg-blue-50 active:scale-95 disabled:opacity-50 shadow-xl"
            >
              {{ form.processing ? 'Guardando...' : (isEditing ? 'Actualizar Espacio' : 'Crear Espacio') }}
            </button>
          </div>

        </form>
      </div>
    </div>

  </div>
</template>

<style scoped>
/* Custom scrollbar for the table */
.overflow-x-auto::-webkit-scrollbar {
    height: 6px;
}
.overflow-x-auto::-webkit-scrollbar-track {
    background: transparent;
}
.overflow-x-auto::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
}
.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.1);
}
</style>
