<script setup>
import { ref, computed } from "vue";
import { Bars2Icon } from "@heroicons/vue/24/solid";
import { TransitionRoot, TransitionChild } from "@headlessui/vue";

const channelCount = ref(0);
const channelViews = ref(0);
const streamStartTime = Date();
const streamTime = ref("0:00");
const streamTips = ref("$0");
const streamLikes = ref(0);
const statsBar = ref(true);

const streamStats = ref([
    {
        id: 1,
        title: "Viewers",
        stat: channelCount.value,
    },
    {
        id: 2,
        title: "Tips",
        stat: streamTips.value,
    },
    {
        id: 3,
        title: "Stream Time",
        stat: streamTime.value,
    },
    {
        id: 4,
        title: "Likes",
        stat: streamLikes.value,
    },
    {
        id: 5,
        title: "Total Views",
        stat: channelViews.value,
    },
]);

const updateStreamTime = () => {
    const now = Date();
    const diff = Math.abs(new Date(now) - new Date(streamStartTime));
    const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
    const rawMinutes = Math.floor(diff / 1000 / 60);
    const rawSeconds = Math.floor((diff / 1000) % 60);

    const formatTimeComponent = (component) => {
        return component < 10 ? `0${component}` : component;
    };

    const minutes = formatTimeComponent(rawMinutes);
    const seconds = formatTimeComponent(rawSeconds);
    streamTime.value = `${hours}:${minutes}:${seconds}`;
};

//update the frontend every 5 seconds
setInterval(() => {
    updateStreamTime();
    streamStats.value = [
        {
            id: 1,
            title: "Viewers",
            stat: channelCount.value,
            mobile: true,
        },
        {
            id: 2,
            title: "Tips",
            stat: streamTips.value,
            mobile: true,
        },
        {
            id: 3,
            title: "Stream Time",
            stat: streamTime.value,
            mobile: false,
        },
        {
            id: 4,
            title: "Likes",
            stat: streamLikes.value,
            mobile: true,
        },
        {
            id: 5,
            title: "Total Views",
            stat: channelViews.value,
            mobile: false,
        },
    ];
}, 1000);

const shouldShowItem = (item) => {
    if (item.mobile) {
        return true;
    }
    return window.innerWidth > 768;
};

const filteredStreamStats = computed(() => {
    return streamStats.value.filter((item) => {
        return shouldShowItem(item);
    });
});
</script>

<template>
    <div
        class="flex w-full justify-center items-center xs:col-span-3 md:col-span-5"
        :class="{
            'my-2': !statsBar,
            'my-2': statsBar,
        }"
    >
        <Bars2Icon
            @click="statsBar = !statsBar"
            class="w-24 h-6 text-gray-100"
        />
    </div>
    <TransitionRoot
        :show="statsBar"
        enter="transition ease-in-out duration-300 transform"
        enter-from="-translate-y-10 opacity-0"
        enter-to="translate-y-0 opacity-100"
        leave="transition ease-in-out duration-300 transform"
        leave-from="translate-y-0 opacity-100"
        leave-to="-translate-y-10 opacity-0"
    >
        <div
            class="w-full bg-gray-900 rounded-lg text-2xl font-bold grid grid-cols-3 md:grid-cols-5 gap-2 items-center px-6 pb-4 shadow-md shadow-black"
        >
            <div v-for="item in filteredStreamStats" :key="item.id" class="">
                <div
                    class="flex flex-col justify-center items-center bg-gray-950 shadow-md shadow-black rounded-lg p-4 w-full transform transition-transform hover:translate-y-1"
                >
                    <div class="text-2xl font-medium text-gray-100">
                        {{ item.stat }}
                    </div>
                    <div class="text-lg font-medium text-gray-500">
                        {{ item.title }}
                    </div>
                </div>
            </div>
        </div>
    </TransitionRoot>
</template>
