<script setup>
import { router, usePage } from "@inertiajs/vue3";
import { computed, onMounted, ref, watch } from "vue";
import AgoraRTC from "agora-rtc-sdk-ng";
import {
    CogIcon,
    ChatBubbleLeftRightIcon,
    EllipsisVerticalIcon,
} from "@heroicons/vue/24/outline";
import ChooseDevices from "./ChooseDevices.vue";
import { Menu, MenuButton, MenuItems, MenuItem } from "@headlessui/vue";

const page = usePage();
const client = AgoraRTC.createClient({
    mode: "live",
    codec: "vp9",
    role: "host",
});
const localTracks = ref([]);
const uid = ref(0);
const appId = ref(page.props.appId);
const token = ref(page.props.rtcToken);
const channel = ref(page.props.channelId);
const isVideoMuted = ref(false);
const isAudioMuted = ref(false);
const isBroadcasting = ref(false);
const showPulse = ref(false);
const localVideoDevices = ref([]);
const localAudioDevices = ref([]);
const openChooseDevices = ref(false);
const selectedAudioDevice = ref("");
const selectedVideoDevice = ref("");
const mobileChatOverlay = ref(false);
const emit = defineEmits(["endStream"]);

const testChatMessages = [
    {
        id: 1,
        username: "AlphaApollo",
        message: "Sup guys, welcome to the stream!",
    },
    {
        id: 2,
        username: "HumanATM",
        message: "Please Alpha, drain me of all my cash!",
    },
    {
        id: 3,
        username: "SillyPiggy34",
        message: "Holy shit! Video Streaming?!?!",
    },
    {
        id: 4,
        username: "PapaSub",
        message: "I cannot wait to do my weekly shows on here",
    },
    {
        id: 5,
        username: "HumanATM",
        message: "Please submit a withdrawl request Alpha!",
    },
];

const messageNavigation = [
    {
        id: 1,
        name: "Delete Message",
        href: "#",
    },
    {
        id: 2,
        name: "Kick User",
        href: "#",
    },
    {
        id: 3,
        name: "Invite to Stage",
        href: "#",
    },
];

const reverseMessages = computed(() => [...testChatMessages].reverse());

watch(selectedAudioDevice, async () => {
    localTracks.value[0].setDevice(selectedAudioDevice._value);
    //await unpublish();
    //await publish();
});

watch(selectedVideoDevice, async () => {

    localTracks.value[1].setDevice(selectedVideoDevice._value);

    //await stopLocalTrack();
    //await unpublish();
    //await playLocalTrack();
    //await publish();
});

const closeChooseDevices = () => {
    openChooseDevices.value = false;
};

const getDevices = async () => {
    localVideoDevices.value = [];
    localAudioDevices.value = [];
    const devices = await AgoraRTC.getDevices();
    devices.forEach((device) => {
        if (device.kind === "videoinput") {
            localVideoDevices.value.push(device);
        }
        if (device.kind === "audioinput") {
            localAudioDevices.value.push(device);
        }
    });
     selectedAudioDevice.value = localAudioDevices.value[0].deviceId;
    selectedVideoDevice.value = localVideoDevices.value[0].deviceId;
};

const join = async () => {
    console.log("joining");
    console.log(
        appId.value,
        channel.value,
        token.value,
        page.props.auth.user.id
    );
    await client.join(
        appId.value,
        channel.value,
        token.value,
        page.props.auth.user.id
    );
};

const leave = async () => {
    //confirm before leaving
    alert("Are you sure you want to end the live stream?");
    for (let i = 0; i < localTracks.value.length; i++) {
        localTracks.value[i].stop();
    }
    await client.leave();
    isBroadcasting.value = false;
    //redirect to /live
    router.visit("/live");
};

/*

        localTracks.value = await AgoraRTC.createMicrophoneAndCameraTracks(
        {
            microphoneId: selectedAudioDevice.id,
        },
        {
            cameraId: selectedVideoDevice.id,
            encoderConfig: "720p_auto",
        }
*/

const accessDevices  = async () => {
        localTracks.value = await AgoraRTC.createMicrophoneAndCameraTracks(
        {
        },
        {
            encoderConfig: "720p_auto",
        }
    );
}
const publish = async () => {
    await client.publish(localTracks.value);
};

const unpublish = async () => {
    await client.unpublish();
};

const goLive = async () => {
    await join();
    await publish();
   // await playLocalTrack();
    isBroadcasting.value = true;
    showPulse.value = true;
};

const endLive = async () => {
    await unpublish();
    await leave();
};

const muteVideo = async () => {
    await localTracks.value[1].setEnabled(false);
    isVideoMuted.value = true;
};

const unmuteVideo = async () => {
    await localTracks.value[1].setEnabled(true);
    isVideoMuted.value = false;
};

const muteAudio = async () => {
    await localTracks.value[0].setEnabled(false);
    isAudioMuted.value = true;
};

const unmuteAudio = async () => {
    await localTracks.value[0].setEnabled(true);
    isAudioMuted.value = false;
};

const playLocalTrack = async () => {
    await localTracks.value[1].play("local-player");
};

const stopLocalTrack = async () => {
    await localTracks.value[1].stop();
};

//mounted getDevices
onMounted(async () => {
    // access cam and mic
    await accessDevices();
    await playLocalTrack();
    await getDevices();
    await console.log("App ID: " + appId.value);
 
});
</script>

<!--<script setup>-->
<!--import {usePage} from '@inertiajs/vue3'-->
<!--import {onBeforeUnmount, onMounted, ref} from 'vue'-->
<!--import AgoraRTC from 'agora-rtc-sdk-ng'-->
<!--import {CogIcon} from "@heroicons/vue/24/outline";-->
<!--import ChooseDevices from "./ChooseDevices.vue";-->

<!--const page = usePage()-->
<!--const client = AgoraRTC.createClient({mode: 'live', codec: 'vp9', role: 'host'})-->
<!--const localTracks = ref([])-->
<!--const uid = ref(0)-->
<!--const appId = page.props.appId-->
<!--const token = ref(page.props.agoraToken)-->
<!--const channel = page.props.channelId-->
<!--const isVideoMuted = ref(false)-->
<!--const isAudioMuted = ref(false)-->
<!--const isBroadcasting = ref(false)-->
<!--const showPulse = ref(false)-->
<!--const localVideoDevices = ref([])-->
<!--const localAudioDevices = ref([])-->
<!--const openChooseDevices = ref(false)-->
<!--const selectedAudioDevice = ref('')-->
<!--const selectedVideoDevice = ref('')-->

<!--const emit = defineEmits(['endStream'])-->

<!--const join = async () => {-->
<!--    await client.join(appId.value, page.props.channelId, token.value, page.props.auth.user.id)-->
<!--    localTracks.value = await AgoraRTC.createMicrophoneAndCameraTracks({microphoneId: selectedAudioDevice.id}, {-->
<!--        cameraId: selectedVideoDevice.id,-->
<!--        encoderConfig: '720p_auto'-->
<!--    })-->
<!--    await client.publish(localTracks.value)-->
<!--}-->

<!--const leave = async () => {-->
<!--    //confirm before leaving-->
<!--    alert('Are you sure you want to end the live stream?')-->
<!--    for (let i = 0; i < localTracks.value.length; i++) {-->
<!--        localTracks.value[i].stop()-->
<!--    }-->
<!--    await client.leave()-->
<!--    isBroadcasting.value = false-->
<!--    emit('endStream')-->
<!--}-->

<!--const unpublish = async () => {-->
<!--    await client.unpublish()-->
<!--}-->

<!--const publish = async () => {-->
<!--    localTracks.value = await AgoraRTC.createMicrophoneAndCameraTracks({microphoneId: selectedAudioDevice.id}, {-->
<!--        cameraId: selectedVideoDevice.id,-->
<!--        encoderConfig: '720p_auto'-->
<!--    })-->
<!--    await client.publish(localTracks.value)-->
<!--}-->

<!--const hostLive = async () => {-->
<!--    await join()-->
<!--    await playLocalTrack()-->
<!--    isBroadcasting.value = true-->
<!--    showPulse.value = true-->
<!--}-->

<!--const muteVideo = async () => {-->
<!--    await localTracks.value[1].setEnabled(false)-->
<!--    isVideoMuted.value = true-->
<!--}-->

<!--const unmuteVideo = async () => {-->
<!--    await localTracks.value[1].setEnabled(true)-->
<!--    isVideoMuted.value = false-->
<!--}-->

<!--const muteAudio = async () => {-->
<!--    await localTracks.value[0].setEnabled(false)-->
<!--    isAudioMuted.value = true-->
<!--}-->

<!--const unmuteAudio = async () => {-->
<!--    await localTracks.value[0].setEnabled(true)-->
<!--    isAudioMuted.value = false-->
<!--}-->

<!--const playLocalTrack = async () => {-->
<!--    await localTracks.value[1].play('local-player')-->
<!--}-->

<!--const getDevices = async () => {-->
<!--    localVideoDevices.value = []-->
<!--    localAudioDevices.value = []-->
<!--    const devices = await AgoraRTC.getDevices();-->
<!--    devices.forEach((device) => {-->
<!--        if (device.kind === 'videoinput') {-->
<!--            localVideoDevices.value.push(device);-->
<!--        }-->
<!--        if (device.kind === 'audioinput') {-->
<!--            localAudioDevices.value.push(device);-->
<!--        }-->
<!--    });-->
<!--    selectedAudioDevice.value = localAudioDevices.value[0].deviceId-->
<!--    selectedVideoDevice.value = localVideoDevices.value[0].deviceId-->
<!--};-->

<!--const changeDevices = async () => {-->
<!--    await unpublish()-->
<!--    await publish()-->
<!--    await playLocalTrack()-->
<!--    openChooseDevices.value = false-->
<!--}-->
<!--const closeChooseDevices = () => {-->
<!--    openChooseDevices.value = false-->
<!--}-->

<!--const selectMediaDevices = () => {-->
<!--    if (localAudioDevices.value.length > 1 || localVideoDevices.value.length > 1) {-->
<!--        openChooseDevices.value = true-->
<!--    }-->
<!--}-->

<!--onMounted( async () => {-->
<!--    await getDevices()-->
<!--    selectMediaDevices()-->
<!--})-->

<!--onBeforeUnmount(() => {-->
<!--    unpublish()-->
<!--    leave()-->
<!--})-->
<!--</script>-->

<template>
    <ChooseDevices
        v-if="openChooseDevices"
        :audioDevices="localAudioDevices"
        :videoDevices="localVideoDevices"
        :open="true"
        @close="closeChooseDevices"
        @newAudioDevice="selectedAudioDevice = $event"
        @newVideoDevice="selectedVideoDevice = $event"
    />
    <div
        class="relative w-full h-full bg-gray-800 rounded-lg text-2xl font-bold flex justify-center items-center shadow-md shadow-black"
    >
        <div class="w-full h-full rounded-lg relative" id="local-player">
            <div
                class="absolute w-full h-[calc(100%-96px)] z-20 flex flex-col-reverse gap-5 no-scrollbar overflow-y-scroll mt-12 px-4 bg-gradient-to-b from-transparent to-gray-900/80 via-gray-900/20"
                v-show="mobileChatOverlay"
            >
                <div
                    v-for="message in reverseMessages"
                    :key="message.id"
                    class="flex flex-row gap-1 w-full justify-between items-center"
                >
                    <div class="flex flex-col gap-1">
                        <div class="text-xs text-gray-300">
                            {{ message.username }}
                        </div>
                        <div class="text-sm font-light text-gray-100">
                            {{ message.message }}
                        </div>
                    </div>
                    <Menu as="div" class="relative">
                        <MenuButton class="flex justify-center items-center">
                            <span class="sr-only">Open message menu</span>
                            <EllipsisVerticalIcon
                                class="w-5 h-5 text-gray-300 cursor-pointer"
                            />
                        </MenuButton>
                        <transition
                            enter-active-class="transition ease-out duration-100"
                            enter-from-class="transform opacity-0 scale-95"
                            enter-to-class="transform opacity-100 scale-100"
                            leave-active-class="transition ease-in duration-75"
                            leave-from-class="transform opacity-100 scale-100"
                            leave-to-class="transform opacity-0 scale-95"
                        >
                            <MenuItems
                                class="z-50 absolute border border-gray-700 text-sm right-0 mt-2 w-40 origin-top-right bg-gray-900 divide-y divide-gray-600 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none overflow-auto"
                            >
                                <div class="px-1 py-2">
                                    <MenuItem
                                        as="div"
                                        v-for="item in messageNavigation"
                                        :key="item.id"
                                        v-slot="{ active }"
                                        class="px-2 py-1"
                                    >
                                        {{ item.name }}
                                    </MenuItem>
                                </div>
                            </MenuItems>
                        </transition>
                    </Menu>
                </div>
            </div>
        </div>
        <div
            class="absolute top-0 left-0 w-full xs:h-12 md:h-16 bg-gray-900/50"
        ></div>
        <a
            href="/live"
            class="absolute xs:top-3 md:top-4 left-4 font-logo flex flex-row xs:text-sm md:text-lg"
        >
            <div class="text-white">REALKINK.</div>
            <div class="text-sky-500">MEN</div>
        </a>
        <div class="absolute xs:top-2 md:top-4 right-4">
            <span
                v-if="isBroadcasting"
                class="rounded-2xl px-6 py-1.5 bg-red-600 font-medium text-white xs:text-sm md:text-lg"
                :class="{ 'animate-pulse': showPulse }"
                >LIVE</span
            >
        </div>
        <div
            class="w-full h-12 flex flex-row justify-between items-center absolute bottom-0 rounded-b-lg z-2"
            :class="{
                'bg-gray-900/80': mobileChatOverlay,
                'bg-gray-900/50': !mobileChatOverlay,
            }"
        >
            <div class="flex flex-row gap-6 px-4 items-center">
                <div>
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 576 512"
                        class="hover:fill-green-700 h-5"
                        :class="{
                            'fill-gray-600': !isBroadcasting,
                            'fill-green-600': isBroadcasting,
                        }"
                        v-if="!isVideoMuted"
                        @click="muteVideo"
                    >
                        <path
                            d="M0 128C0 92.7 28.7 64 64 64H320c35.3 0 64 28.7 64 64V384c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V128zM559.1 99.8c10.4 5.6 16.9 16.4 16.9 28.2V384c0 11.8-6.5 22.6-16.9 28.2s-23 5-32.9-1.6l-96-64L416 337.1V320 192 174.9l14.2-9.5 96-64c9.8-6.5 22.4-7.2 32.9-1.6z"
                        />
                    </svg>
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 640 512"
                        class="fill-red-600 h-5"
                        v-else
                        @click="unmuteVideo"
                    >
                        <path
                            d="M38.8 5.1C28.4-3.1 13.3-1.2 5.1 9.2S-1.2 34.7 9.2 42.9l592 464c10.4 8.2 25.5 6.3 33.7-4.1s6.3-25.5-4.1-33.7l-86.4-67.7 13.8 9.2c9.8 6.5 22.4 7.2 32.9 1.6s16.9-16.4 16.9-28.2V128c0-11.8-6.5-22.6-16.9-28.2s-23-5-32.9 1.6l-96 64L448 174.9V192 320v5.8l-32-25.1V128c0-35.3-28.7-64-64-64H113.9L38.8 5.1zM407 416.7L32.3 121.5c-.2 2.1-.3 4.3-.3 6.5V384c0 35.3 28.7 64 64 64H352c23.4 0 43.9-12.6 55-31.3z"
                        />
                    </svg>
                </div>
                <!-- Mute Audio -->
                <div>
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 640 512"
                        class="fill-red-600 h-5"
                        v-if="isAudioMuted"
                        @click="unmuteAudio"
                    >
                        <path
                            d="M38.8 5.1C28.4-3.1 13.3-1.2 5.1 9.2S-1.2 34.7 9.2 42.9l592 464c10.4 8.2 25.5 6.3 33.7-4.1s6.3-25.5-4.1-33.7L472.1 344.7c15.2-26 23.9-56.3 23.9-88.7V216c0-13.3-10.7-24-24-24s-24 10.7-24 24v40c0 21.2-5.1 41.1-14.2 58.7L416 300.8V96c0-53-43-96-96-96s-96 43-96 96v54.3L38.8 5.1zM344 430.4c20.4-2.8 39.7-9.1 57.3-18.2l-43.1-33.9C346.1 382 333.3 384 320 384c-70.7 0-128-57.3-128-128v-8.7L144.7 210c-.5 1.9-.7 3.9-.7 6v40c0 89.1 66.2 162.7 152 174.4V464H248c-13.3 0-24 10.7-24 24s10.7 24 24 24h72 72c13.3 0 24-10.7 24-24s-10.7-24-24-24H344V430.4z"
                        />
                    </svg>
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 384 512"
                        class="hover:fill-green-700 h-5"
                        :class="{
                            'fill-gray-600': !isBroadcasting,
                            'fill-green-600': isBroadcasting,
                        }"
                        v-else
                        @click="muteAudio"
                    >
                        <path
                            d="M192 0C139 0 96 43 96 96V256c0 53 43 96 96 96s96-43 96-96V96c0-53-43-96-96-96zM64 216c0-13.3-10.7-24-24-24s-24 10.7-24 24v40c0 89.1 66.2 162.7 152 174.4V464H120c-13.3 0-24 10.7-24 24s10.7 24 24 24h72 72c13.3 0 24-10.7 24-24s-10.7-24-24-24H216V430.4c85.8-11.7 152-85.3 152-174.4V216c0-13.3-10.7-24-24-24s-24 10.7-24 24v40c0 70.7-57.3 128-128 128s-128-57.3-128-128V216z"
                        />
                    </svg>
                </div>
                <CogIcon
                    @click="openChooseDevices = true"
                    class="w-6 h-6 text-gray-300 cursor-pointer"
                />
                <ChatBubbleLeftRightIcon
                    @click="mobileChatOverlay = !mobileChatOverlay"
                    :class="
                        mobileChatOverlay ? 'text-green-500' : 'text-red-500'
                    "
                    class="w-6 h-60 cursor-pointer xs:visible md:hidden"
                />
            </div>
            <div class="flex flex-row gap-6 px-2 items-center">
                <button
                    class="text-sm font-medium text-gray-100 bg-sky-500 hover:bg-sky-600 px-5 py-1 rounded-lg"
                    v-if="!isBroadcasting"
                    @click="goLive"
                >
                    Go Live
                </button>
                <button
                    class="text-sm font-medium text-gray-100 bg-red-500 hover:bg-red-600 px-5 py-1 rounded-lg"
                    v-else
                    @click="endLive"
                >
                    End Live
                </button>
            </div>
        </div>
    </div>
</template>

<!--suppress CssUnusedSymbol -->
<style>
.agora_video_player {
    object-fit: cover !important;
}
</style>
