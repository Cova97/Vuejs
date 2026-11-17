<script setup lang="ts">
import { ref, reactive } from 'vue'
import { companiesApi } from '@/api/companies'
import { ValidationError } from '@/api/http'
import type { Company, CompanyInput } from '@/types/company'
import type { ValidationErrors } from '@/types/part'

// Mismo patrón que PartForm: con empresa = editar, sin empresa = crear.
const props = defineProps<{ company?: Company }>()
const emit = defineEmits<{ saved: [company: Company] }>()

const isEdit = !!props.company

function emptyForm(): CompanyInput {
  return { name: '', address: '', phone: '', rfc: '' }
}

function fromCompany(company: Company): CompanyInput {
  return {
    name: company.name,
    address: company.address ?? '',
    phone: company.phone ?? '',
    rfc: company.rfc,
  }
}

const form = reactive<CompanyInput>(props.company ? fromCompany(props.company) : emptyForm())
const errors = ref<ValidationErrors>({})
const saving = ref(false)
const error = ref<string | null>(null)

async function submit() {
  saving.value = true
  errors.value = {}
  error.value = null

  try {
    // El RFC se guarda siempre en mayúsculas.
    const data = { ...form, rfc: form.rfc.trim().toUpperCase() }
    const saved = props.company
      ? await companiesApi.update(props.company.id, data)
      : await companiesApi.create(data)

    if (!isEdit) Object.assign(form, emptyForm())
    emit('saved', saved)
  } catch (e) {
    if (e instanceof ValidationError) errors.value = e.errors
    else error.value = String(e)
  } finally {
    saving.value = false
  }
}
</script>

<template>
  <form @submit.prevent="submit">
    <div class="card-header">
      <h2>{{ isEdit ? 'Editar empresa' : 'Nueva empresa' }}</h2>
    </div>

    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <div class="form-grid">
      <div class="field">
        <label for="name">Nombre</label>
        <input
          id="name"
          v-model="form.name"
          class="input"
          :class="{ 'has-error': errors.name }"
          placeholder="Ej. Refacciones del Norte"
        />
        <small v-if="errors.name" class="field-error">{{ errors.name[0] }}</small>
      </div>

      <div class="field">
        <label for="rfc">RFC</label>
        <input
          id="rfc"
          v-model="form.rfc"
          class="input mono"
          :class="{ 'has-error': errors.rfc }"
          maxlength="13"
          placeholder="12 o 13 caracteres"
        />
        <small v-if="errors.rfc" class="field-error">{{ errors.rfc[0] }}</small>
      </div>

      <div class="field">
        <label for="phone">Teléfono</label>
        <input
          id="phone"
          v-model="form.phone"
          class="input"
          :class="{ 'has-error': errors.phone }"
          placeholder="Opcional"
        />
        <small v-if="errors.phone" class="field-error">{{ errors.phone[0] }}</small>
      </div>

      <div class="field">
        <label for="address">Dirección</label>
        <input
          id="address"
          v-model="form.address"
          class="input"
          :class="{ 'has-error': errors.address }"
          placeholder="Opcional"
        />
        <small v-if="errors.address" class="field-error">{{ errors.address[0] }}</small>
      </div>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary" :disabled="saving">
        {{ saving ? 'Guardando...' : isEdit ? 'Guardar cambios' : 'Crear empresa' }}
      </button>
      <slot />
    </div>
  </form>
</template>
