<script setup>
import SvgIcon from '@jamescoyle/vue-icon'
import { mdiPlus, mdiLogout, mdiMessageOutline, mdiFileEditOutline, mdiDeleteOutline, mdiCheckBold, mdiClose, mdiMenu } from '@mdi/js'
import { Link, router, usePage } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { onMounted, ref, watch } from 'vue';
import { useDebounceFn } from '@vueuse/core'
import { useWindowSize } from '@vueuse/core'

const user = usePage().props.auth.user;
const roomToEdit = ref({ name: '', id: '' });
const editRoomName = ref(false);
const nameDiv = ref(null);
const nameLink = ref(null);
const chatSettings = ref({});
const chatRooms = ref([]);
const activeChatRoom = ref(null);
const showMenu = ref(false);
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
    }
});

watch(() => usePage().props.chat.chatRooms, (newValue, oldValue) => {
    chatRooms.value = newValue;
});

watch(() => usePage().props.chat.chatSettings, (newValue, oldValue) => {
    chatSettings.value = newValue;
});

const sendCreateRoom = () => {
    router.post('/chat-rooms', { user_id: user.id, name: 'New Chat Room' }, {});
}

const sendDeleteRoom = (roomId) => {
    router.delete("/chat-rooms/" + roomId, {}, {
        preserveScroll: true,
        preserveState: true
    });
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
    if (showMenu.value)
        toggleMenu();
}

const editRoom = room => {
    editRoomName.value = true;
    if (activeChatRoom.value.id !== room.id)
        nameLink.value[0].focus();
    else
        nameDiv.value[0].focus();
    roomToEdit.value = { ...room };
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
    showMenu.value = !showMenu.value;
}

</script>

<template>
    <div class=" bg-[hsl(0,0%,10%)] fixed flex inset-0">
        <div v-if="width > 640 ? false : true"
            :class="{ 'fixed top-0 p-5 left-0 z-50 justify-between h-[40px] items-center flex w-full bg-[hsl(0,0%,30%)]': true, 'hidden': showMenu }">
            <svg-icon @click="toggleMenu" type="mdi" class="text-white hover:cursor-pointer" size="32"
                :path="mdiMenu"></svg-icon>
            <div class="text-white text-lg">
                {{ activeChatRoom?.name }}
            </div>
            <svg-icon @click="sendCreateRoom" type="mdi" class="text-white hover:cursor-pointer" size="32"
                :path="mdiPlus"></svg-icon>
        </div>
        <div v-if="width > 640 ? true : showMenu"
            class=" bg-[hsl(0,0%,5%)] shrink-0 h-full w-full text-white text-xs sm:text-base lg:w-[320px] sm:w-[260px] p-2">
            <div class="flex flex-col h-full lg:p-4 p-4">
                <div class="flex gap-2">
                    <PrimaryButton @click="sendCreateRoom"
                        class="w-full text-start border p-2 flex border-[hsl(0,0%,25%)] rounded gap-2 items-center hover:border-[hsl(0,0%,30%)] hover:scale-[1.01]">
                        <svg-icon type="mdi" :path="mdiPlus"></svg-icon>
                        <div>Nouveau Chat</div>
                    </PrimaryButton>
                    <PrimaryButton class="w-full border-[hsl(0,0%,20%)] hover:border-white sm:hidden" @click="toggleMenu">
                        Fermer</PrimaryButton>
                </div>
                <div class="flex-col flex-1 overflow-auto scrollbar-hide md:scrollbar-default">
                    <div v-for="( room, index ) in  chatRooms " :key="index">
                        <div
                            :class="{ 'flex items-center p-2 rounded mt-2': true, 'bg-[hsl(0,0%,30%)]': room.id === activeChatRoom?.id }">
                            <Link href="/" :class="{ 'hidden': room.id == activeChatRoom.id }" :data="{ chatRoom: room.id }"
                                class="flex gap-4 items-center  min-w-[80%]">
                            <svg-icon type="mdi" size="20" :path="mdiMessageOutline"></svg-icon>
                            <div :class="{ 'min-w-[80%]': true, 'hidden': editRoomName && room.id == activeChatRoom.id }">{{
                                room.name }}</div>
                            <input ref="nameLink" @keydown.enter="sendUpdateRoomName" v-model="roomToEdit.name"
                                :class="{ 'min-w-[80%] text-xs sm:text-base p-0 m-0 border-none border-b bg-transparent text-white focus:ring-0 focus:outline-none': true, 'hidden': !editRoomName || room.id !== activeChatRoom.id }" />
                            </Link>
                            <div
                                :class="{ 'hidden': room.id !== activeChatRoom.id, 'flex gap-4 items-center  min-w-[80%]': true }">
                                <svg-icon type="mdi" size="20" :path="mdiMessageOutline"></svg-icon>
                                <div
                                    :class="{ 'min-w-[80%] truncate': true, 'hidden': editRoomName && room.id == activeChatRoom.id }">
                                    {{ room.name }}
                                </div>
                                <input ref="nameDiv" @keydown.enter="sendUpdateRoomName"
                                    :class="{ 'min-w-[80%]  text-xs sm:text-base  p-0 m-0 border-none border-b bg-transparent text-white focus:ring-0 focus:outline-none ': true, 'hidden': !editRoomName || room.id !== activeChatRoom.id }"
                                    v-model="roomToEdit.name" />
                            </div>
                            <div v-if="room.id == activeChatRoom.id" class="relative min-w-[80px] h-full">
                                <div v-if="!editRoomName" class="absolute -top-[11px] flex items-center gap-2">
                                    <svg-icon @click="() => editRoom(room)" class="hover:cursor-pointer" type="mdi"
                                        size="20" :path="mdiFileEditOutline"></svg-icon>
                                    <svg-icon class="hover:cursor-pointer" @click="() => sendDeleteRoom(room.id)" type="mdi"
                                        size="20" :path="mdiDeleteOutline"></svg-icon>
                                </div>
                                <div v-else class="absolute min-w-[80px] -top-[11px] flex items-center gap-2">
                                    <svg-icon @click="sendUpdateRoomName" class="hover:cursor-pointer" type="mdi" size="20"
                                        :path="mdiCheckBold"></svg-icon>
                                    <svg-icon @click="closeEditRoom" class="hover:cursor-pointer" type="mdi" size="20"
                                        :path="mdiClose"></svg-icon>
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
                        <div class="font-bold  text-base sm:text-xl">Température</div>
                        <div class="flex items-center justify-between">
                            <input v-on:change="sendUpdateChatSettings" v-model="chatSettings.temperature" class="w-[80%]"
                                type="range" min="0" max="2" step="0.1" />
                            <div class="text-base sm:text-lg">{{ chatSettings.temperature }}</div>
                        </div>
                    </div>
                    <PrimaryButton href="/logout" method="POST" type="link"
                        class="w-full text-start border p-2 flex border-[hsl(0,0%,20%)] rounded gap-2 items-center hover:border-[hsl(0,0%,30%)] hover:scale-[1.01]">
                        <svg-icon type="mdi" :path="mdiLogout"></svg-icon>
                        <div>Se déconnecter</div>
                    </PrimaryButton>
                </div>
            </div>
        </div>
        <div :class="{ 'h-full w-full': true, 'hidden sm:block': showMenu }">
            <slot />
        </div>
    </div>
</template>
