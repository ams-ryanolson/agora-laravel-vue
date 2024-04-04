<script setup>
import { ref, defineProps, watchEffect, reactive } from "vue";
import {
    Dialog,
    DialogPanel,
    DialogTitle,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    TransitionChild,
    TransitionRoot,
} from "@headlessui/vue";
import { PhotoIcon } from "@heroicons/vue/24/outline";
import { ChevronDownIcon } from "@heroicons/vue/20/solid";
import { router } from "@inertiajs/vue3";
import Vapor from "laravel-vapor";

const props = defineProps(["open"]);
const open = ref(false);

watchEffect(() => {
    open.value = props.open;
});

const form = reactive({
    description: "",
    tags: "",
    audioOnly: null,
    privacy: "Everyone",
    coverPhoto: "",
});

const setAudioOnly = (audioOnly) => {
    if (audioOnly == null) {
        form.audioOnly = false;
    }
    form.audioOnly = audioOnly;
};

const selectPrivacy = (privacy) => {
    form.privacy = privacy;
};

const uploadCoverPhoto = async () => {
    try {
        // Create an input element for file selection
        const input = document.createElement("input");
        input.type = "file";
        input.multiple = false;
        input.accept = "image/*";

        // Wait for the user to select a file
        await new Promise((resolve) => {
            input.addEventListener("change", resolve);
            document.body.appendChild(input);
            input.click();
            document.body.removeChild(input);
        });

        // Access the selected file
        const file = input.files[0];

        // Use Vapor to upload the cover photo
        const response = await Vapor.store(file, {
            onProgress: (progress) => {
                console.log(progress);
                // Assuming you have a variable to store the progress
                this.uploadProgress = Math.round(progress * 100);
            },
        });

        // Assuming you have a variable to store the cover photo URL
        form.coverPhoto = "https://cdn.realkink.men/" + response.key;
    } catch (error) {
        console.error("Error uploading cover photo:", error);
    }
};

// Example of how to call the function
// uploadCoverPhoto();

const submit = () => {
    router.post("/live", form);
};

const cancel = () => {
    form.description = "";
    form.tags = "";
    form.privacy = "Everyone";
    form.coverPhoto = "";
    router.visit("/live");
};
</script>

<template>
    <TransitionRoot as="template" :show="open">
        <Dialog as="div" class="relative z-10">
            <TransitionChild
                as="template"
                enter="ease-out duration-300"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="ease-in duration-200"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div
                    class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"
                />
            </TransitionChild>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div
                    class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0"
                >
                    <TransitionChild
                        as="template"
                        enter="ease-out duration-300"
                        enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        enter-to="opacity-100 translate-y-0 sm:scale-100"
                        leave="ease-in duration-200"
                        leave-from="opacity-100 translate-y-0 sm:scale-100"
                        leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    >
                        <DialogPanel
                            class="relative transform overflow-hidden rounded-lg bg-gray-900 px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
                        >
                            <div class="flex flex-col">
                                <div
                                    class="text-center flex justify-center items-center"
                                >
                                    <DialogTitle
                                        class="text-2xl leading-6 font-medium text-gray-100"
                                    >
                                        Start a stream
                                    </DialogTitle>
                                </div>
                                <div
                                    class="text-gray-400 text-center pt-4 flex flex-col gap-3 font-medium"
                                >
                                    <span
                                        >Setup your stream how you want it to
                                        be, then click Start Stream. Once you
                                        do, you will be joinable by your
                                        profile, FanTV list, your activity wall,
                                        or if someone shares your stream.</span
                                    >
                                    <span class="text-gray-300"
                                        >You must have verified your identity
                                        and added payout details before you can
                                        start streaming live</span
                                    >
                                </div>
                            </div>
                            <form @click.prevent class="mt-6">
                                <div class="flex flex-col gap-3">
                                    <div class="flex flex-col gap-2">
                                        <textarea
                                            id="description"
                                            name="description"
                                            v-model="form.description"
                                            class="w-full rounded-lg bg-gray-800 text-gray-100 placeholder-gray-400 pl-4 pr-12 py-2 border-gray-800"
                                            rows="3"
                                            placeholder="Description"
                                        />
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <input
                                            type="text"
                                            id="tags"
                                            name="tags"
                                            v-model="form.tags"
                                            class="w-full h-12 rounded-lg bg-gray-800 text-gray-100 placeholder-gray-400 pl-4 pr-12 py-2 border-gray-800"
                                            placeholder="Tags"
                                        />
                                    </div>

                                    <Menu
                                        as="div"
                                        class="relative inline-block text-left"
                                    >
                                        <div>
                                            <MenuButton
                                                class="inline-flex w-full justify-center gap-x-1.5 rounded-md bg-gray-800 px-3 py-4 text-sm font-medium text-gray-100 shadow-sm ring-1 ring-inset ring-gray-800"
                                            >
                                                {{
                                                    form.privacy
                                                        ? form.privacy
                                                        : "Select Audience"
                                                }}
                                                <ChevronDownIcon
                                                    class="-mr-1 h-5 w-5 text-gray-400"
                                                    aria-hidden="true"
                                                />
                                            </MenuButton>
                                        </div>

                                        <transition
                                            enter-active-class="transition ease-out duration-100"
                                            enter-from-class="transform opacity-0 scale-95"
                                            enter-to-class="transform opacity-100 scale-100"
                                            leave-active-class="transition ease-in duration-75"
                                            leave-from-class="transform opacity-100 scale-100"
                                            leave-to-class="transform opacity-0 scale-95"
                                        >
                                            <MenuItems
                                                class="absolute right-0 z-50 mt-2 w-full origin-top-right rounded-md bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none border border-gray-900"
                                            >
                                                <div class="py-1 space-y-2">
                                                    <MenuItem
                                                        v-slot="{ active }"
                                                    >
                                                        <a
                                                            href="#"
                                                            @click="
                                                                selectPrivacy(
                                                                    'Everyone'
                                                                )
                                                            "
                                                            :class="[
                                                                active
                                                                    ? 'bg-sky-600 text-gray-100'
                                                                    : 'text-gray-100',
                                                                'block px-4 py-2 text-center',
                                                            ]"
                                                            >Everyone</a
                                                        >
                                                    </MenuItem>
                                                    <MenuItem
                                                        v-slot="{ active }"
                                                    >
                                                        <a
                                                            href="#"
                                                            @click="
                                                                selectPrivacy(
                                                                    'Followers'
                                                                )
                                                            "
                                                            :class="[
                                                                active
                                                                    ? 'bg-sky-600 text-gray-100'
                                                                    : 'text-gray-100',
                                                                'block px-4 py-2 text-center',
                                                            ]"
                                                            >Followers</a
                                                        >
                                                    </MenuItem>
                                                    <MenuItem
                                                        v-slot="{ active }"
                                                    >
                                                        <a
                                                            href="#"
                                                            @click="
                                                                selectPrivacy(
                                                                    'Subscribers'
                                                                )
                                                            "
                                                            :class="[
                                                                active
                                                                    ? 'bg-sky-600 text-gray-100'
                                                                    : 'text-gray-100',
                                                                'block px-4 py-2 text-center',
                                                            ]"
                                                            >Subscribers</a
                                                        >
                                                    </MenuItem>
                                                </div>
                                            </MenuItems>
                                        </transition>
                                    </Menu>
                                    <div class="col-span-full">
                                        <div
                                            v-if="!form.coverPhoto"
                                            @click="uploadCoverPhoto"
                                            class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-500 px-6 py-10"
                                        >
                                            <div class="text-center">
                                                <PhotoIcon
                                                    class="mx-auto h-12 w-12 text-gray-500"
                                                    aria-hidden="true"
                                                />
                                                <div
                                                    class="mt-4 flex text-sm leading-6 text-gray-500"
                                                >
                                                    <label
                                                        for="file-upload"
                                                        class="relative cursor-pointer rounded-md bg-gray-900 font-semibold text-gray-200 focus-within:outline-none focus-within:ring-2 focus-within:ring-sky-600 focus-within:ring-offset-2 hover:text-sky-500"
                                                    >
                                                        <span
                                                            >Upload a file</span
                                                        >
                                                    </label>
                                                    <p class="pl-1">
                                                        or drag and drop
                                                    </p>
                                                </div>
                                                <p
                                                    class="text-xs leading-5 text-gray-500"
                                                >
                                                    PNG, JPG, GIF up to 10MB
                                                </p>
                                            </div>
                                        </div>
                                        <div v-if="form.coverPhoto">
                                            <img
                                                :src="form.coverPhoto"
                                                alt="image"
                                                class="w-full h-48 object-cover rounded-lg"
                                            />
                                        </div>
                                    </div>
                                    <Menu
                                        as="div"
                                        class="relative inline-block text-left"
                                    >
                                        <div>
                                            <MenuButton
                                                class="inline-flex w-full justify-center gap-x-1.5 rounded-md bg-gray-800 px-3 py-4 text-sm font-medium text-gray-100 shadow-sm ring-1 ring-inset ring-gray-800"
                                            >
                                                {{
                                                    form.audioOnly
                                                        ? "Audio Only"
                                                        : "Video Only"
                                                }}
                                                <ChevronDownIcon
                                                    class="-mr-1 h-5 w-5 text-gray-400"
                                                    aria-hidden="true"
                                                />
                                            </MenuButton>
                                        </div>

                                        <transition
                                            enter-active-class="transition ease-out duration-100"
                                            enter-from-class="transform opacity-0 scale-95"
                                            enter-to-class="transform opacity-100 scale-100"
                                            leave-active-class="transition ease-in duration-75"
                                            leave-from-class="transform opacity-100 scale-100"
                                            leave-to-class="transform opacity-0 scale-95"
                                        >
                                            <MenuItems
                                                class="absolute right-0 z-50 mt-2 w-full origin-top-right rounded-md bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none border border-gray-900"
                                            >
                                                <div class="py-1 space-y-2">
                                                    <MenuItem
                                                        v-slot="{ active }"
                                                    >
                                                        <a
                                                            href="#"
                                                            @click="
                                                                setAudioOnly(
                                                                    true
                                                                )
                                                            "
                                                            :class="[
                                                                active
                                                                    ? 'bg-sky-600 text-gray-100'
                                                                    : 'text-gray-100',
                                                                'block px-4 py-2 text-center',
                                                            ]"
                                                            >Audio Only</a
                                                        >
                                                    </MenuItem>
                                                    <MenuItem
                                                        v-slot="{ active }"
                                                    >
                                                        <a
                                                            href="#"
                                                            @click="
                                                                setAudioOnly(
                                                                    false
                                                                )
                                                            "
                                                            :class="[
                                                                active
                                                                    ? 'bg-sky-600 text-gray-100'
                                                                    : 'text-gray-100',
                                                                'block px-4 py-2 text-center',
                                                            ]"
                                                            >Video Only</a
                                                        >
                                                    </MenuItem>
                                                </div>
                                            </MenuItems>
                                        </transition>
                                    </Menu>

                                    <div
                                        class="w-full flex flex-row gap-6 justify-between items-center"
                                    >
                                        <button
                                            @click="submit"
                                            class="w-full rounded-lg bg-sky-500 hover:bg-sky-600 px-4 py-2 mt-6 font-medium text-white"
                                        >
                                            Start stream
                                        </button>
                                        <button
                                            @click="cancel"
                                            class="w-full rounded-lg bg-gray-600 hover:bg-sky-600 px-4 py-2 mt-6 font-medium text-white"
                                        >
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>
