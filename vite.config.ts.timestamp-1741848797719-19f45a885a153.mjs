// vite.config.ts
import tailwindcss from "file:///var/www/node_modules/@tailwindcss/vite/dist/index.mjs";
import vue from "file:///var/www/node_modules/@vitejs/plugin-vue/dist/index.mjs";
import vueJsx from "file:///var/www/node_modules/@vitejs/plugin-vue-jsx/dist/index.mjs";
import laravel from "file:///var/www/node_modules/laravel-vite-plugin/dist/index.js";
import { fileURLToPath } from "node:url";
import AutoImport from "file:///var/www/node_modules/unplugin-auto-import/dist/vite.js";
import Components from "file:///var/www/node_modules/unplugin-vue-components/dist/vite.js";
import { VueRouterAutoImports, getPascalCaseRouteName } from "file:///var/www/node_modules/unplugin-vue-router/dist/index.mjs";
import VueRouter from "file:///var/www/node_modules/unplugin-vue-router/dist/vite.mjs";
import { defineConfig } from "file:///var/www/node_modules/vite/dist/node/index.js";
import Layouts from "file:///var/www/node_modules/vite-plugin-vue-layouts/dist/index.mjs";
import vuetify from "file:///var/www/node_modules/vite-plugin-vuetify/dist/index.mjs";
import svgLoader from "file:///var/www/node_modules/vite-svg-loader/index.js";
var __vite_injected_original_import_meta_url = "file:///var/www/vite.config.ts";
var vite_config_default = defineConfig({
  plugins: [
    // Docs: https://github.com/posva/unplugin-vue-router
    // ℹ️ This plugin should be placed before vue plugin
    VueRouter({
      getRouteName: (routeNode) => {
        return getPascalCaseRouteName(routeNode).replace(/([a-z\d])([A-Z])/g, "$1-$2").toLowerCase();
      },
      routesFolder: "resources/ts/pages"
    }),
    vue({
      template: {
        compilerOptions: {
          isCustomElement: (tag) => tag === "swiper-container" || tag === "swiper-slide"
        },
        transformAssetUrls: {
          base: null,
          includeAbsolute: false
        }
      }
    }),
    laravel({
      input: ["resources/ts/main.ts", "resources/styles/app.css"],
      refresh: true
    }),
    vueJsx(),
    // Docs: https://github.com/vuetifyjs/vuetify-loader/tree/master/packages/vite-plugin
    vuetify({
      styles: {
        configFile: "resources/styles/variables/_vuetify.scss"
      }
    }),
    // Docs: https://github.com/johncampionjr/vite-plugin-vue-layouts#vite-plugin-vue-layouts
    Layouts({
      layoutsDirs: "./resources/ts/layouts/"
    }),
    // Docs: https://github.com/antfu/unplugin-vue-components#unplugin-vue-components
    Components({
      dirs: ["resources/ts/@core/components", "resources/ts/views/demos", "resources/ts/components"],
      dts: true,
      resolvers: [
        (componentName) => {
          if (componentName === "VueApexCharts")
            return { name: "default", from: "vue3-apexcharts", as: "VueApexCharts" };
        }
      ]
    }),
    // Docs: https://github.com/antfu/unplugin-auto-import#unplugin-auto-import
    AutoImport({
      imports: ["vue", VueRouterAutoImports, "@vueuse/core", "@vueuse/math", "vue-i18n", "pinia"],
      dirs: [
        "./resources/ts/@core/utils",
        "./resources/ts/@core/composable/",
        "./resources/ts/composables/",
        "./resources/ts/utils/",
        "./resources/ts/plugins/*/composables/*"
      ],
      vueTemplate: true,
      // ℹ️ Disabled to avoid confusion & accidental usage
      ignore: ["useCookies", "useStorage"]
    }),
    svgLoader(),
    tailwindcss()
  ],
  define: { "process.env": {} },
  resolve: {
    alias: {
      "@core-scss": fileURLToPath(new URL("./resources/styles/@core", __vite_injected_original_import_meta_url)),
      "@": fileURLToPath(new URL("./resources/ts", __vite_injected_original_import_meta_url)),
      "@themeConfig": fileURLToPath(new URL("./themeConfig.ts", __vite_injected_original_import_meta_url)),
      "@core": fileURLToPath(new URL("./resources/ts/@core", __vite_injected_original_import_meta_url)),
      "@layouts": fileURLToPath(new URL("./resources/ts/@layouts", __vite_injected_original_import_meta_url)),
      "@images": fileURLToPath(new URL("./resources/images/", __vite_injected_original_import_meta_url)),
      "@styles": fileURLToPath(new URL("./resources/styles/", __vite_injected_original_import_meta_url)),
      "@configured-variables": fileURLToPath(new URL("./resources/styles/variables/_template.scss", __vite_injected_original_import_meta_url)),
      "@db": fileURLToPath(new URL("./resources/ts/plugins/fake-api/handlers/", __vite_injected_original_import_meta_url)),
      "@api-utils": fileURLToPath(new URL("./resources/ts/plugins/fake-api/utils/", __vite_injected_original_import_meta_url))
    }
  },
  build: {
    chunkSizeWarningLimit: 5e3
  },
  optimizeDeps: {
    exclude: ["vuetify"],
    entries: [
      "./resources/ts/**/*.vue"
    ]
  },
  server: {
    host: "0.0.0.0",
    // Allow connections from outside the container
    port: 5173,
    // Ensure the correct port is set
    strictPort: true,
    // Fail if the port is in use
    watch: {
      usePolling: true
      // Fix for file changes in Docker
    }
  }
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcudHMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCIvdmFyL3d3d1wiO2NvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9maWxlbmFtZSA9IFwiL3Zhci93d3cvdml0ZS5jb25maWcudHNcIjtjb25zdCBfX3ZpdGVfaW5qZWN0ZWRfb3JpZ2luYWxfaW1wb3J0X21ldGFfdXJsID0gXCJmaWxlOi8vL3Zhci93d3cvdml0ZS5jb25maWcudHNcIjtpbXBvcnQgdGFpbHdpbmRjc3MgZnJvbSAnQHRhaWx3aW5kY3NzL3ZpdGUnXG5pbXBvcnQgdnVlIGZyb20gJ0B2aXRlanMvcGx1Z2luLXZ1ZSdcbmltcG9ydCB2dWVKc3ggZnJvbSAnQHZpdGVqcy9wbHVnaW4tdnVlLWpzeCdcbmltcG9ydCBsYXJhdmVsIGZyb20gJ2xhcmF2ZWwtdml0ZS1wbHVnaW4nXG5pbXBvcnQgeyBmaWxlVVJMVG9QYXRoIH0gZnJvbSAnbm9kZTp1cmwnXG5pbXBvcnQgQXV0b0ltcG9ydCBmcm9tICd1bnBsdWdpbi1hdXRvLWltcG9ydC92aXRlJ1xuaW1wb3J0IENvbXBvbmVudHMgZnJvbSAndW5wbHVnaW4tdnVlLWNvbXBvbmVudHMvdml0ZSdcbmltcG9ydCB7IFZ1ZVJvdXRlckF1dG9JbXBvcnRzLCBnZXRQYXNjYWxDYXNlUm91dGVOYW1lIH0gZnJvbSAndW5wbHVnaW4tdnVlLXJvdXRlcidcbmltcG9ydCBWdWVSb3V0ZXIgZnJvbSAndW5wbHVnaW4tdnVlLXJvdXRlci92aXRlJ1xuaW1wb3J0IHsgZGVmaW5lQ29uZmlnIH0gZnJvbSAndml0ZSdcbmltcG9ydCBMYXlvdXRzIGZyb20gJ3ZpdGUtcGx1Z2luLXZ1ZS1sYXlvdXRzJ1xuaW1wb3J0IHZ1ZXRpZnkgZnJvbSAndml0ZS1wbHVnaW4tdnVldGlmeSdcbmltcG9ydCBzdmdMb2FkZXIgZnJvbSAndml0ZS1zdmctbG9hZGVyJ1xuXG4vLyBodHRwczovL3ZpdGVqcy5kZXYvY29uZmlnL1xuZXhwb3J0IGRlZmF1bHQgZGVmaW5lQ29uZmlnKHtcbiAgcGx1Z2luczogWy8vIERvY3M6IGh0dHBzOi8vZ2l0aHViLmNvbS9wb3N2YS91bnBsdWdpbi12dWUtcm91dGVyXG4gIC8vIFx1MjEzOVx1RkUwRiBUaGlzIHBsdWdpbiBzaG91bGQgYmUgcGxhY2VkIGJlZm9yZSB2dWUgcGx1Z2luXG4gICAgVnVlUm91dGVyKHtcbiAgICAgIGdldFJvdXRlTmFtZTogcm91dGVOb2RlID0+IHtcbiAgICAgIC8vIENvbnZlcnQgcGFzY2FsIGNhc2UgdG8ga2ViYWIgY2FzZVxuICAgICAgICByZXR1cm4gZ2V0UGFzY2FsQ2FzZVJvdXRlTmFtZShyb3V0ZU5vZGUpXG4gICAgICAgICAgLnJlcGxhY2UoLyhbYS16XFxkXSkoW0EtWl0pL2csICckMS0kMicpXG4gICAgICAgICAgLnRvTG93ZXJDYXNlKClcbiAgICAgIH0sXG5cbiAgICAgIHJvdXRlc0ZvbGRlcjogJ3Jlc291cmNlcy90cy9wYWdlcycsXG4gICAgfSksXG4gICAgdnVlKHtcbiAgICAgIHRlbXBsYXRlOiB7XG4gICAgICAgIGNvbXBpbGVyT3B0aW9uczoge1xuICAgICAgICAgIGlzQ3VzdG9tRWxlbWVudDogdGFnID0+IHRhZyA9PT0gJ3N3aXBlci1jb250YWluZXInIHx8IHRhZyA9PT0gJ3N3aXBlci1zbGlkZScsXG4gICAgICAgIH0sXG5cbiAgICAgICAgdHJhbnNmb3JtQXNzZXRVcmxzOiB7XG4gICAgICAgICAgYmFzZTogbnVsbCxcbiAgICAgICAgICBpbmNsdWRlQWJzb2x1dGU6IGZhbHNlLFxuICAgICAgICB9LFxuICAgICAgfSxcbiAgICB9KSxcbiAgICBsYXJhdmVsKHtcbiAgICAgIGlucHV0OiBbJ3Jlc291cmNlcy90cy9tYWluLnRzJywgJ3Jlc291cmNlcy9zdHlsZXMvYXBwLmNzcyddLFxuICAgICAgcmVmcmVzaDogdHJ1ZSxcbiAgICB9KSxcbiAgICB2dWVKc3goKSwgLy8gRG9jczogaHR0cHM6Ly9naXRodWIuY29tL3Z1ZXRpZnlqcy92dWV0aWZ5LWxvYWRlci90cmVlL21hc3Rlci9wYWNrYWdlcy92aXRlLXBsdWdpblxuICAgIHZ1ZXRpZnkoe1xuICAgICAgc3R5bGVzOiB7XG4gICAgICAgIGNvbmZpZ0ZpbGU6ICdyZXNvdXJjZXMvc3R5bGVzL3ZhcmlhYmxlcy9fdnVldGlmeS5zY3NzJyxcbiAgICAgIH0sXG4gICAgfSksIC8vIERvY3M6IGh0dHBzOi8vZ2l0aHViLmNvbS9qb2huY2FtcGlvbmpyL3ZpdGUtcGx1Z2luLXZ1ZS1sYXlvdXRzI3ZpdGUtcGx1Z2luLXZ1ZS1sYXlvdXRzXG4gICAgTGF5b3V0cyh7XG4gICAgICBsYXlvdXRzRGlyczogJy4vcmVzb3VyY2VzL3RzL2xheW91dHMvJyxcbiAgICB9KSwgLy8gRG9jczogaHR0cHM6Ly9naXRodWIuY29tL2FudGZ1L3VucGx1Z2luLXZ1ZS1jb21wb25lbnRzI3VucGx1Z2luLXZ1ZS1jb21wb25lbnRzXG4gICAgQ29tcG9uZW50cyh7XG4gICAgICBkaXJzOiBbJ3Jlc291cmNlcy90cy9AY29yZS9jb21wb25lbnRzJywgJ3Jlc291cmNlcy90cy92aWV3cy9kZW1vcycsICdyZXNvdXJjZXMvdHMvY29tcG9uZW50cyddLFxuICAgICAgZHRzOiB0cnVlLFxuICAgICAgcmVzb2x2ZXJzOiBbXG4gICAgICAgIGNvbXBvbmVudE5hbWUgPT4ge1xuICAgICAgICAvLyBBdXRvIGltcG9ydCBgVnVlQXBleENoYXJ0c2BcbiAgICAgICAgICBpZiAoY29tcG9uZW50TmFtZSA9PT0gJ1Z1ZUFwZXhDaGFydHMnKVxuICAgICAgICAgICAgcmV0dXJuIHsgbmFtZTogJ2RlZmF1bHQnLCBmcm9tOiAndnVlMy1hcGV4Y2hhcnRzJywgYXM6ICdWdWVBcGV4Q2hhcnRzJyB9XG4gICAgICAgIH0sXG4gICAgICBdLFxuICAgIH0pLCAvLyBEb2NzOiBodHRwczovL2dpdGh1Yi5jb20vYW50ZnUvdW5wbHVnaW4tYXV0by1pbXBvcnQjdW5wbHVnaW4tYXV0by1pbXBvcnRcbiAgICBBdXRvSW1wb3J0KHtcbiAgICAgIGltcG9ydHM6IFsndnVlJywgVnVlUm91dGVyQXV0b0ltcG9ydHMsICdAdnVldXNlL2NvcmUnLCAnQHZ1ZXVzZS9tYXRoJywgJ3Z1ZS1pMThuJywgJ3BpbmlhJ10sXG4gICAgICBkaXJzOiBbXG4gICAgICAgICcuL3Jlc291cmNlcy90cy9AY29yZS91dGlscycsXG4gICAgICAgICcuL3Jlc291cmNlcy90cy9AY29yZS9jb21wb3NhYmxlLycsXG4gICAgICAgICcuL3Jlc291cmNlcy90cy9jb21wb3NhYmxlcy8nLFxuICAgICAgICAnLi9yZXNvdXJjZXMvdHMvdXRpbHMvJyxcbiAgICAgICAgJy4vcmVzb3VyY2VzL3RzL3BsdWdpbnMvKi9jb21wb3NhYmxlcy8qJyxcbiAgICAgIF0sXG4gICAgICB2dWVUZW1wbGF0ZTogdHJ1ZSxcblxuICAgICAgLy8gXHUyMTM5XHVGRTBGIERpc2FibGVkIHRvIGF2b2lkIGNvbmZ1c2lvbiAmIGFjY2lkZW50YWwgdXNhZ2VcbiAgICAgIGlnbm9yZTogWyd1c2VDb29raWVzJywgJ3VzZVN0b3JhZ2UnXSxcbiAgICB9KSxcbiAgICBzdmdMb2FkZXIoKSxcbiAgICB0YWlsd2luZGNzcygpLFxuICBdLFxuICBkZWZpbmU6IHsgJ3Byb2Nlc3MuZW52Jzoge30gfSxcbiAgcmVzb2x2ZToge1xuICAgIGFsaWFzOiB7XG4gICAgICAnQGNvcmUtc2Nzcyc6IGZpbGVVUkxUb1BhdGgobmV3IFVSTCgnLi9yZXNvdXJjZXMvc3R5bGVzL0Bjb3JlJywgaW1wb3J0Lm1ldGEudXJsKSksXG4gICAgICAnQCc6IGZpbGVVUkxUb1BhdGgobmV3IFVSTCgnLi9yZXNvdXJjZXMvdHMnLCBpbXBvcnQubWV0YS51cmwpKSxcbiAgICAgICdAdGhlbWVDb25maWcnOiBmaWxlVVJMVG9QYXRoKG5ldyBVUkwoJy4vdGhlbWVDb25maWcudHMnLCBpbXBvcnQubWV0YS51cmwpKSxcbiAgICAgICdAY29yZSc6IGZpbGVVUkxUb1BhdGgobmV3IFVSTCgnLi9yZXNvdXJjZXMvdHMvQGNvcmUnLCBpbXBvcnQubWV0YS51cmwpKSxcbiAgICAgICdAbGF5b3V0cyc6IGZpbGVVUkxUb1BhdGgobmV3IFVSTCgnLi9yZXNvdXJjZXMvdHMvQGxheW91dHMnLCBpbXBvcnQubWV0YS51cmwpKSxcbiAgICAgICdAaW1hZ2VzJzogZmlsZVVSTFRvUGF0aChuZXcgVVJMKCcuL3Jlc291cmNlcy9pbWFnZXMvJywgaW1wb3J0Lm1ldGEudXJsKSksXG4gICAgICAnQHN0eWxlcyc6IGZpbGVVUkxUb1BhdGgobmV3IFVSTCgnLi9yZXNvdXJjZXMvc3R5bGVzLycsIGltcG9ydC5tZXRhLnVybCkpLFxuICAgICAgJ0Bjb25maWd1cmVkLXZhcmlhYmxlcyc6IGZpbGVVUkxUb1BhdGgobmV3IFVSTCgnLi9yZXNvdXJjZXMvc3R5bGVzL3ZhcmlhYmxlcy9fdGVtcGxhdGUuc2NzcycsIGltcG9ydC5tZXRhLnVybCkpLFxuICAgICAgJ0BkYic6IGZpbGVVUkxUb1BhdGgobmV3IFVSTCgnLi9yZXNvdXJjZXMvdHMvcGx1Z2lucy9mYWtlLWFwaS9oYW5kbGVycy8nLCBpbXBvcnQubWV0YS51cmwpKSxcbiAgICAgICdAYXBpLXV0aWxzJzogZmlsZVVSTFRvUGF0aChuZXcgVVJMKCcuL3Jlc291cmNlcy90cy9wbHVnaW5zL2Zha2UtYXBpL3V0aWxzLycsIGltcG9ydC5tZXRhLnVybCkpLFxuICAgIH0sXG4gIH0sXG4gIGJ1aWxkOiB7XG4gICAgY2h1bmtTaXplV2FybmluZ0xpbWl0OiA1MDAwLFxuICB9LFxuICBvcHRpbWl6ZURlcHM6IHtcbiAgICBleGNsdWRlOiBbJ3Z1ZXRpZnknXSxcbiAgICBlbnRyaWVzOiBbXG4gICAgICAnLi9yZXNvdXJjZXMvdHMvKiovKi52dWUnLFxuICAgIF0sXG4gIH0sICBcbiAgc2VydmVyOiB7XG4gICAgaG9zdDogXCIwLjAuMC4wXCIsIC8vIEFsbG93IGNvbm5lY3Rpb25zIGZyb20gb3V0c2lkZSB0aGUgY29udGFpbmVyXG4gICAgcG9ydDogNTE3MywgLy8gRW5zdXJlIHRoZSBjb3JyZWN0IHBvcnQgaXMgc2V0XG4gICAgc3RyaWN0UG9ydDogdHJ1ZSwgLy8gRmFpbCBpZiB0aGUgcG9ydCBpcyBpbiB1c2VcbiAgICB3YXRjaDoge1xuICAgICAgdXNlUG9sbGluZzogdHJ1ZSwgLy8gRml4IGZvciBmaWxlIGNoYW5nZXMgaW4gRG9ja2VyXG4gICAgfSxcbiAgfSxcbn0pXG4iXSwKICAibWFwcGluZ3MiOiAiO0FBQTBNLE9BQU8saUJBQWlCO0FBQ2xPLE9BQU8sU0FBUztBQUNoQixPQUFPLFlBQVk7QUFDbkIsT0FBTyxhQUFhO0FBQ3BCLFNBQVMscUJBQXFCO0FBQzlCLE9BQU8sZ0JBQWdCO0FBQ3ZCLE9BQU8sZ0JBQWdCO0FBQ3ZCLFNBQVMsc0JBQXNCLDhCQUE4QjtBQUM3RCxPQUFPLGVBQWU7QUFDdEIsU0FBUyxvQkFBb0I7QUFDN0IsT0FBTyxhQUFhO0FBQ3BCLE9BQU8sYUFBYTtBQUNwQixPQUFPLGVBQWU7QUFaa0csSUFBTSwyQ0FBMkM7QUFlekssSUFBTyxzQkFBUSxhQUFhO0FBQUEsRUFDMUIsU0FBUztBQUFBO0FBQUE7QUFBQSxJQUVQLFVBQVU7QUFBQSxNQUNSLGNBQWMsZUFBYTtBQUV6QixlQUFPLHVCQUF1QixTQUFTLEVBQ3BDLFFBQVEscUJBQXFCLE9BQU8sRUFDcEMsWUFBWTtBQUFBLE1BQ2pCO0FBQUEsTUFFQSxjQUFjO0FBQUEsSUFDaEIsQ0FBQztBQUFBLElBQ0QsSUFBSTtBQUFBLE1BQ0YsVUFBVTtBQUFBLFFBQ1IsaUJBQWlCO0FBQUEsVUFDZixpQkFBaUIsU0FBTyxRQUFRLHNCQUFzQixRQUFRO0FBQUEsUUFDaEU7QUFBQSxRQUVBLG9CQUFvQjtBQUFBLFVBQ2xCLE1BQU07QUFBQSxVQUNOLGlCQUFpQjtBQUFBLFFBQ25CO0FBQUEsTUFDRjtBQUFBLElBQ0YsQ0FBQztBQUFBLElBQ0QsUUFBUTtBQUFBLE1BQ04sT0FBTyxDQUFDLHdCQUF3QiwwQkFBMEI7QUFBQSxNQUMxRCxTQUFTO0FBQUEsSUFDWCxDQUFDO0FBQUEsSUFDRCxPQUFPO0FBQUE7QUFBQSxJQUNQLFFBQVE7QUFBQSxNQUNOLFFBQVE7QUFBQSxRQUNOLFlBQVk7QUFBQSxNQUNkO0FBQUEsSUFDRixDQUFDO0FBQUE7QUFBQSxJQUNELFFBQVE7QUFBQSxNQUNOLGFBQWE7QUFBQSxJQUNmLENBQUM7QUFBQTtBQUFBLElBQ0QsV0FBVztBQUFBLE1BQ1QsTUFBTSxDQUFDLGlDQUFpQyw0QkFBNEIseUJBQXlCO0FBQUEsTUFDN0YsS0FBSztBQUFBLE1BQ0wsV0FBVztBQUFBLFFBQ1QsbUJBQWlCO0FBRWYsY0FBSSxrQkFBa0I7QUFDcEIsbUJBQU8sRUFBRSxNQUFNLFdBQVcsTUFBTSxtQkFBbUIsSUFBSSxnQkFBZ0I7QUFBQSxRQUMzRTtBQUFBLE1BQ0Y7QUFBQSxJQUNGLENBQUM7QUFBQTtBQUFBLElBQ0QsV0FBVztBQUFBLE1BQ1QsU0FBUyxDQUFDLE9BQU8sc0JBQXNCLGdCQUFnQixnQkFBZ0IsWUFBWSxPQUFPO0FBQUEsTUFDMUYsTUFBTTtBQUFBLFFBQ0o7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsTUFDRjtBQUFBLE1BQ0EsYUFBYTtBQUFBO0FBQUEsTUFHYixRQUFRLENBQUMsY0FBYyxZQUFZO0FBQUEsSUFDckMsQ0FBQztBQUFBLElBQ0QsVUFBVTtBQUFBLElBQ1YsWUFBWTtBQUFBLEVBQ2Q7QUFBQSxFQUNBLFFBQVEsRUFBRSxlQUFlLENBQUMsRUFBRTtBQUFBLEVBQzVCLFNBQVM7QUFBQSxJQUNQLE9BQU87QUFBQSxNQUNMLGNBQWMsY0FBYyxJQUFJLElBQUksNEJBQTRCLHdDQUFlLENBQUM7QUFBQSxNQUNoRixLQUFLLGNBQWMsSUFBSSxJQUFJLGtCQUFrQix3Q0FBZSxDQUFDO0FBQUEsTUFDN0QsZ0JBQWdCLGNBQWMsSUFBSSxJQUFJLG9CQUFvQix3Q0FBZSxDQUFDO0FBQUEsTUFDMUUsU0FBUyxjQUFjLElBQUksSUFBSSx3QkFBd0Isd0NBQWUsQ0FBQztBQUFBLE1BQ3ZFLFlBQVksY0FBYyxJQUFJLElBQUksMkJBQTJCLHdDQUFlLENBQUM7QUFBQSxNQUM3RSxXQUFXLGNBQWMsSUFBSSxJQUFJLHVCQUF1Qix3Q0FBZSxDQUFDO0FBQUEsTUFDeEUsV0FBVyxjQUFjLElBQUksSUFBSSx1QkFBdUIsd0NBQWUsQ0FBQztBQUFBLE1BQ3hFLHlCQUF5QixjQUFjLElBQUksSUFBSSwrQ0FBK0Msd0NBQWUsQ0FBQztBQUFBLE1BQzlHLE9BQU8sY0FBYyxJQUFJLElBQUksNkNBQTZDLHdDQUFlLENBQUM7QUFBQSxNQUMxRixjQUFjLGNBQWMsSUFBSSxJQUFJLDBDQUEwQyx3Q0FBZSxDQUFDO0FBQUEsSUFDaEc7QUFBQSxFQUNGO0FBQUEsRUFDQSxPQUFPO0FBQUEsSUFDTCx1QkFBdUI7QUFBQSxFQUN6QjtBQUFBLEVBQ0EsY0FBYztBQUFBLElBQ1osU0FBUyxDQUFDLFNBQVM7QUFBQSxJQUNuQixTQUFTO0FBQUEsTUFDUDtBQUFBLElBQ0Y7QUFBQSxFQUNGO0FBQUEsRUFDQSxRQUFRO0FBQUEsSUFDTixNQUFNO0FBQUE7QUFBQSxJQUNOLE1BQU07QUFBQTtBQUFBLElBQ04sWUFBWTtBQUFBO0FBQUEsSUFDWixPQUFPO0FBQUEsTUFDTCxZQUFZO0FBQUE7QUFBQSxJQUNkO0FBQUEsRUFDRjtBQUNGLENBQUM7IiwKICAibmFtZXMiOiBbXQp9Cg==
