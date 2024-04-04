<script setup>
import {
    EllipsisVerticalIcon,
    PaperAirplaneIcon,
} from "@heroicons/vue/24/outline";
import { ref, onBeforeUnmount, onMounted, defineExpose } from "vue";
import { usePage } from "@inertiajs/vue3";
import AgoraRTM from "agora-rtm-sdk";
import ChatMessage from "../ChatMessage.vue";

const props = defineProps(["channelId", "visible"]);
const emit = defineEmits(["channelCount", "sysMessage", "chatMembersUpdate"]);

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
        // console.error("MessageFromPeer", json);
        emit("sysMessage", json, peerId);
    });

    await rtmClient.on("ConnectionStateChanged", (newState, reason) => {
        console.log(
            "on connection state changed to " + newState + " reason: " + reason
        );
        if (newState === "CONNECTED") {
            console.log("Connected to AgoraRTM");
        }
    });
    console.log("the UID is: ", uid);
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
            rtmChannel.on("MemberJoined", async (member) => {
                console.warn("MemberJoined", member);
                try {
                    const attributes = await rtmClient.getUserAttributes(
                        member
                    );
                    attributes.id = member;
                    chatMembers.value.push(attributes);
                    emit("chatMembersUpdate", chatMembers);
                    getChannelCount();
                    const systemMessage = {
                        id: null,
                        text: attributes.name + " joined the room",
                        userData: attributes,
                    };
                    messages.value.push(systemMessage)
                } catch (err) {}
            });
            rtmChannel.on("MemberLeft", async (member) => {
                console.warn("MemberLeft", member);
                const indexToRemove = chatMembers.value.findIndex(
                    (element) => element["id"] === member
                );
                console.error("MemberLeft 1", indexToRemove, chatMembers.value);
                if (indexToRemove !== -1) {
                    chatMembers.value.splice(indexToRemove, 1);
                }
                console.error("MemberLeft 2", indexToRemove, chatMembers.value);
                emit("chatMembersUpdate", chatMembers);
                getChannelCount();
                const attributes = await rtmClient.getUserAttributes(
                    member
                );
                const systemMessage = {
                    id: null,
                    text: attributes.name + " left the room",
                    userData: attributes,
                };
                messages.value.push(systemMessage)
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
                //  console.warn('getMembers',member);
                try {
                    const attributes = await rtmClient.getUserAttributes(
                        member
                    );
                    attributes.id = member;
                    chatMembers.value.push(attributes);
                    emit("chatMembersUpdate", chatMembers);
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
    if (message.value === "") {
        return;
    }

    const userMessage = {
        id: Date.now() + Math.random().toString(6).substr(2, 9),
        text: message.value,
        userData: userData,
    };

    if (rtmChannel) {
        rtmChannel
            .sendMessage({ text: JSON.stringify(userMessage) })
            .then(() => {
                messages.value.push({
                    id:  userMessage.id,
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

const sendPermissionResponse = (peerId, response) => {
    const systemMessage = {
        message: "permissionResponse",
        response: response,
        userData: userData,
    };

    if (rtmClient) {
        rtmClient
            .sendMessageToPeer({ text: JSON.stringify(systemMessage) }, peerId)
            .then(() => {})
            .catch((err) => {
                console.log(
                    "AgoraRTM rtmClient sendMessageToPeer failure",
                    err
                );
            });
    }
};

// used for inviting audience to stage
const sendPermissionRequest = (peerId) => {
    const systemMessage = {
        message: "permissionRequest",
        userData: userData,
    };

    if (rtmClient) {
        rtmClient
            .sendMessageToPeer({ text: JSON.stringify(systemMessage) }, peerId)
            .then(() => {})
            .catch((err) => {
                console.log(
                    "AgoraRTM rtmClient sendMessageToPeer failure",
                    err
                );
            });
    }
};

const sendRemoveAudience = (peerId) => {
    const systemMessage = {
        message: "removeAudience",
        userData: userData,
    };

    if (rtmClient) {
        rtmClient
            .sendMessageToPeer({ text: JSON.stringify(systemMessage) }, peerId)
            .then(() => {})
            .catch((err) => {
                console.log(
                    "AgoraRTM rtmClient sendMessageToPeer failure",
                    err
                );
            });
    }
};

const toggleMute = (peerId) => {
    const systemMessage = {
        message: "toggleMute",
        userData: userData,
    };

    if (rtmClient) {
        rtmClient
            .sendMessageToPeer({ text: JSON.stringify(systemMessage) }, peerId)
            .then(() => {})
            .catch((err) => {
                console.log(
                    "AgoraRTM rtmClient sendMessageToPeer failure",
                    err
                );
            });
    }
};

const sendDeleteMessage = (messageId) => {
    const systemMessage = {
        message: "deleteMessage",
        messageId: messageId,
    };

    if (rtmChannel) {
        rtmChannel
            .sendMessage({ text: JSON.stringify(systemMessage) })
            .then(() => {
                const indexToRemove = messages.value.findIndex(
                    (element) => element["id"] === messageId
                );
                if (indexToRemove !== -1) {
                    messages.value.splice(indexToRemove, 1);
                }
            })
            .catch((err) => {
                console.log("AgoraRTM channel sendMessage failure", err);
            });
    }
};

defineExpose({ sendPermissionResponse, sendPermissionRequest, sendRemoveAudience, toggleMute });

onBeforeUnmount(() => {
    rtmChannel.leave();
    rtmClient.logout();
});
</script>

<template>
    <div :class="{ block: visible === 'chat', hidden: visible !== 'chat' }">
        <div
            id="chatMessages"
            v-for="message in messages"
            :key="message.id"
            class="w-full flex items-center"
        >
            <ChatMessage
                :message="message"
                :host="true"
                @deleteMessage="sendDeleteMessage"
            />
        </div>

        <div class="absolute bottom-1 w-full flex justify-center items-center">
            <input
                type="text"
                v-model="message"
                @keydown.enter="sendMessage"
                class="relative w-full h-12 rounded-lg bg-gray-800 text-gray-100 placeholder-gray-400 pl-4 pr-12 py-2 border-gray-900 m-2 shadow-lg"
                placeholder="Type a message..."
            />
            <button
                class="absolute right-4 bg-sky-600 hover:bg-sky-500 rounded-lg p-1 text-white"
            >
                <PaperAirplaneIcon
                    @click="sendMessage"
                    class="w-5 h-5 text-gray-300 cursor-pointer"
                />
            </button>
        </div>
    </div>
</template>
