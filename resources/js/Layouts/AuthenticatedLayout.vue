<script setup>
import SvgIcon from '@jamescoyle/vue-icon'
import { mdiPlus, mdiLogout, mdiMessageOutline, mdiFileEditOutline, mdiDeleteOutline, mdiCheckBold, mdiClose } from '@mdi/js'
import { Link, router, usePage } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { onMounted, ref, watch } from 'vue';

const user = usePage().props.auth.user;
const roomToEdit = ref();
const editRoomName = ref(false);
const nameDiv = ref(null);
const nameLink = ref(null);
onMounted(() => {
    if (usePage().props.chat.activeChatRoom != null) {
        activeChatRoom.value = { id: usePage().props.chat.activeChatRoom.id, name: usePage().props.chat.activeChatRoom.name };
    }
    chatRooms.value = usePage().props.chat.chatRooms;
})

watch(() => usePage().props.chat.activeChatRoom, (newValue, oldValue) => {
    if (newValue) {
        activeChatRoom.value = { id: newValue.id, name: newValue.name };
    }
});

watch(() => usePage().props.chat.chatRooms, (newValue, oldValue) => {
    chatRooms.value = newValue;
});

const sendCreateRoom = () => {
    router.post('/chat-rooms', { user_id: user.id, name: 'New Chat Room' }, {});
}

const sendDeleteRoom = (roomId) => {
    router.delete("/chat-rooms/" + roomId, {}, {});
}

const editRoom = room => {
    console.log(this.$refs);
    if (nameLink.value)
        nameLink.value.focus();
    else
        nameDiv.value.focus();
    roomToEdit.value = room;
    editRoomName.value = true;
}

const chatRooms = ref([]);
const activeChatRoom = ref(null);

</script>

<template>
    <div class="min-h-screen bg-[hsl(0,0%,10%)] flex">
        <div class=" bg-[hsl(0,0%,5%)] text-white shadow flex justify-between w-full max-w-[300px] flex-col">
            <div class="p-4">
                <PrimaryButton @click="sendCreateRoom"
                    class="min-w-full text-start border p-2 flex border-[hsl(0,0%,20%)] rounded gap-2 items-center hover:border-[hsl(0,0%,30%)] hover:scale-[1.01]">
                    <svg-icon type="mdi" :path="mdiPlus"></svg-icon>
                    <div>Nouveau Chat</div>
                </PrimaryButton>
                <div v-for="(room, index) in   chatRooms  " :key="index">
                    <div
                        :class="{ 'flex items-center p-2 rounded mt-2': true, 'bg-[hsl(0,0%,30%)]': room.id === activeChatRoom?.id }">
                        <Link v-if="room.id !== activeChatRoom.id" href="/" :data="{ chatRoom: room.id }"
                            class="flex gap-4 items-center  min-w-[80%]">
                        <svg-icon type="mdi" size="20" :path="mdiMessageOutline"></svg-icon>
                        <div v-if="!editRoomName" class="min-w-[80%]">{{ room.name }}</div>
                        <input ref="nameLink" v-else v-model="roomName.name"
                            class="min-w-[80%]  p-0 m-0 underline-black border-none bg-transparent text-white" />
                        </Link>
                        <div v-else class="flex gap-4 items-center  min-w-[80%]">
                            <svg-icon type="mdi" size="20" :path="mdiMessageOutline"></svg-icon>
                            <div v-if="!editRoomName" class="min-w-[80%]">{{ room.name }}</div>
                            <input ref="nameDiv" v-else v-model="roomToEdit.name"
                                class="min-w-[80%]  p-0 m-0 underline-black border-none bg-transparent text-white" />
                        </div>
                        <div class="relative w-[20%] h-full">
                            <div v-if="!editRoomName" class="absolute -top-[11px] flex items-center gap-2">
                                <svg-icon @click="() => editRoom(room)" class="hover:cursor-pointer" type="mdi" size="20"
                                    :path="mdiFileEditOutline"></svg-icon>
                                <svg-icon class="hover:cursor-pointer" @click="() => sendDeleteRoom(room.id)" type="mdi"
                                    size="20" :path="mdiDeleteOutline"></svg-icon>
                            </div>
                            <div v-else class="absolute -top-[11px] flex items-center gap-2">
                                <svg-icon class="hover:cursor-pointer" type="mdi" size="20" :path="mdiCheckBold"></svg-icon>
                                <svg-icon @click="() => editRoomName = false" class="hover:cursor-pointer" type="mdi"
                                    size="20" :path="mdiClose"></svg-icon>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-4">
                <PrimaryButton href="/logout" method="POST" type="link"
                    class="min-w-full text-start border p-2 flex border-[hsl(0,0%,20%)] rounded gap-2 items-center hover:border-[hsl(0,0%,30%)] hover:scale-[1.01]">
                    <svg-icon type="mdi" :path="mdiLogout"></svg-icon>
                    <div>Se déconnecter</div>
                </PrimaryButton>
            </div>
        </div>
        <div class="w-full">
            <slot />
        </div>
    </div>
</template>
