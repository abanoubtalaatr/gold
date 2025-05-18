<template>
    <TransitionRoot appear :show="show" as="template">
        <Dialog
            as="div"
            @close="$emit('close')"
            class="relative z-50"
            :open="show"
        >
            <div class="fixed inset-0 overflow-y-auto">
                <div
                    class="flex min-h-full items-center justify-center p-4 text-center"
                >
                    <!-- Backdrop -->
                    <TransitionChild
                        as="template"
                        enter="ease-out duration-300"
                        enter-from="opacity-0"
                        enter-to="opacity-100"
                        leave="ease-in duration-200"
                        leave-from="opacity-100"
                        leave-to="opacity-0"
                    >
                        <DialogOverlay class="fixed inset-0 bg-black/25" />
                    </TransitionChild>

                    <!-- Modal Content -->
                    <TransitionChild
                        as="template"
                        enter="ease-out duration-300"
                        enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100"
                        leave="ease-in duration-200"
                        leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95"
                    >
                        <DialogPanel
                            class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 text-right align-middle shadow-xl transition-all"
                        >
                            <div class="mt-2">
                                <slot />
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script setup>
import {
    Dialog,
    DialogPanel,
    DialogOverlay,
    TransitionChild,
    TransitionRoot,
} from "@headlessui/vue";

defineProps({
    show: {
        type: Boolean,
        required: true,
    },
});

defineEmits(["close"]);
</script>

<style scoped>
:deep(.modal-content) {
    @apply relative mx-auto;
}

:deep(.modal-overlay) {
    @apply fixed inset-0;
}
</style>
