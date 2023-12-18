<script setup>
import {
    ref,
    defineProps,
    defineEmits,
    watchEffect,
    reactive,
    watch,
} from "vue";
import {
    Dialog,
    DialogPanel,
    DialogTitle,
    TransitionChild,
    TransitionRoot,
} from "@headlessui/vue";
import { XMarkIcon } from "@heroicons/vue/24/outline";

const props = defineProps(["audioDevices", "videoDevices", "open"]);
const emits = defineEmits(["close", "newAudioDevice", "newVideoDevice"]);
const open = ref(false);
const selectedAudioDevice = ref(
    props.audioDevices.length > 0 ? props.audioDevices[0].deviceId : null
);
const selectedVideoDevice = ref(
    props.videoDevices.length > 0 ? props.videoDevices[0].deviceId : null
);

watchEffect(() => {
    open.value = props.open;
});

watch(selectedAudioDevice, (value) => {
    emits("newAudioDevice", value);
});

watch(selectedVideoDevice, (value) => {
    emits("newVideoDevice", value);
});
const closeModal = () => {
    open.value = false;
    emits("close");
};
</script>

<template>
    <TransitionRoot as="template" :show="open">
        <Dialog as="div" class="relative z-10" @close="closeModal">
            <TransitionChild
                as="template"
                enter="ease-out duration-300"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="ease-in duration-200"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 bg-gray-900/75 transition-opacity" />
            </TransitionChild>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div
                    class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0"
                >
                    <TransitionChild
                        as="template"
                        enter="ease-out duration-300"
                        enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        enter-to="opacity-100 translate-y-0 sm:scale-100"
                        leave="ease-in duration-200"
                        leave-from="opacity-100 translate-y-0 sm:scale-100"
                        leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    >
                        <DialogPanel
                            class="border border-gray-700 relative transform overflow-hidden rounded-lg bg-gray-800 px-4 pb-4 pt-5 text-left transition-all sm:my-8 sm:w-full sm:max-w-sm sm:p-6"
                        >
                            <div class="flex flex-col">
                                <div
                                    class="text-center flex justify-between items-center"
                                >
                                    <DialogTitle
                                        class="text-2xl leading-6 font-medium text-gray-100"
                                    >
                                        Choose Devices
                                    </DialogTitle>
                                    <button
                                        @click="closeModal"
                                        class="text-gray-300 hover:text-gray-100"
                                    >
                                        <XMarkIcon class="w-6 h-6" />
                                    </button>
                                </div>
                            </div>
                            <div class="flex flex-col gap-4 pt-4">
                                <div>
                                    <label
                                        for="audioDevices"
                                        class="block font-medium leading-6 text-gray-100"
                                        >Audio Device</label
                                    >
                                    <select
                                        id="audioDevices"
                                        name="audioDevices"
                                        v-model="selectedAudioDevice"
                                        class="bg-gray-800 mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-100 ring-1 ring-inset ring-gray-700 focus:ring-2 focus:ring-sky-600 sm:text-sm sm:leading-6"
                                    >
                                        <option
                                            v-for="device in props.audioDevices"
                                            :key="device.deviceId"
                                            :value="device.deviceId"
                                        >
                                            {{ device.label }}
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label
                                        for="videoDevices"
                                        class="block font-medium leading-6 text-gray-100"
                                        >Video Device</label
                                    >
                                    <select
                                        id="videoDevices"
                                        name="videoDevices"
                                        v-model="selectedVideoDevice"
                                        class="bg-gray-800 mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-100 ring-1 ring-inset ring-gray-700 focus:ring-2 focus:ring-sky-600 sm:text-sm sm:leading-6"
                                    >
                                        <option
                                            v-for="device in props.videoDevices"
                                            :key="device.deviceId"
                                            :value="device.deviceId"
                                        >
                                            {{ device.label }}
                                        </option>
                                    </select>
                                </div>
                                <div class="flex justify-end">
                                    <button
                                        type="button"
                                        class="inline-flex justify-center px-4 py-2 font-medium text-gray-100 bg-sky-500 border border-transparent rounded-md hover:bg-sky-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-sky-500"
                                        @click="closeModal"
                                    >
                                        Save
                                    </button>
                                </div>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>
