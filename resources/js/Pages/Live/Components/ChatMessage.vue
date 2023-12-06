<script setup>
import {EllipsisVerticalIcon} from "@heroicons/vue/24/outline";

const props = defineProps({
    message: Object,
})
</script>

<template>
    <img :src="message.userData.avatar" class="w-14 h-14 rounded-full mr-4 border-4 border-sky-700" />
    <div class="flex-1">
        <div class="text-gray-300 font-medium mb-1 text-sm">
            {{ message.userData.name }}
        </div>
        <div class="text-gray-100 mb-1 text-xl font-medium"
             :class="{'italic text-3xl font-semibold text-sky-500': message.tip}">
            {{ message.text }}
        </div>
    </div>
    <div class="flex items-center ml-auto px-6"> <!-- Use ml-auto to push the dots to the right -->
        <EllipsisVerticalIcon class="w-6 h-6 text-gray-300 cursor-pointer"/>
    </div>
</template>
