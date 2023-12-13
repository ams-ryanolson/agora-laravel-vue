<script setup>
const props = defineProps(["visible","broadcasters","channelCount"]);
import UserItem from "./UserItem.vue";

const viewers = [{}];
</script>

<template>
    <div
        class="px-4 flex flex-col gap-4"
        :class="{ block: visible === 'users', hidden: visible !== 'users' }"
    >
        <div class="">
            <div class="flex flex-row justify-between items-center">
                <div class="text-xl font-semibold text-gray-100">
                    Broadcasters
                </div>
                <div class="font-medium text-lg text-gray-100">Count</div>
            </div>
            <div>
                <div
                    id="users"
                    v-for="user in props.broadcasters"
                    :key="user.id"
                    class="w-full flex items-center mb-4 mx-4"
                >
                    <UserItem :user="user" />
                </div>

            </div>
        </div>
        <div class="">
            <div class="flex flex-row justify-between items-center">
                <div class="text-xl font-semibold text-gray-100">
                    Audience
                </div>
                <div class="font-medium text-lg text-gray-100">{{props.channelCount}}</div>
            </div>
            <div>PUT VIEWERS HERE</div>
        </div>
    </div>
</template>
