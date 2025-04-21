import { defineStore } from "pinia";
import { ref } from "vue";

export const useSnackbarStore = defineStore("snackbar", () => {
  const isOpen = ref(false);
  const message = ref("");
  const color = ref("success"); // Can be 'success', 'error', 'warning', etc.

  function openSnackbar(msg: string, msgColor: string = "success") {
    message.value = msg;
    color.value = msgColor;
    isOpen.value = true;

    // Auto-close after 3 seconds
    setTimeout(() => {
      isOpen.value = false;
    }, 3000);
  }

  return { isOpen, message, color, openSnackbar };
});
