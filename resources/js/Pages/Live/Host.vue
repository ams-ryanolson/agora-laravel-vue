<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from "vue";
import { usePage, Head, router } from "@inertiajs/vue3";
import { TransitionRoot, TransitionChild } from "@headlessui/vue";
import { Bars2Icon } from "@heroicons/vue/24/solid";
import HostChat from "./Components/Host/HostChat.vue";
import UserList from "./Components/UserList.vue";
import AdminPanel from "./Components/AdminPanel.vue";
import HostBroadcast from "./Components/Host/HostBroadcast.vue";
import TipUser from "./Components/TipUser.vue";
import JoinRequest from "../Components/JoinRequest.vue";
import HostStatsBar from "./Components/Host/HostStatsBar.vue";

const page = usePage();
const rightSidebar = ref("chat");
const channelCount = ref(0);
const channelViews = ref(0);
const channelId = page.props.channelId;
const rtmChat = ref();
const joinData = ref(null);
const inviteToStage = ref(false);
const pageLoading = ref(true);

const broadcasterUIDs = ref([]);
const broadcasters = ref([]);
const viewers = ref([]);

const rightSidebarTop = [
    {
        id: 1,
        title: "Chat",
        icon: "ChatAlt2Icon",
        visible: true,
    },
    {
        id: 2,
        title: "Users",
        icon: "UsersIcon",
        visible: true,
    },
    {
        id: 3,
        title: "Admin",
        icon: "CogIcon",
        visible: true,
    },
];

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

const setPageLoading = () => {
    setTimeout(() => {
        pageLoading.value = false;
    }, 5000); // 5000 milliseconds = 5 seconds
};

onMounted(() => {
    scrollToBottom();
    setPageLoading();
});

const updateChannelCount = (count) => {
    channelCount.value = count;
    channelViews.value = channelViews.value + 1;
};

const updateChatMembers = (chatList) => {
    viewers.value = [...chatList.value];
    copyBroadcasters();
};

const broadcastersUpdate = (blist) => {
    broadcasterUIDs.value = [...blist.value];
    copyBroadcasters();
};

const copyBroadcasters = () => {
    let tmp = [];
    broadcasterUIDs.value.forEach((uid) => {
        const copy = viewers.value.findIndex((element) => element["id"] == uid);
        if (copy !== -1) {
            tmp.push(viewers.value[copy]);
        }
    });
    broadcasters.value = [...tmp];
};

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
    <TransitionRoot
        :show="pageLoading"
        leave="transition fade-out duration-200"
        leave-from="opacity-100"
        leave-to="opacity-0"
        class="absolute left-0 top-0 w-full h-full bg-gray-950 z-50"
    >
        <div
            class="flex flex-row justify-center items-center my-auto h-full animate-pulse"
        >
            <div class="text-3xl text-white font-logo">REALKINK.</div>
            <div class="text-3xl text-sky-500 font-logo">MEN</div>
        </div>
    </TransitionRoot>
    <div
        class="grid xs:grid-cols-6 md:grid-cols-9 border-r border-l border-gray-900 xs:min-h-screen-app md:h-screen max-w-[1600px] mx-auto bg-gray-950 overflow-y-hidden"
    >
        <!-- MAIN CONTENT -->
        <div
            class="relative col-span-6 border-r border-gray-800 h-screen p-3 flex flex-col gap-4"
        >
            <HostBroadcast
                @broadcastersUpdate="broadcastersUpdate"
                @endStream="deleteStream"
            />
            <div class="flex flex-col w-full bg-gray-900 rounded-lg">
                <HostStatsBar />
            </div>
            <div
                class="w-full h-60 rounded-lg text-2xl font-bold hidden md:flex justify-center items-center flex-row gap-4 shadow-md shadow-black"
            >
                <div
                    class="w-full h-60 rounded-lg flex flex-col p-4 bg-gray-900 gap-2"
                >
                    <div class="text-white">Tip Goals go here</div>
                </div>

                <div
                    id="messagesContainer"
                    class="w-full h-60 rounded-lg flex flex-col p-4 gap-2 bg-gray-900 overflow-y-auto no-scrollbar"
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
        <div class="hidden md:block col-span-3 h-screen p-3">
            <div
                class="w-full h-full bg-gray-900 rounded-lg flex flex-col shadow-md shadow-black"
            >
                <div class="w-full flex justify-between items-center px-4 pt-3">
                    <div
                        v-for="item in rightSidebarTop"
                        :key="item.id"
                        class="w-full cursor-pointer font-medium text-gray-50 antialiased flex justify-center items-center text-center flex-1"
                        :class="[
                            {
                                'border-b-2 border-sky-600':
                                    rightSidebar === item.title.toLowerCase(),
                            },
                            'border-b-2 border-gray-700 hover:border-sky-600',
                        ]"
                        @click="rightSidebar = item.title.toLowerCase()"
                    >
                        {{ item.title }}
                    </div>
                </div>
                <div
                    class="relative h-full border border-gray-900 rounded-b-md mx-4 mb-4 pt-4 overflow-y-auto no-scrollbar"
                >
                    <HostChat
                        :channelId="channelId"
                        :visible="rightSidebar"
                        @channelCount="updateChannelCount"
                        @sysMessage="sysMessage"
                        @chatMembersUpdate="updateChatMembers"
                        ref="rtmChat"
                    />
                    <UserList
                        :channelCount="channelCount"
                        :broadcasters="broadcasters"
                        :viewers="viewers"
                        :channelId="channelId"
                        :visible="rightSidebar"
                    />
                    <AdminPanel :visible="rightSidebar" />
                </div>
            </div>
        </div>
    </div>
</template>
