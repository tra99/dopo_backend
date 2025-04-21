<template>
    <div class="custom-select" @click="toggleDropdown" ref="select">
      <div class="selected">{{ selectedTitle || "Select an option" }}</div>
      <ul v-if="isOpen" class="dropdown">
        <li v-for="item in items" :key="item.id" class="dropdown-item">
          <div
            class="dropdown-title"
            @click="selectItem(item)"
            @mouseover="openSubmenu(item)"
          >
            {{ item.title }}
            <span v-if="item.children.length">▶</span>
          </div>
          <ul class="submenu">
            <li
              v-for="sub in item.children"
              :key="sub.id"
              class="submenu-item"
              @click.stop="selectItem(sub)"
            >
              {{ sub.title }}
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </template>
  
  <script setup>
  import { computed, onMounted, onUnmounted, ref } from "vue";
  
  const props = defineProps({
    modelValue: String,
    items: Array,
  });
  
  const emit = defineEmits(["update:modelValue"]);
  
  const isOpen = ref(false);
  const activeSubmenu = ref(null);
  const select = ref(null);
  
  const selectedTitle = computed(() => {
    const findItem = (list) =>
      list.flatMap((item) =>
        item.id === props.modelValue
          ? item
          : item.children.length
          ? findItem(item.children)
          : []
      )[0];
  
    return findItem(props.items)?.title || "";
  });
  
  const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
  };
  
  const selectItem = (item) => {
    emit("update:modelValue", item.id);
    isOpen.value = false;
  };
  
  const openSubmenu = (item) => {
    activeSubmenu.value = item.children.length ? item : null;
  };
  
  // Close dropdown when clicking outside
  const handleClickOutside = (event) => {
    if (select.value && !select.value.contains(event.target)) {
      isOpen.value = false;
      activeSubmenu.value = null;
    }
  };
  
  onMounted(() => {
    document.addEventListener("click", handleClickOutside);
  });
  
  onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside);
  });
  </script>
  
  <style scoped>
  .custom-select {
    position: relative;
    width: 200px;
    background: #fff;
    border: 1px solid #ccc;
    padding: 8px;
    cursor: pointer;
  }
  
  .selected {
    padding: 5px;
  }
  
  .dropdown {
    position: absolute;
    width: 100%;
    background: white;
    border: 1px solid #ccc;
    list-style: none;
    padding: 0;
    margin: 0;
  }
  
  .dropdown-item {
    position: relative;
    padding: 8px;
    cursor: pointer;
  }
  
  .dropdown-item:hover {
    background: #f0f0f0;
  }
  
  .dropdown-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .submenu {
    position: absolute;
    left: 100%;
    top: 0;
    background: white;
    border: 1px solid #ccc;
    list-style: none;
    padding: 0;
    margin: 0;
    display: block;
    min-width: 150px;
  }
  
  .submenu-item {
    padding: 8px;
    cursor: pointer;
  }
  
  .submenu-item:hover {
    background: #ddd;
  }
  </style>
  