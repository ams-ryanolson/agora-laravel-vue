<script setup>
const props = defineProps([
    "visible",
    "viewers",
    "broadcasters",
    "channelCount",
]);
import UserItem from "./UserItem.vue";
</script>

<template>
    <div
        class="px-4 flex flex-col gap-4"
        :class="{ block: visible === 'users', hidden: visible !== 'users' }"
    >
        <div class="">
            <div class="flex flex-row justify-between items-center">
                <div class="text-lg font-semibold text-gray-100">
                    Broadcasters
                </div>
            </div>
            <div class="pt-4">
                <div
                    id="users"
                    v-for="user in props.broadcasters"
                    :key="user.id"
                    class="w-full flex items-center mb-4"
                >
                    <UserItem :user="user" :isBroadcaster=true />
                </div>
            </div>
        </div>
        <div class="">
            <div class="flex flex-row justify-between items-center">
                <div
                    class="flex justify-between items-center w-full border-b border-gray-700 pb-2"
                >
                    <div class="text-lg font-semibold text-gray-100">
                        Viewers
                    </div>
                    <div class="text-lg text-gray-100 font-medium pr-2">
                        {{ props.channelCount }}
                    </div>
                </div>
            </div>
            <div class="pt-4">
                <div
                    id="users"
                    v-for="user in props.viewers"
                    :key="user.id"
                    class="w-full flex items-center"
                >
                    <UserItem :user="user" />
                </div>
            </div>
        </div>
    </div>
</template>
