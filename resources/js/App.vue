<template>
  <v-app>
    <v-toolbar color="#003F96" dark style="flex: none;">
      <slot name="actions">
        <v-toolbar-title>お客様の基幹システム（デモ）</v-toolbar-title>
      </slot>
    </v-toolbar>

    <v-content>
      <router-view/>
    </v-content>
  </v-app>
</template>