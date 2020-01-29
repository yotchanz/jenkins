<template>
  <v-dialog v-model="dialog">
    <v-container style="overflow-y: hidden">
      <div class="text-xs-center" v-if="inProgress">
        <v-progress-circular :size="100" color="primary" indeterminate />
      </div>
    </v-container>
  </v-dialog>
</template>

<script>
export default {
  data: () => ({
    inProgress: true,
    dialog: false,
    options: {
      color: 'primary',
    }
  }),
  methods: {
    open() {
      this.dialog = true;
    },
    close() {
      this.dialog = false;
    }
  }
}
</script>
