<script setup>
const props = defineProps(['tipper']);

</script>

<template>
    <div class="flex gap-2 items-center">
        <img :src="tipper.user.avatar" class="w-10 h-10 rounded-full"/>
        <div class="text-lg font-medium text-gray-100">
            {{ tipper.user.name }} tipped you {{ tipper.amount }}
        </div>
    </div>
    <div class="flex gap-2 items-center">
        <button
            class="text-sm font-medium bg-gradient-to-br from-sky-500 to-sky-600 via-blue-500 hover:bg-sky-500 px-2 py-1 rounded-md border border-sky-500">
            Thanks
        </button>
        <button
            class="text-sm font-medium bg-gradient-to-br from-red-500 to-red-600 via-rose-600 hover:bg-red-500 px-2 py-1 rounded-md border border-red-600">
            Double
        </button>
    </div>
</template>
