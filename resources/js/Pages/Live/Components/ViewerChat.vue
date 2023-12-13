<script setup>
import { PaperAirplaneIcon } from "@heroicons/vue/24/outline";
import { ref, onBeforeUnmount, onMounted, defineExpose } from "vue";
import { usePage } from "@inertiajs/vue3";
import AgoraRTM from "agora-rtm-sdk";
import ChatMessage from "./ChatMessage.vue";

const props = defineProps(["channelId"]);
const emit = defineEmits(["channelCount","sysMessage"]);

const page = usePage();
const appId = page.props.appId;
const token = ref(page.props.rtmToken);
const channelName = props.channelId;
const messages = ref([]);
const message = ref("");

const chatMembers = ref([]);
const occupantList = ref([]);
const rtmClient = AgoraRTM.createInstance(appId);
let rtmChannel = null;
const userData = {
    name: page.props.auth.user.name,
    avatar: page.props.auth.user.profile_photo_path,
};
let uid = page.props.auth.user.id.toString();

onMounted(async () => {
    await rtmClient.on("MessageFromPeer", ({ text }, peerId) => {
        const json = JSON.parse(text);
        emit("sysMessage",json,peerId);               
    });

    await rtmClient.on("ConnectionStateChanged", (newState, reason) => {
        console.log(
            "on connection state changed to " + newState + " reason: " + reason
        );
        if (newState === "CONNECTED") {
            console.log("Connected to AgoraRTM");
        }
    });

    await rtmClient
        .login({ uid: uid, token: token.value })
        .then(() => {
            // Create the channel instance once
            rtmChannel = rtmClient.createChannel(channelName);
            return rtmChannel.join();
        })
        .then(() => {
            rtmChannel.on("ChannelMessage", (messageData) => {
                const message = JSON.parse(messageData.text);
                messages.value.push(message);
            });
            rtmChannel.on("MemberJoined", (joinData) => {});
            rtmChannel.on("MemberLeft", (leftData) => {
                console.log("MemberLeft", leftData);
            });
        })
        .catch((err) => {
            console.log("AgoraRTM channel login failure", err);
        });

       
    await rtmClient
        .setLocalUserAttributes({
            name: page.props.auth.user.name || "na",
            avatar: page.props.auth.user.profile_photo_path || "na",
        })
        .then(() => {
            console.log("AgoraRTM client set local user attributes success");
        })
        .catch((err) => {
            console.log(
                "AgoraRTM client set local user attributes failure",
                err
            );
        });

    await rtmChannel.getMembers().then((members) => {
        Promise.all(
            members.map(async (member) => {
                try {
                    const attributes = await rtmClient.getUserAttributes(
                        member
                    );
                    attributes.userId = member;
                    chatMembers.value.push(attributes);
                } catch (err) {}
            })
        );
    });

    await getChannelCount();
});

const getChannelCount = async () => {
    await rtmClient.getChannelMemberCount([channelName]).then((counts) => {
        const count = counts[channelName];
        emit("channelCount", count);
    });
};

const sendMessage = () => {

    const userMessage = {
        text: message.value,
        userData: userData,
    };
    if (message.value === "") {
        return;
    }
    if (rtmChannel) {
        rtmChannel
            .sendMessage({ text: JSON.stringify(userMessage) })
            .then(() => {
                messages.value.push({
                    text: message.value,
                    userData: userData,
                });
                message.value = "";
            })
            .catch((err) => {
                console.log("AgoraRTM channel sendMessage failure", err);
            });
    }
};

onBeforeUnmount(() => {
    rtmChannel.leave();
    rtmClient.logout();
});

const sendPermissionRequest = (peerId) => {

    const systemMessage = {
        message: 'permissionRequest',
        userData: userData,
    };

    if (rtmClient) {
        rtmClient
            .sendMessageToPeer({ text: JSON.stringify(systemMessage) },peerId)
            .then(() => {
            })
            .catch((err) => {
                console.log("AgoraRTM rtmClient sendMessageToPeer failure", err);
            });
    }
};

defineExpose({ sendPermissionRequest });
</script>

<template>
    <div
        id="chatMessages"
        v-for="message in messages"
        :key="message.id"
        class="w-full flex items-center mb-4 mx-4"
    >
        <ChatMessage :message="message" />
    </div>

    <div class="absolute bottom-1 w-full flex justify-center items-center">
        <input
            type="text"
            v-model="message"
            @keydown.enter="sendMessage"
            class="relative w-full h-12 rounded-lg bg-gray-900 text-gray-100 placeholder-gray-400 pl-4 pr-12 py-2 border-gray-800 m-2"
            placeholder="Type a message..."
        />
        <button
            class="absolute right-4 bg-sky-600 hover:bg-sky-500 rounded-lg p-1 text-white"
        >
            <PaperAirplaneIcon
                @click="sendMessage"
                class="w-5 h-5 text-gray-300 cursor-pointer rotate-90"
            />
        </button>
    </div>
</template>
