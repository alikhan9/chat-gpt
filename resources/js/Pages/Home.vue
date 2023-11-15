<script setup>
import { Head, usePage, router } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue'
import OpenAI from "openai";
import SvgIcon from '@jamescoyle/vue-icon'
import { mdiRobot, mdiAlphaABox, mdiSend, mdiStop } from '@mdi/js'
import { useWindowSize } from '@vueuse/core'

const props = defineProps({
    chatSettings: Object
})
onMounted(() => {
    if (usePage().props.chat.activeChatRoom != null) {
        fullChat.value = usePage().props.chat.activeChatRoom.messages.map(c => { return { content: c.content, role: c.role } });
        chatRoom.value = { id: usePage().props.chat.activeChatRoom.id, name: usePage().props.chat.activeChatRoom.name };
    }

    chatSettings.value = usePage().props.chat.chatSettings;
})

watch(() => usePage().props.chat.chatSettings, (newValue, oldValue) => {
    chatSettings.value = newValue;
});

const openai = new OpenAI({
    apiKey: import.meta.env.VITE_API_KEY,
    dangerouslyAllowBrowser: true,
});

const chatSettings = ref({});
const max_tokkens_context_message = ref(100);
const model = ref('gpt-3.5-turbo');
const message = ref('');
const fullChat = ref([]);
const chatRoom = ref(null);
const enableInput = ref(false);
const textAreaReff = ref()
const currentStream = ref(null);
const { height } = useWindowSize()


const saveInDatabase = (role, content) => {
    router.post('/message', { role, content, chat_room_id: chatRoom.value.id });
}

const send = async () => {
    if (message.value.length == 0)
        return;
    const mes = message.value;
    enableInput.value = true;
    message.value = '';
    saveInDatabase('user', mes);
    fullChat.value.push({ role: 'user', content: mes });

    const messagesToSend = fullChat.value.length > 4 ? fullChat.value.slice(fullChat.value.length - 4, fullChat.value.length) : fullChat.value;

    messagesToSend.map(m => m.content.slice(0, max_tokkens_context_message.value));

    currentStream.value = await openai.chat.completions.create({
        messages: messagesToSend,
        stream: true,
        ...chatSettings.value
    });
    fullChat.value.push({ role: 'assistant', content: '' });
    for await (const chunk of currentStream.value) {
        if (breakStream.value)
            break;
        if (chunk.choices[0].delta.content)
            fullChat.value[fullChat.value.length - 1].content += chunk.choices[0].delta.content;
    }
    saveInDatabase(fullChat.value[fullChat.value.length - 1].role, fullChat.value[fullChat.value.length - 1].content);
    enableInput.value = false;
    breakStream.value = false;
    currentStream.value = null;
}

const breakStream = ref(false);

const stopStream = () => {
    breakStream.value = true;
}

const autoResize = () => {
    textAreaReff.value.style.height = 'auto';
    if (textAreaReff.value.scrollHeight <= 200) {
        textAreaReff.value.style.height = textAreaReff.value.scrollHeight - 38 + 'px';
    } else {
        textAreaReff.value.style.height = '180px';
    }

}

</script>

<template>
    <Head title="ChatGpt" />
    <div class="text-white h-full">
        <div class="h-full">
            <div class="flex flex-col h-full">
                <div class="flex-1 h-full"
                    :class="{ ' mt-[40px] sm:mt-0 scrollbar-hide sm:scrollbar-default border-b overflow-auto w-screen sm:w-full': true, '': fullChat.length == 0 }">
                    <div class=" flex flex-col justify-center w-full">
                        <div class="mb-2" v-for="(item, index) in fullChat" :key="index">
                            <div
                                :class="{ 'bg-[hsl(0,0%,15%)]': !item.role == 'assistant', 'bg-[hsl(0,0%,30%)]': item.role == 'assistant' }">
                                <div v-if="item.role == 'assistant'" class="p-2 rounded">
                                    <div class="flex items-start justify-center gap-4">
                                        <svg-icon type="mdi" :path="mdiRobot" size="32"
                                            class="min-w-[10%] text-red-500"></svg-icon>
                                        <div class="w-full">
                                            {{ item.content }}
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="rounded min-w-full p-2">
                                    <div class="flex items-start justify-center gap-4">
                                        <svg-icon type="mdi" size="32" :path="mdiAlphaABox"
                                            class="min-w-[10%] text-green-500">
                                        </svg-icon>
                                        <div class="w-full">
                                            {{ item.content }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="currentStream" class="sticky  bottom-0 max-w-[100px] float-right mr-10  flex items-center hover:cursor-pointer hover:scale-105 hover:animate-none active:animate-ping animate-pulse border-[hsl(0,0%,70%)]  rounded border px-3 py-1"
                        @click="stopStream">
                        <SvgIcon type="mdi" :path="mdiStop" size="32" />
                        Stop
                    </div>
                </div>
                <div class="shrink-0 sm:px-4 w-full mb-2 sm:flex sm:py-4 bg-[hsl(0,0%,10%)]" :aria-disabled="enableInput">
                    <div class="sm:flex gap-4 w-full relative items-end sm:mx-0 ">
                        <div class="flex rounded border relative  w-full overflow-auto ">
                            <textarea :rows="3" ref="textAreaReff" id="message" type="text" @input="autoResize"
                                @keydown.enter.exact="send" @keydown.enter.shift.exact="text += '\n'" rows="1"
                                class=" border max-h-[10%] sm:max-h-none p-2  h-[50px] w-full border-none  focus:ring-0  bg-transparent "
                                style="resize:none;" v-model="message" :disabled="enableInput || !chatRoom" />
                            <svg-icon @click="send" color="red" size="36"
                                :class="{ 'self-center text-white w-[80px] mx-2 p-1 rounded': true, 'bg-blue-500  hover:cursor-pointer': message.length != 0 }"
                                type="mdi" :path="mdiSend">
                            </svg-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
