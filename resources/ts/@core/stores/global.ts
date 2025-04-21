import { defineStore } from "pinia";
import { ref } from "vue";

export const useGlobalStore = defineStore("global", () => {
  const isDialogVisible = ref(false);
  const setIsDialogVisible = (val: boolean) => isDialogVisible.value = val

  return { isDialogVisible, setIsDialogVisible };
});
