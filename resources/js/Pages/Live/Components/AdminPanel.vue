<script setup>
import { computed, reactive, ref } from "vue";
import { router, Link, usePage } from "@inertiajs/vue3";
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
import { ChevronLeftIcon } from "@heroicons/vue/20/solid";

const page = usePage().props;
const props = defineProps(["visible"]);

//turn page.live.tip_options from json to array
const tips = ref(JSON.parse(page.live.tip_options));

//computed jsonTips
const jsonTips = computed(() => {
    return JSON.stringify(tips.value);
});

const form = reactive({
    cover: page.live.cover,
    description: page.live.description,
    privacy: page.live.privacy,
    rtmp: "",
    tips: jsonTips,
    rtmpUrl: "",
    rtmpKey: "",
    rtmpSource: "",
});

const createMediaGatewayToken = async () => {
    try {
        const response = await axios
            .post("/live/createmediagatewaytoken", {
                userId: page.auth.user.id,
                region: "na",
                appId: "4fb2753a5318441f864b4ddc6118ab06",
            })
            .then((response) => {
                console.log(response.data);
                form.rtmpUrl = "rtmp://rtls-ingress-prod-na.agoramdn.com/live";
                form.rtmpKey = response.data.data.streamKey;
            });

        // Now you can handle the response as needed
    } catch (error) {
        // Handle errors here
        console.error("Error fetching media gateway token:", error);
    }
};
</script>

<template>
    <div :class="{ block: visible === 'admin', hidden: visible !== 'admin' }">
        <Disclosure as="div" v-slot="{ open }">
            <dt>
                <DisclosureButton
                    class="flex justify-between w-full text-left border-b border-gray-700 px-4 pb-4"
                >
                    <span class="text-xl font-medium text-gray-200">
                        Basic Info
                    </span>
                    <span class="ml-6 h-7 flex items-center">
                        <ChevronLeftIcon
                            class="h-6 w-6 transform"
                            :class="open ? '-rotate-90 transform' : ''"
                        />
                    </span>
                </DisclosureButton>
            </dt>
            <transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="transform scale-95 opacity-0"
                enter-to-class="transform scale-100 opacity-100"
                leave-active-class="transition duration-100 ease-out"
                leave-from-class="transform scale-100 opacity-100"
                leave-to-class="transform scale-95 opacity-0"
            >
                <disclosure-panel
                    as="dd"
                    class="py-4 w-full flex flex-col gap-4"
                >
                    <div class="flex flex-col mt-4 gap-4 px-4">
                        <div class="flex flex-col gap-2">
                            <label for="cover" class="text-white font-medium"
                                >Cover</label
                            >
                            <input
                                type="hidden"
                                id="cover"
                                v-model="form.cover"
                                class="rounded-lg border border-gray-700 bg-gray-800 text-white px-4 py-2"
                            />
                            <img
                                :src="form.cover"
                                class="rounded-lg bg-gray-800 text-white py-2 w-full h-48 object-cover"
                            />
                        </div>
                        <div class="flex flex-col gap-2">
                            <label
                                for="description"
                                class="text-white font-medium"
                                >Description</label
                            >
                            <textarea
                                id="description"
                                v-model="form.description"
                                class="rounded-lg border border-gray-700 bg-gray-800 text-white px-4 py-2"
                            ></textarea>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label for="privacy" class="text-white font-medium"
                                >Privacy</label
                            >
                            <select
                                id="privacy"
                                v-model="form.privacy"
                                class="rounded-lg border border-gray-700 bg-gray-800 text-white px-4 py-2"
                            >
                                <option value="everyone">Everyone</option>
                                <option value="followers">Followers</option>
                                <option value="subscribers">Subscribers</option>
                            </select>
                        </div>
                        <div class="flex flex-col gap-2">
                            <button
                                class="rounded-lg border border-gray-700 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2"
                                @click="update"
                            >
                                Update
                            </button>
                        </div>
                    </div>
                </disclosure-panel>
            </transition>
        </Disclosure>

        <Disclosure as="div" v-slot="{ open }">
            <dt>
                <DisclosureButton
                    class="flex justify-between w-full text-left border-b border-gray-700 px-4 py-4"
                >
                    <span class="text-xl font-medium text-gray-200">
                        Tip Settings
                    </span>
                    <span class="ml-6 h-7 flex items-center">
                        <ChevronLeftIcon
                            class="h-6 w-6 transform"
                            :class="open ? '-rotate-90 transform' : ''"
                        />
                    </span>
                </DisclosureButton>
            </dt>
            <transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="transform scale-95 opacity-0"
                enter-to-class="transform scale-100 opacity-100"
                leave-active-class="transition duration-100 ease-out"
                leave-from-class="transform scale-100 opacity-100"
                leave-to-class="transform scale-95 opacity-0"
            >
                <disclosure-panel
                    as="dd"
                    class="p-4 w-full flex flex-col gap-4"
                >
                    <!-- 5 Options needed where a user can type up a custom message and set a price -->
                    <div
                        v-for="(tip, index) in tips"
                        :key="tip.id"
                        class="flex flex-row gap-4"
                    >
                        <div class="flex flex-col gap-2">
                            <label for="tip1Name" class="text-white font-medium"
                                >Title</label
                            >
                            <input
                                type="text"
                                :id="'tipName' + tip.id"
                                v-model="tips[index].name"
                                class="rounded-lg border border-gray-700 bg-gray-800 text-white px-4 py-2"
                            />
                        </div>
                        <div class="flex flex-col gap-2">
                            <label
                                for="tip1Price"
                                class="text-white font-medium"
                                >Price</label
                            >
                            <input
                                type="text"
                                :id="'tipPrice_' + tip.id"
                                v-model="tips[index].price"
                                class="rounded-lg border border-gray-700 bg-gray-800 text-white px-4 py-2"
                                :disabled="tip.id === 5"
                            />
                        </div>
                    </div>
                </disclosure-panel>
            </transition>
        </Disclosure>

        <Disclosure as="div" v-slot="{ open }">
            <dt>
                <DisclosureButton
                    class="flex justify-between w-full text-left border-b border-gray-700 px-4 py-4"
                >
                    <span class="text-xl font-medium text-gray-200">
                        Advanced Settings
                    </span>
                    <span class="ml-6 h-7 flex items-center">
                        <ChevronLeftIcon
                            class="h-6 w-6 transform"
                            :class="open ? '-rotate-90 transform' : ''"
                        />
                    </span>
                </DisclosureButton>
            </dt>
            <transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="transform scale-95 opacity-0"
                enter-to-class="transform scale-100 opacity-100"
                leave-active-class="transition duration-100 ease-out"
                leave-from-class="transform scale-100 opacity-100"
                leave-to-class="transform scale-95 opacity-0"
            >
                <disclosure-panel
                    as="dd"
                    class="p-4 w-full flex flex-col gap-4 border-b border-gray-700"
                >
                    <div class="flex flex-col gap-2">
                        <label for="rtmp" class="text-white font-medium"
                            >RTMP URL</label
                        >
                        <input
                            type="text"
                            id="rtmp"
                            v-model="form.rtmpUrl"
                            class="rounded-lg border border-gray-700 bg-gray-800 text-white px-4 py-2"
                        />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="rtmp" class="text-white font-medium"
                            >RTMP Key</label
                        >
                        <input
                            type="text"
                            id="rtmp"
                            v-model="form.rtmpKey"
                            class="rounded-lg border border-gray-700 bg-gray-800 text-white px-4 py-2"
                        />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="streamSource" class="text-white font-medium"
                            >Stream Source</label
                        >
                        <input
                            type="text"
                            id="streamSource"
                            v-model="form.rtmpSource"
                            class="rounded-lg border border-gray-700 bg-gray-800 text-white px-4 py-2"
                        />
                    </div>
                    <div class="flex flex-col gap-2">
                        <button
                            class="rounded-lg border border-gray-700 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2"
                            @click="createMediaGatewayToken"
                        >
                            Update
                        </button>
                    </div>
                </disclosure-panel>
            </transition>
        </Disclosure>

        <!--    <div class="text-gray-400 p-4">-->
        <!--        <div class="text-lg font-medium">*** Please Note ***</div>-->
        <!--        <div class="pt-2">All changes you make will show up on the FanTV page immediately, however, it may not reflect in the stream until the next update</div>-->
        <!--        <div class="pt-2">Advanced settings should only be used by creators who know how to use them. They are extremely dangerous if you do not know what they are for</div>-->
        <!--        <div class="pt-2">If you are unsure about anything, please contact support</div>-->
        <!--    </div>-->
    </div>
</template>
