<script setup>
import { ref } from "vue";
import { EllipsisVerticalIcon } from "@heroicons/vue/24/outline";

// TODO : pass in prop to indicate self user, if is_self, don't show menu options
const props = defineProps({
    message: Object,
    host: Boolean,
});

const emits = defineEmits([
    "kickUser",
    "banUser",
    "deleteMessage",
    "reportUser",
]);

const messageMenu = ref(false);

const openMessageMenu = () => {
    messageMenu.value = !messageMenu.value;
};

//watch for user to click outside of menu and then close it
const closeMessageMenu = () => {
    messageMenu.value = false;
};
</script>

<template>
    <div class="flex w-full group hover:bg-gray-800 px-2 py-2">
        <img
            :src="'/storage/' + message.userData.avatar"
            class="w-12 h-12 rounded-full mr-4 border-2 border-sky-700"
        />
        <div class="flex-1">
            <div class="text-gray-300 font-medium mb-1 text-sm">
                {{ message.userData.name }}
            </div>
            <div
                class="text-gray-100 mb-1 font-medium"
                :class="{
                    'italic text-lg font-semibold text-sky-500': message.tip,
                }"
            >
                {{ message.text }}
            </div>
        </div>
        <div
            v-if="host"
            class="relative items-center ml-auto px-6 hidden group-hover:flex cursor-pointer"
            @click="openMessageMenu"
        >
            <EllipsisVerticalIcon
                class="w-6 h-6 text-gray-300 cursor-pointer"
            />
            <div
                class="absolute top-0 right-0 mt-12 w-48 bg-gray-900 border border-gray-700 rounded-lg shadow-lg z-10 p-2"
                v-if="messageMenu"
                @mouseleave="closeMessageMenu"
            >
                <div class="py-1">
                    <a
                        href="#"
                        class="block px-4 py-2 text-sm text-gray-300 hover:bg-sky-900 hover:text-white"
                        @click="emits('deleteMessage', message.id)"
                    >
                        Delete
                    </a>
                    <a
                        href="#"
                        class="block px-4 py-2 text-sm text-gray-300 hover:bg-sky-900 hover:text-white"
                        @click="emits('reportMessage', message.id, message.text, message.userData.name)"
                    >
                        Report
                    </a>
                    <a
                        href="#"
                        class="block px-4 py-2 text-sm text-gray-300 hover:bg-sky-900 hover:text-white"
                        @click="emits('kickUser', message.userData.name)"
                    >
                        Kick
                    </a>
                    <a
                        href="#"
                        class="block px-4 py-2 text-sm text-gray-300 hover:bg-sky-900 hover:text-white"
                        @click="emits('banUser', message.userData.name)"
                    >
                        Ban
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>
