<script setup>
import { usePage, Link, Head } from "@inertiajs/vue3";
import { onMounted, onUnmounted, ref } from "vue";
import AgoraRTC from "agora-rtc-sdk-ng";
import ViewerChat from "./Components/ViewerChat.vue";
import TipModal from "./Components/TipModal.vue";

const page = usePage();

const client = AgoraRTC.createClient({
    mode: "live",
    codec: "vp9",
    role: "audience",
});

const uid = ref(null);
const channelId = ref(page.props.channelId);
const appId = ref(page.props.appId);
const token = ref(page.props.rtcToken);
const localTracks = {
    audioTrack: null,
    videoTrack: null,
};
const remoteUsers = ref({});
const isJoined = ref(false);
const openTipModal = ref(false);

//join as audience
const join = async () => {
    client.on("user-published", handleUserPublished);
    client.on("user-unpublished", handleUserUnpublished);
    await client.join(
        appId.value,
        channelId.value,
        token.value,
        page.props.auth.user.id
    );
};

//leave
//subscribe
const subscribe = async (user, mediaType) => {
    const uid = user.uid;
    await client.subscribe(user, mediaType);
    console.log("subscribe success");
    if (mediaType === "video") {
        user.videoTrack.play(`remote-player`);
    }
    if (mediaType === "audio") {
        user.audioTrack.play();
    }
};
const handleUserPublished = (user, mediaType) => {
    console.log('"User Published" event for remote users is triggered');
    subscribe(user, mediaType);
};

const handleUserUnpublished = (user, mediaType) => {
    console.log('"User Unpublished" event for remote users is triggered');
};

onMounted(async () => {
    await join();
});

const tipOptions = [
    {
        id: 1,
        amount: 5,
        label: "🍺",
        screenFlair: "",
    },
    {
        id: 2,
        amount: 10,
        label: "🍻",
        screenFlair: "",
    },
    {
        id: 3,
        amount: 20,
        label: "🍷",
        screenFlair: "",
    },
    {
        id: 4,
        amount: 50,
        label: "🍸",
        screenFlair: "",
    },
    {
        id: 5,
        amount: 100,
        label: "🍾",
        screenFlair: "",
    },
];
</script>

<template>
    <!--suppress HtmlRequiredTitleElement -->
    <Head title="Live" />

    <div
        class="grid xs:grid-cols-6 md:grid-cols-9 border-r border-gray-800 xs:min-h-screen-app md:h-screen"
    >
        <!-- MAIN CONTENT -->
        <div
            class="col-span-6 border-r border-gray-800 h-screen p-3 flex flex-col gap-4"
        >
            <div
                class="relative w-full h-full bg-gray-800 rounded-lg text-2xl font-bold flex justify-center items-center"
            >
                <div class="w-full h-full rounded-lg" id="remote-player"></div>
                <div
                    class="w-full h-12 flex flex-row justify-between items-center absolute bottom-0 rounded-b-lg bg-gray-900/50"
                ></div>
            </div>
            <div
                class="w-full h-36 bg-gray-800 rounded-lg text-2xl font-bold grid grid-cols-5 gap-2 items-center px-6 shadow-md shadow-black"
            >
                <div v-for="item in tipOptions" :key="item.id" class="">
                    <div
                        class="flex flex-col gap-2 bg-gray-900 shadow-md shadow-black rounded-lg p-4 w-full transform transition-transform hover:translate-y-1 cursor-pointer"
                    >
                        <div
                            class="text-2xl font-medium text-gray-100 justify-center items-center flex"
                        >
                            ${{ item.amount }}
                        </div>
                        <div
                            class="text-lg font-medium text-gray-500 justify-center items-center flex"
                        >
                            {{ item.label }}
                        </div>
                    </div>
                </div>
            </div>
            <div
                class="xs:hidden w-full h-72 bg-gray-800 rounded-lg text-2xl font-bold md:flex justify-center items-center"
            >
                <div class="flex gap-2"></div>
            </div>
        </div>
        <!-- SIDE BAR CHAT -->
        <div class="xs:hidden md:block col-span-3 h-screen p-3">
            <div
                class="w-full h-full bg-gray-800 rounded-lg flex flex-col shadow-md shadow-black"
            >
                <div
                    class="relative h-full border border-gray-700 rounded-md mx-4 my-4 pt-4"
                >
                    <ViewerChat :channelId="page.props.channelId" />
                </div>
            </div>
        </div>
    </div>
</template>

<style>
.agora_video_player {
    object-fit: contain !important;
}
</style>
