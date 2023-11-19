<script setup>
import SvgIcon from '@jamescoyle/vue-icon'
import { mdiPlus, mdiLogout, mdiMessageOutline, mdiFileEditOutline, mdiDeleteOutline, mdiCheckBold, mdiClose, mdiMenu, mdiVacuum } from '@mdi/js'
import { Link, router, usePage } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { onMounted, ref, watch } from 'vue';
import { useDebounceFn } from '@vueuse/core'
import { useWindowSize } from '@vueuse/core'

const user = usePage().props.auth.user;
const roomToEdit = ref({ name: '', id: '' });
const editRoomName = ref(false);
const nameLink = ref(null);
const chatSettings = ref({});
const chatRooms = ref([]);
const activeChatRoom = ref(null);
const showMenu = ref(false);
const valideDeleteAllChats = ref(false);
const showContent = ref(true);
const { width } = useWindowSize()
const models = ref([
    {
        "name": "gpt-3.5-turbo",
        "info": "Advanced model for generating text"
    },
    {
        "name": "gpt-4",
        "info": "Next-generation model for generating text"
    },
    {
        "name": "gpt-3.5-turbo-0301",
        "info": "Version of the gpt-3.5-turbo model"
    },
    {
        "name": "gpt-3.5-turbo-0613",
        "info": "Version of the gpt-3.5-turbo model"
    },
    {
        "name": "gpt-3.5-turbo-16k-0613",
        "info": "Version of the gpt-3.5-turbo model with 16k tokens"
    }
]);

onMounted(() => {
    if (usePage().props.chat.activeChatRoom != null) {
        activeChatRoom.value = { id: usePage().props.chat.activeChatRoom.id, name: usePage().props.chat.activeChatRoom.name };
    }
    chatRooms.value = usePage().props.chat.chatRooms;
    chatSettings.value = usePage().props.chat.chatSettings;
})

watch(() => usePage().props.chat.activeChatRoom, (newValue, oldValue) => {
    if (newValue) {
        activeChatRoom.value = { id: newValue.id, name: newValue.name };
        if (showMenu.value)
            toggleMenu();
    }
});

watch(() => usePage().props.chat.chatRooms, (newValue, oldValue) => {
    chatRooms.value = newValue;
});

watch(() => usePage().props.chat.chatSettings, (newValue, oldValue) => {
    chatSettings.value = newValue;
});

const sendCreateRoom = () => {
    router.post('/chat-rooms', { name: 'New Chat Room' }, {
        preserveState: true,
    });
}

const sendDeleteRoom = (roomId) => {
    router.delete("/chat-rooms/" + roomId, {}, {
        preserveScroll: true,
        preserveState: true
    });
}

const sendDeleteAllRooms = () => {
    router.delete("/chat-rooms/all", {}, {
        preserveScroll: true,
        preserveState: true,
    });
    toggleDeleteChats();

}


const sendUpdateRoomName = () => {
    router.put('/chat-rooms/' + roomToEdit.value.id, { name: roomToEdit.value.name }, {
        preserveScroll: true,
        preserveState: true,
        onFinish: () => {
            editRoomName.value = false;
            chatRooms.value.map(room => room.id == roomToEdit.value.id ? roomToEdit.value : null)
            roomToEdit.value = { name: '', id: '' };
        }
    });
}

const editRoom = room => {
    editRoomName.value = true;
    roomToEdit.value = { ...room };
    setTimeout(() => {

        nameLink.value[0].focus();
    }, 100);
}

const closeEditRoom = () => {
    editRoomName.value = false;
    roomToEdit.value = { name: '', id: '' };
}

const sendUpdateChatSettings = useDebounceFn(() => {
    router.put('/chat-settings', { ...chatSettings.value }, {
    });
}, 200)

const toggleMenu = () => {
    if (showMenu.value) {
        showMenu.value = false;
        setTimeout(() => {
            showContent.value = true;
        }, 300);
    } else {
        showContent.value = false;
        setTimeout(() => {
            showMenu.value = true;
        }, 300);
    }
}

const toggleDeleteChats = () => {
    valideDeleteAllChats.value = !valideDeleteAllChats.value;
}

</script>

<template>
    <Transition>
        <div class=" bg-[hsl(0,0%,10%)] fixed flex inset-0">
            <div v-if="width > 1024 ? false : true"
                :class="{ 'fixed top-0 p-5 left-0 z-50 justify-between h-[40px] items-center flex w-full bg-[hsl(0,0%,30%)]': true, 'hidden': showMenu }">
                <svg-icon @click="toggleMenu" type="mdi" class="text-white hover:cursor-pointer" size="32"
                    :path="mdiMenu"></svg-icon>
                <div class="text-white text-lg">
                    {{ activeChatRoom?.name }}
                </div>
                <svg-icon @click="sendCreateRoom" type="mdi" class="text-white hover:cursor-pointer" size="32"
                    :path="mdiPlus"></svg-icon>
            </div>
            <Transition name="slide">
                <div v-if="width > 1024 ? true : showMenu"
                    class=" bg-[hsl(0,0%,5%)] z-50 h-full w-full shrink-0 text-white text-xs sm:text-base lg:w-[320px] p-2">
                    <div class="flex flex-col h-full lg:p-4 p-4">
                        <div class="flex gap-2">
                            <PrimaryButton @click="sendCreateRoom"
                                class="w-full text-start border p-2 flex border-[hsl(0,0%,25%)] rounded gap-2 items-center hover:border-[hsl(0,0%,30%)] hover:scale-[1.01]">
                                <svg-icon type="mdi" :path="mdiPlus"></svg-icon>
                                <div>New Chat</div>
                            </PrimaryButton>
                            <PrimaryButton class="w-full border-[hsl(0,0%,20%)] hover:border-white lg:hidden"
                                @click="toggleMenu">
                                Fermer</PrimaryButton>
                        </div>
                        <div class="flex-col flex-1 overflow-auto overflow-x-hidden scrollbar-hide md:scrollbar-default">
                            <div v-for="( room, index ) in  chatRooms " :key="index">
                                <div
                                    :class="{ 'flex items-center p-2 rounded mt-2 w-full': true, 'bg-[hsl(0,0%,30%)]': room.id === activeChatRoom?.id }">
                                    <Link href="/" v-if="room.id !== activeChatRoom.id"
                                        @click="() => showMenu ? toggleMenu() : null" :data="{ chatRoom: room.id }"
                                        class="flex gap-4 items-center  min-w-[80%]">
                                    <div>
                                        <svg-icon type="mdi" size="20" :path="mdiMessageOutline"></svg-icon>
                                    </div>
                                    <div
                                        :class="{ 'min-w-[60%]': true, 'hidden': editRoomName && room.id == activeChatRoom.id }">
                                        {{
                                            room.name }}</div>
                                    </Link>
                                    <div v-else
                                        :class="{ 'hidden': room.id !== activeChatRoom.id, 'flex pr-4 gap-4 items-center sm:max-w-[250px] grow': true }">
                                        <div>
                                            <svg-icon type="mdi" size="20" :path="mdiMessageOutline"></svg-icon>
                                        </div>
                                        <div :class="{ 'truncate': true, 'hidden': editRoomName }">
                                            {{ room.name }}
                                        </div>
                                        <input ref="nameLink" @keydown.enter="sendUpdateRoomName" v-model="roomToEdit.name"
                                            :class="{ 'text-xs sm:text-base w-full p-0 m-0 border-none border-b bg-transparent text-white focus:ring-0 focus:outline-none': true, 'hidden': !editRoomName }" />
                                    </div>
                                    <div v-if="room.id == activeChatRoom.id" class="relative shrink-0 w-[50px] h-full">
                                        <div v-if="!editRoomName" class="absolute -top-[11px] flex items-center gap-2">
                                            <svg-icon @click="() => editRoom(room)" class="hover:cursor-pointer" type="mdi"
                                                size="20" :path="mdiFileEditOutline"></svg-icon>
                                            <svg-icon class="hover:cursor-pointer" @click="() => sendDeleteRoom(room.id)"
                                                type="mdi" size="20" :path="mdiDeleteOutline"></svg-icon>
                                        </div>
                                        <div v-else
                                            class="absolute min-w-[80px] shrink-0 -top-[11px] flex items-center gap-2">
                                            <svg-icon @click="sendUpdateRoomName" class="hover:cursor-pointer" type="mdi"
                                                size="20" :path="mdiCheckBold"></svg-icon>
                                            <svg-icon @click="closeEditRoom" class="hover:cursor-pointer" type="mdi"
                                                size="20" :path="mdiClose"></svg-icon>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="shrink-0 bg-[hsl(0,0%,5%)]">
                            <div class="mb-4">
                                <div class="font-bold text-base sm:text-xl">Model</div>
                                <select v-on:change="sendUpdateChatSettings"
                                    class="bg-[hsl(0,0%,20%)] text-white border-none text-xs sm:text-base w-full rounded focus:ring-0 focus:outline-none"
                                    v-model="chatSettings.model">
                                    <option class="text-center" v-for=" model  in  models ">
                                        {{ model.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <div class="font-bold  text-base sm:text-xl">Temperature</div>
                                <div class="flex items-center justify-between">
                                    <input v-on:change="sendUpdateChatSettings" v-model="chatSettings.temperature"
                                        class="w-[80%]" type="range" min="0" max="2" step="0.1" />
                                    <div class="text-base sm:text-lg">{{ chatSettings.temperature }}</div>
                                </div>
                            </div>
                            <PrimaryButton v-if="!valideDeleteAllChats" @click="toggleDeleteChats"
                                class="w-full text-start border p-2 mb-2 flex border-[hsl(0,0%,20%)] rounded gap-2 items-center hover:border-[hsl(0,0%,30%)] hover:scale-[1.01]">
                                <svg-icon type="mdi" :path="mdiVacuum"></svg-icon>
                                <div>Delete All Chats</div>
                            </PrimaryButton>
                            <PrimaryButton v-else
                                class="w-full text-start border h-[41.6px] p-1 mb-2 flex bg-red-700 text-white rounded gap-2 items-center hover:border-red-900  hover:scale-[1.01]">
                                <div class="w-full">Confirm</div>
                                <div class="flex items-center gap-4">
                                    <div class="hover:bg-red-500 rounded-full p-1">
                                        <svg-icon @click="sendDeleteAllRooms" class="hover:cursor-pointer" type="mdi"
                                            size="20" :path="mdiCheckBold"></svg-icon>
                                    </div>
                                    <div class="hover:bg-red-500 rounded-full p-1">
                                        <svg-icon @click="toggleDeleteChats" class="hover:cursor-pointer" type="mdi"
                                            size="20" :path="mdiClose"></svg-icon>
                                    </div>
                                </div>
                            </PrimaryButton>
                            <PrimaryButton href="/logout" method="POST" type="link"
                                class="w-full text-start border p-2 flex border-[hsl(0,0%,20%)] rounded gap-2 items-center hover:border-[hsl(0,0%,30%)] hover:scale-[1.01]">
                                <svg-icon type="mdi" :path="mdiLogout"></svg-icon>
                                <div>Log out</div>
                            </PrimaryButton>
                        </div>
                    </div>
                </div>
            </Transition>
            <Transition name="slide-reverse">
                <div v-if="width > 1024 ? true : showContent" :class="{ ' h-full w-full': true }">
                    <slot />
                </div>
            </Transition>
        </div>
    </Transition>
</template>

<style>
.slide-enter-active,
.slide-reverse-enter-active,
.slide-leave-active,
.slide-reverse-leave-active {
    transition: all 0.3s ease-in-out;
}

.slide-enter-from,
.slide-leave-to {
    transform: translateX(-100vw);
}


.slide-reverse-enter-from,
.slide-reverse-leave-to {
    transform: translateX(100vw);
}
</style>
