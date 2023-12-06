<script setup>
import { computed, onMounted, ref } from "vue";
import { usePage, Head } from "@inertiajs/vue3";
import { CogIcon } from "@heroicons/vue/24/outline";
import StartHostModal from "./Components/StartHostModal.vue";

const page = usePage();
const rightSidebar = ref("chat");
const channelCount = ref(0);
const channelViews = ref(0);
const streamTime = ref("0:00");
const streamTips = ref("$0");
const streamLikes = ref(0);
const channelId = page.props.channelId;

const scrollToBottom = () => {
    const container = document.getElementById("messagesContainer");
    container.scrollTop = container.scrollHeight;
};

onMounted(() => {
    scrollToBottom();
});

const shouldShowItem = (item) => {
    if (item.mobile) {
        return true;
    }
    if (window.innerWidth > 768) {
        return true;
    }
    return false;
};

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

//create a computed to create filteredStreamStats
const filteredStreamStats = computed(() => {
    return streamStats.value.filter((item) => {
        return shouldShowItem(item);
    });
});
</script>

<template>
    <!--suppress HtmlRequiredTitleElement -->
    <Head title="Live" />
    <StartHostModal :open="true" />
    <div
        class="grid xs:grid-cols-6 md:grid-cols-9 border-r border-l border-gray-800 xs:min-h-screen-app md:h-screen max-w-[1600px] mx-auto"
    >
        <!-- MAIN CONTENT -->
        <div
            class="relative col-span-6 border-r border-gray-800 h-screen p-3 flex flex-col gap-4"
        >
            <div
                class="relative w-full h-full bg-gray-800 rounded-lg text-2xl font-bold flex justify-center items-center shadow-md shadow-black"
            >
                <div class="w-full h-full rounded-lg" id="local-player"></div>
                <div class="absolute top-4 left-4 font-logo flex flex-row">
                    <div class="text-white">REALADULT.</div>
                    <div class="text-sky-500">FANS</div>
                </div>
                <div
                    class="w-full h-12 flex flex-row justify-between items-center absolute bottom-0 rounded-b-lg bg-gray-900/50"
                >
                    <div class="flex flex-row gap-6 px-4 items-center">
                        <!-- Mute Audio -->

                        <CogIcon class="w-6 h-6 text-gray-500 cursor-pointer" />
                    </div>
                    <div class="flex flex-row gap-6 px-2 items-center">
                        <button
                            class="text-sm font-medium text-gray-100 bg-gray-500 hover:bg-gray-600 px-5 py-1 rounded-lg"
                        >
                            Go Live
                        </button>
                    </div>
                </div>
            </div>

            <div
                class="w-full xs:h-36 md:h-48 bg-gray-800 rounded-lg text-2xl font-bold grid xs:grid-cols-3 md:grid-cols-5 gap-2 items-center px-6 shadow-md shadow-black"
            >
                <div
                    v-for="item in filteredStreamStats"
                    :key="item.id"
                    class=""
                >
                    <div
                        class="flex flex-col bg-gray-900 shadow-md shadow-black rounded-lg p-4 w-full transform transition-transform hover:translate-y-1"
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
            <div
                class="w-full h-60 rounded-lg text-2xl font-bold xs:hidden md:flex justify-center items-center flex-row gap-4 shadow-md shadow-black"
            >
                <div
                    class="w-full h-60 rounded-lg flex flex-col p-4 bg-gray-800 gap-2"
                >
                    <!-- This is where the stream info belongs -->
                </div>

                <div
                    id="messagesContainer"
                    class="w-full h-60 rounded-lg flex flex-col p-4 gap-2 bg-gray-800 overflow-y-auto no-scrollbar"
                >
                    <!-- This is where the Tip List belongs -->
                </div>
            </div>
        </div>
        <!-- SIDE BAR CHAT -->
        <div class="xs:hidden md:block col-span-3 h-screen p-3">
            <div
                class="w-full h-full bg-gray-800 rounded-lg flex flex-col shadow-md shadow-black"
            >
                <div class="w-full flex justify-between items-center px-4 pt-3">
                    <div
                        class="cursor-pointer text-xl font-medium text-gray-100 flex justify-center items-center text-center flex-1 rounded-t-xl shadow-lg border border-gray-700 hover:bg-sky-600"
                        :class="[
                            { 'bg-sky-500': rightSidebar === 'chat' },
                            'bg-gray-600',
                        ]"
                    >
                        Chat
                    </div>
                    <div
                        class="cursor-pointer text-xl font-medium text-gray-100 flex justify-center items-center text-center flex-1 rounded-t-xl shadow-lg border border-gray-700 hover:bg-sky-600"
                        :class="[
                            { 'bg-sky-500': rightSidebar === 'users' },
                            'bg-gray-600',
                        ]"
                    >
                        Users
                    </div>
                    <div
                        class="cursor-pointer text-xl font-medium text-gray-100 flex justify-center items-center text-center flex-1 rounded-t-xl shadow-lg border border-gray-700 hover:bg-sky-600"
                        :class="[
                            { 'bg-sky-500': rightSidebar === 'admin' },
                            'bg-gray-600',
                        ]"
                    >
                        Admin
                    </div>
                </div>
                <div
                    class="relative h-full border border-gray-700 rounded-b-md mx-4 mb-4 pt-4"
                ></div>
            </div>
        </div>
    </div>
</template>
