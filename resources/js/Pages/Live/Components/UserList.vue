<script setup>
const props = defineProps(["visible","viewers","broadcasters","channelCount"]);
import UserItem from "./UserItem.vue";

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
                    Viewers ({{props.channelCount}})
                </div>
                
            </div>
             <div
                    id="users"
                    v-for="user in props.viewers"
                    :key="user.id"
                    class="w-full flex items-center mb-4 mx-4"
                >
                    <UserItem :user="user" />
                </div>
        </div>
    </div>
</template>
