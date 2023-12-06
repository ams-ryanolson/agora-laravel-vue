<script setup>
import { ref } from "vue";
import { usePage, Link } from "@inertiajs/vue3";
import { PlusIcon } from "@heroicons/vue/24/outline";

const page = usePage();

const liveStreams = ref(page.props.liveStreams);
</script>

<template>
    <div
        class="w-full flex flex-col pt-2 border-r border-gray-800 gap-2 min-h-screen-app"
    >
        <div
            class="flex justify-between border-b border-gray-700 pb-4 pt-2 px-4"
        >
            <div class="text-2xl font-semibold text-gray-300">FanTV</div>
            <Link :href="route('live.create')">
                <button
                    class="flex gap-2 items-center bg-sky-500 hover:bg-sky-600 px-5 py-1.5 rounded-lg"
                >
                    <PlusIcon class="w-5 h-5 text-gray-100" />
                    <span class="text-white font-medium">Start Broadcast</span>
                </button>
            </Link>
        </div>
        <div
            v-if="liveStreams.length === 0"
            class="flex flex-col items-center justify-center h-64"
        >
            <div class="text-2xl font-semibold text-gray-300">
                No Live Streams
            </div>
            <div class="text-gray-400">Start a live stream to see it here</div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 px-2 gap-3">
            <div
                v-for="stream in liveStreams"
                :key="stream.id"
                class="w-full h-64 flex flex-col"
            >
                <img
                    alt="image"
                    :src="stream.cover"
                    class="object-cover w-full h-48"
                />
                <div class="flex flex-col justify-between h-full py-2">
                    <div class="flex flex-col gap-2">
                        <div class="flex justify-between items-center">
                            <div class="flex gap-2 items-center">
                                <img
                                    alt="image"
                                    :src="stream.user.profile_photo_path"
                                    class="w-8 h-8 rounded-full"
                                />
                                <div class="flex flex-col">
                                    <div class="font-medium text-gray-100">
                                        {{ stream.user.user_display_name }}
                                    </div>
                                    <div
                                        class="text-sm font-medium text-gray-500 pr-4"
                                    >
                                        {{ stream.description }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <Link
                                    :href="'/live/' + stream.uuid"
                                    class="text font-medium text-gray-100 bg-sky-500 hover:bg-sky-600 px-5 py-1.5 rounded-lg"
                                >
                                    Join
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
