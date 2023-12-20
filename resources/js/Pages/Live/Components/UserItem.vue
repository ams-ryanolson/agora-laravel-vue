<script setup>
import { ref } from "vue";
import { EllipsisVerticalIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    user: Object,
});

const userItemMenu = ref(false);

const closeUserItemMenu = () => {
    userItemMenu.value = !userItemMenu.value;
};
</script>

<template>
    <div class="flex w-full pb-3">
        <img
            :src="user.avatar"
            class="w-12 h-12 rounded-full mr-4 border-2 border-sky-700"
        />
        <div class="flex-1">
            <div class="text-gray-200 font-medium mb-1">
                {{ user.name }}
            </div>
            <div class="text-gray-400 mb-1 text-sm font-medium">
                @{{ user.name }}
            </div>
        </div>
        <div class="flex items-center ml-auto relative">
            <!-- Use ml-auto to push the dots to the right -->
            <EllipsisVerticalIcon
                class="w-6 h-6 text-gray-300 cursor-pointer"
                @click="closeUserItemMenu"
            />
            <div
                class="absolute top-3 right-6 w-48 bg-gray-900 border border-gray-700 rounded-lg shadow-lg z-10 p-2"
                v-if="userItemMenu"
                @mouseleave="closeUserItemMenu"
            >
                <div class="py-1">
                    <a
                        href="#"
                        class="block px-4 py-2 text-sm text-gray-300 hover:bg-sky-900 hover:text-white"
                    >
                        Kick
                    </a>
                    <a
                        href="#"
                        class="block px-4 py-2 text-sm text-gray-300 hover:bg-sky-900 hover:text-white"
                    >
                        Ban
                    </a>
                    <a
                        href="#"
                        class="block px-4 py-2 text-sm text-gray-300 hover:bg-sky-900 hover:text-white"
                    >
                        Mute
                    </a>
                    <a
                        href="#"
                        class="block px-4 py-2 text-sm text-gray-300 hover:bg-sky-900 hover:text-white"
                    >
                        Invite
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>
