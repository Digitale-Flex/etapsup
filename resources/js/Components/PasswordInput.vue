<template>
  <div class="position-relative">
    <label class="form-label" :class="labelClass" :for="id ?? 'form-password'">{{
      label ?? 'Enter password'
    }}</label>
    <input
      class="form-control"
      :type="showPassword ? 'text' : 'password'"
      :id="id ?? 'form-password'"
      :class="inputClass"
      :placeholder="placeHolder ?? 'Enter your password'"
      :value="modelValue"
      @input="updateValue"
      v-bind="$attrs"
    />

    <span class="position-absolute top-50 end-0 translate-middle-y p-0 mt-3">
      <FontAwesomeIcon
        v-if="showPassword"
        :icon="faEye"
        @click="setShowPassword"
        class="cursor-pointer pe-3 mt-1"
      />
      <FontAwesomeIcon
        v-else
        :icon="faEyeSlash"
        @click="setShowPassword"
        class="cursor-pointer pe-3 mt-1"
      />
    </span>
  </div>
</template>

<script setup lang="ts">
import { faEyeSlash, faEye } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { ref } from 'vue'

type PasswordInputPropType = {
  id?: string
  value?: string
  label?: string
  labelClass?: string
  inputClass?: string
  placeHolder?: string
  error?: string
  modelValue?: string
}

defineProps<PasswordInputPropType>()

const showPassword = ref<boolean>(false)

const setShowPassword = () => {
  return (showPassword.value = !showPassword.value)
}

const emit = defineEmits(['update:modelValue'])

const updateValue = (e: Event) => {
  emit('update:modelValue', (e.target as HTMLInputElement).value)
}
</script>
