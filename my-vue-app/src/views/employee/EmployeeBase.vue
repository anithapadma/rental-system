<template>
  <EmployeeLayout>
    <router-view></router-view>
  </EmployeeLayout>
</template>

<script>
import EmployeeLayout from '@/components/EmployeeLayout.vue'

export default {
  name: 'EmployeeBase',
  components: {
    EmployeeLayout
  }
}
</script>