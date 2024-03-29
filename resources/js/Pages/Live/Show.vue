<script setup>
import { usePage, Link, Head } from "@inertiajs/vue3";
import { onMounted, onUnmounted, onBeforeUnmount, ref } from "vue";
import AgoraRTC from "agora-rtc-sdk-ng";
import ViewerChat from "./Components/ViewerChat.vue";
import TipModal from "./Components/TipModal.vue";
import ChooseDevices from "./Components/Host/ChooseDevices.vue";
import Notification from "@/Pages/Components/Notification.vue";
import InviteRequest from "../Components/InviteRequest.vue";

import {
    CogIcon,
    ChatBubbleLeftRightIcon,
    EllipsisVerticalIcon,
} from "@heroicons/vue/24/outline";

const page = usePage();

const client = AgoraRTC.createClient({
    mode: "live",
    codec: "vp9",
    role: "audience",
});

const uid = ref(null);
const host_uid = ref(null);
const channelId = ref(page.props.channelId);
const appId = ref(page.props.appId);
const token = ref(page.props.rtcToken);
const localTracks = ref([]);
const remoteUsers = ref({});
const isJoined = ref(false);
const inviteData = ref(null);
const invitedToStage = ref(false);
const openTipModal = ref(false);
const openChooseDevices = ref(false);
const isVideoMuted = ref(false);
const isAudioMuted = ref(false);
const isBroadcasting = ref(false);
const hasPermissionToGoLive = ref(false);
const localVideoDevices = ref([]);
const localAudioDevices = ref([]);
const selectedAudioDevice = ref("");
const selectedVideoDevice = ref("");
const notificationData = ref({
    message: "You Got This!",
    description: "You are now live!",
});
const notificationType = ref("error");
const notificationOpen = ref(false);

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

const leave = async () => {
    client.leave();
    isBroadcasting.value = false;
};

//leave
//subscribe
const subscribe = async (user, mediaType) => {
    const uid = user.uid;
    if (host_uid.value == null) {
        host_uid.value = uid.toString();
    }

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
    subscribe(user, mediaType);
};

const handleUserUnpublished = (user, mediaType) => {
    console.log('"User Unpublished" event for remote users is triggered');
};

const closeChooseDevices = () => {
    openChooseDevices.value = false;
};

const accessDevices = async () => {
    localTracks.value = await AgoraRTC.createMicrophoneAndCameraTracks(
        {},
        {
            encoderConfig: "720p_auto",
        }
    );
};

const publish = async () => {
    await client.publish(localTracks.value);
};

const unpublish = async () => {
    await client.unpublish();
};

const goLive = async () => {
    await client.setClientRole("host");
    await publish();
    isBroadcasting.value = true;
};

const endLive = async () => {
    await unpublish();
    await client.setClientRole("audience");
    if (localTracks.value.length > 0) {
        stopLocalTrack();
    }
    localTracks.value = [];
    isBroadcasting.value = false;
    hasPermissionToGoLive.value = false;
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
    await localTracks.value[1].play("remote-player");
};

const stopLocalTrack = async () => {
    await localTracks.value[1].stop();
};

const rtmChat = ref();

const sysMessage = (json, peerId) => {
    if (json.message == "permissionRequest") {
        inviteData.value = {
            peerId: peerId,
            message: "Permission Request",
            description: json.userData.name + " invites you to go live.",
        };
        invitedToStage.value = true;
    }
    else if (json.message == "permissionResponse") {
        if (json.response == "OK") {
            prepareGoLive();
        } else {
            notificationData.value = {
                message: "Permission Denied",
                description: "The host denied your request to go live.",
            };
            notificationType.value = "error";
            notificationOpen.value = true;
        }
    }
};

const approveInviteRequest = (peerId) => {
    rtmChat.value.$.exposed.sendPermissionResponse(peerId, "OK");
    prepareGoLive();
    invitedToStage.value = false;
};

const rejectInviteRequest = (peerId) => {
    rtmChat.value.$.exposed.sendPermissionResponse(peerId, "FAIL");
    invitedToStage.value = false;
};

const closeNotification = () => {
    notificationOpen.value = false;
};

const requestPermission = async () => {
    // find host UID
    if (host_uid.value != null) {
        rtmChat.value.$.exposed.sendPermissionRequest(host_uid.value);
    } else {
        alert("No Host");
    }
};

const prepareGoLive = async () => {
    hasPermissionToGoLive.value = true;
    if (localTracks.value.length > 0) {
        stopLocalTrack();
    }
    await accessDevices();
    await playLocalTrack();
    await getDevices();
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

onBeforeUnmount(() => {
    leave();
});

onMounted(async () => {
    await join();
});

onUnmounted(async () => {
    await leave();
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
    <InviteRequest
        :data="inviteData"
        :show="invitedToStage"
        @approve="approveInviteRequest"
        @ignore="rejectInviteRequest"
    />
    <Head title="Live" />

    <Notification
        :type="notificationType"
        :data="notificationData"
        :show="notificationOpen"
        @close="closeNotification"
    />

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
        class="grid xs:grid-cols-6 md:grid-cols-9 border-r border-gray-800 xs:min-h-screen-app md:h-screen"
    >
        <!-- MAIN CONTENT -->
        <div
            class="col-span-6 border-r border-gray-800 h-screen p-3 flex flex-col gap-4"
        >
            <div
                class="relative w-full h-full bg-gray-800 rounded-lg text-2xl font-bold flex justify-center items-center"
            >
                <div
                    id="remote-player"
                    style="display: contents"
                    class="w-full h-full rounded-lg"
                ></div>

                <div
                    v-if="hasPermissionToGoLive"
                    class="w-full h-12 flex flex-row justify-between items-center absolute bottom-0 rounded-b-lg z-2"
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
                <div class="flex gap-2">
                    <button
                        class="text-sm font-medium text-gray-100 bg-gray-500 hover:bg-gray-600 px-5 py-1 rounded-lg"
                        @click="requestPermission()"
                    >
                        Request Go Live
                    </button>
                </div>
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
                    <ViewerChat
                        ref="rtmChat"
                        @sysMessage="sysMessage"
                        :channelId="page.props.channelId"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<style>
.agora_video_player {
    object-fit: cover !important;
    border: 1px solid #ccc;
}
</style>
