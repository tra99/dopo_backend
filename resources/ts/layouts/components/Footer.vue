<template>
  <div class="h-100 text-sm d-flex align-center justify-md-space-between justify-center">
    <!-- 👉 Footer: left content -->
    <span class="d-flex align-center text-secondary font-medium">
      &copy;
      {{ new Date().getFullYear() }} by &nbsp;
      <a href="https://sala.co" target="noopener noreferrer">Sala Tech</a>
    </span>
    <!-- 👉 Footer: right content -->
    <span class="d-md-flex gap-x-4 text-primary d-none">
      <a
        href="https://themeforest.net/licenses/standard"
        target="noopener noreferrer"
      >License</a>
      <a
        href="https://demos.pixinvent.com/vuexy-vuejs-admin-template/documentation/guide/laravel-integration/folder-structure.html"
        target="noopener noreferrer"
      >Documentation</a>
    </span>
  </div>
</template>
