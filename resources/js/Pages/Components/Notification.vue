<script setup>
import { ref, watch } from "vue";
import { CheckCircleIcon } from "@heroicons/vue/24/outline";
import { XMarkIcon } from "@heroicons/vue/20/solid";

const props = defineProps(["data", "show"]);
const emits = defineEmits(["approve", "ignore"]);

watch(
    () => props.show,
    (value) => {
        if (value) {
            setTimeout(() => {
                props.show = false;
            }, 10000);
        }
    }
);

const approve = (peerId) => {
    emits("approve", peerId);
    props.show = false;
};

const ignore = (peerId) => {
    emits("ignore", peerId);
    props.show = false;
};
</script>

<template>
    <!-- Global notification live region, render this permanently at the end of the document -->
    <div
        aria-live="assertive"
        class="pointer-events-none fixed inset-0 flex items-end px-4 py-6 sm:items-start sm:p-6 z-50"
    >
        <div class="flex w-full flex-col items-center space-y-4 sm:items-end">
            <!-- Notification panel, dynamically insert this into the live region when it needs to be displayed -->
            <transition
                enter-active-class="transform ease-out duration-300 transition"
                enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
                leave-active-class="transition ease-in duration-100"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="show"
                    class="pointer-events-auto flex w-full max-w-md rounded-lg bg-gray-800 shadow-lg border border-gray-600 ring-1 ring-black ring-opacity-5"
                >
                    <div class="w-0 flex-1 p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 pt-0.5">
                                <img
                                    class="h-10 w-10 rounded-full"
                                    src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2.2&w=160&h=160&q=80"
                                    alt=""
                                />
                            </div>
                            <div class="ml-3 w-0 flex-1">
                                <p class="text-sm font-medium text-gray-100">
                                    {{ data.message }}
                                </p>
                                <p class="mt-1 text-sm text-gray-400">
                                    {{ data.description }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="flex border-l border-gray-700">
                        <div class="flex flex-col divide-y divide-gray-600">
                            <div class="flex h-0 flex-1">
                                <button
                                    type="button"
                                    @click="approve(data.peerId)"
                                    class="flex w-full items-center justify-center rounded-none rounded-tr-lg border border-transparent px-4 py-3 text-sm font-medium antialiased text-sky-500 hover:text-indigo-500 focus:z-10 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                                    Approve
                                </button>
                            </div>
                            <div class="flex h-0 flex-1">
                                <button
                                    type="button"
                                    @click="ignore(data.peerId)"
                                    class="flex w-full items-center justify-center rounded-none rounded-br-lg border border-transparent px-4 py-3 text-sm font-medium text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                                    Ignore
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </div>
</template>
