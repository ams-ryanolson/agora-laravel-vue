<script setup>
import { EllipsisVerticalIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    user: Object,
});
</script>

<template>
    <img
        :src="user.avatar"
        class="w-14 h-14 rounded-full mr-4 border-4 border-sky-700"
    />
    <div class="flex-1">
        <div class="text-gray-200 font-medium mb-1 text-lg">
            {{ user.name }}
        </div>
        <div class="text-gray-400 mb-1 text-xs font-medium">
            @{{ user.name }}
        </div>
    </div>
    <div class="flex items-center ml-auto px-6">
        <!-- Use ml-auto to push the dots to the right -->
        <EllipsisVerticalIcon class="w-6 h-6 text-gray-300 cursor-pointer" />
    </div>
</template>
