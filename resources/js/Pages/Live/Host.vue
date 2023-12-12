<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from "vue";
import { usePage, Head, router } from "@inertiajs/vue3";
import { TransitionRoot, TransitionChild } from "@headlessui/vue";
import { Bars2Icon } from "@heroicons/vue/24/solid";
import HostChat from "./Components/HostChat.vue";
import UserList from "./Components/UserList.vue";
import AdminPanel from "./Components/AdminPanel.vue";
import HostBroadcast from "./Components/HostBroadcast.vue";
import TipUser from "./Components/TipUser.vue";
import JoinRequest from "../Components/JoinRequest.vue";

const page = usePage();
const rightSidebar = ref("chat");
const channelCount = ref(0);
const channelViews = ref(0);
const streamStartTime = Date();
const streamTime = ref("0:00");
const streamTips = ref("$0");
const streamLikes = ref(0);
const channelId = page.props.channelId;
const statsBar = ref(true);
// const messages = ref([]);
const rtmChat = ref();
const joinData = ref(null);
const inviteToStage = ref(false);

const sysMessage = (json, peerId) => {
    if (json.message == "permissionRequest") {
        joinData.value = {
            peerId: peerId,
            message: json.userData.name + " wants to join you on stage",
            description: "Would you like to let them join you?",
        };
        inviteToStage.value = true;
    }
};

const approveJoinRequest = (peerId) => {
    rtmChat.value.$.exposed.sendPermissionResponse(peerId, "OK");
    inviteToStage.value = false;
};

const ignoreJoinRequest = (peerId) => {
    rtmChat.value.$.exposed.sendPermissionResponse(peerId, "FAIL");
    inviteToStage.value = false;
};

const scrollToBottom = () => {
    const container = document.getElementById("messagesContainer");
    container.scrollTop = container.scrollHeight;
};

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

onMounted(() => {
    scrollToBottom();
});

const updateChannelCount = (count) => {
    channelCount.value = count;
    channelViews.value = channelViews.value + 1;
};

const shouldShowItem = (item) => {
    if (item.mobile) {
        return true;
    }
    return window.innerWidth > 768;
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

const deleteStream = () => {
    router.delete("/live", {
        data: {
            uuid: page.props.live.uuid,
        },
    });
};

onBeforeUnmount(() => {
    deleteStream();
});

const tipList = [
    {
        id: 1,
        amount: "$5",
        user: {
            id: 1,
            name: "PigletATM",
            avatar: "https://i.pravatar.cc/310",
        },
    },
    {
        id: 2,
        amount: "$0", // Set a default value
        user: {
            id: 1,
            name: "SlutBoy92",
            avatar: "https://i.pravatar.cc/311",
        },
    },
    {
        id: 3,
        amount: "$0", // Set a default value
        user: {
            id: 1,
            name: "AlphaTommy",
            avatar: "https://i.pravatar.cc/312",
        },
    },
    {
        id: 4, // Added a missing value
        amount: "$0", // Set a default value
        user: {
            id: 1,
            name: "PigboyATM",
            avatar: "https://i.pravatar.cc/313",
        },
    },
    {
        id: 5,
        amount: "$0", // Set a default value
        user: {
            id: 1,
            name: "BottomCumdump",
            avatar: "https://i.pravatar.cc/314",
        },
    },
    {
        id: 6,
        amount: "$5",
        user: {
            id: 1,
            name: "PigletATM",
            avatar: "https://i.pravatar.cc/315",
        },
    },
    {
        id: 7,
        amount: "$0", // Set a default value
        user: {
            id: 1,
            name: "SlutBoy92",
            avatar: "https://i.pravatar.cc/316",
        },
    },
    {
        id: 8,
        amount: "$0", // Set a default value
        user: {
            id: 1,
            name: "AlphaTommy",
            avatar: "https://i.pravatar.cc/317",
        },
    },
    {
        id: 9, // Added a missing value
        amount: "$0", // Set a default value
        user: {
            id: 1,
            name: "PigboyATM",
            avatar: "https://i.pravatar.cc/318",
        },
    },
    {
        id: 10,
        amount: "$0", // Set a default value
        user: {
            id: 1,
            name: "BottomCumdump",
            avatar: "https://i.pravatar.cc/319",
        },
    },
];
</script>

<template>
    <JoinRequest
        :data="joinData"
        :show="inviteToStage"
        @approve="approveJoinRequest"
        @ignore="ignoreJoinRequest"
    />
    <!--suppress HtmlRequiredTitleElement -->
    <Head title="Live" />
    <div
        class="grid xs:grid-cols-6 md:grid-cols-9 border-r border-l border-gray-800 xs:min-h-screen-app md:h-screen max-w-[1600px] mx-auto bg-gray-900"
    >
        <!-- MAIN CONTENT -->
        <div
            class="relative col-span-6 border-r border-gray-800 h-screen p-3 flex flex-col gap-4"
        >
            <HostBroadcast @endStream="deleteStream" />
            <div class="flex flex-col pt-3 w-full bg-gray-800 rounded-lg">
                <div
                    class="flex w-full justify-center items-center xs:col-span-3 md:col-span-5 pb-2"
                >
                    <Bars2Icon
                        @click="statsBar = !statsBar"
                        class="w-24 h-6 text-gray-100"
                    />
                </div>
                <TransitionRoot as="div" :show="statsBar">
                    <TransitionChild
                        as="div"
                        enter="ease-out duration-300"
                        enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        enter-to="opacity-100 translate-y-0 sm:scale-100"
                        leave="ease-in duration-200"
                        leave-from="opacity-100 translate-y-0 sm:scale-100"
                        leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="w-full bg-gray-800 rounded-lg text-2xl font-bold grid xs:grid-cols-3 md:grid-cols-5 gap-2 items-center px-6 pb-4 shadow-md shadow-black"
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
                    </TransitionChild>
                </TransitionRoot>
            </div>
            <div
                class="w-full h-60 rounded-lg text-2xl font-bold xs:hidden md:flex justify-center items-center flex-row gap-4 shadow-md shadow-black"
            >
                <div
                    class="w-full h-60 rounded-lg flex flex-col p-4 bg-gray-800 gap-2"
                >
                    <!-- Graph showing total users in chat per minute -->
                </div>

                <div
                    id="messagesContainer"
                    class="w-full h-60 rounded-lg flex flex-col p-4 gap-2 bg-gray-800 overflow-y-auto no-scrollbar"
                >
                    <div
                        class="text-white flex flex-row justify-between items-center gap-4"
                        v-for="item in tipList"
                        :key="item.id"
                    >
                        <TipUser :tipper="item" />
                    </div>
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
                        @click="rightSidebar = 'chat'"
                    >
                        Chat
                    </div>
                    <div
                        class="cursor-pointer text-xl font-medium text-gray-100 flex justify-center items-center text-center flex-1 rounded-t-xl shadow-lg border border-gray-700 hover:bg-sky-600"
                        :class="[
                            { 'bg-sky-500': rightSidebar === 'users' },
                            'bg-gray-600',
                        ]"
                        @click="rightSidebar = 'users'"
                    >
                        Users
                    </div>
                    <div
                        class="cursor-pointer text-xl font-medium text-gray-100 flex justify-center items-center text-center flex-1 rounded-t-xl shadow-lg border border-gray-700 hover:bg-sky-600"
                        :class="[
                            { 'bg-sky-500': rightSidebar === 'admin' },
                            'bg-gray-600',
                        ]"
                        @click="rightSidebar = 'admin'"
                    >
                        Admin
                    </div>
                </div>
                <div
                    class="relative h-full border border-gray-700 rounded-b-md mx-4 mb-4 pt-4 overflow-y-auto no-scrollbar"
                >
                    <HostChat
                        :channelId="channelId"
                        :visible="rightSidebar"
                        @channelCount="updateChannelCount"
                        @sysMessage="sysMessage"
                        ref="rtmChat"
                    />
                    <UserList :channelId="channelId" :visible="rightSidebar" />
                    <AdminPanel :visible="rightSidebar" />
                </div>
            </div>
        </div>
    </div>
</template>
