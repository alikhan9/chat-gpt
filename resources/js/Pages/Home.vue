<script setup>
import { Head, usePage, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue'
import OpenAI from "openai";
import TextInput from '@/Components/TextInput.vue';
import SvgIcon from '@jamescoyle/vue-icon'
import { mdiArrowRightBox, mdiRobot, mdiAlphaABox } from '@mdi/js'


onMounted(() => {
    if (usePage().props.chat.activeChatRoom != null) {
        fullChat.value = usePage().props.chat.activeChatRoom.messages.map(c => { return { content: c.content, role: c.role } });
        chatRoom.value = { id: usePage().props.chat.activeChatRoom.id, name: usePage().props.chat.activeChatRoom.name };
    }
})

const openai = new OpenAI({
    apiKey: import.meta.env.VITE_API_KEY,
    dangerouslyAllowBrowser: true,
});

const message = ref();
const fullChat = ref([]);
const chatRoom = ref(null);
const enableInput = ref(false);

const response = ref('');

const saveInDatabase = (role, content) => {
    router.post('/message', { role, content, chat_room_id: chatRoom.value.id });
}

const send = async () => {
    const mes = message.value;
    enableInput.value = true;
    message.value = '';
    saveInDatabase('user', mes);
    fullChat.value.push({ role: 'user', content: mes });

    const messagesToSend = fullChat.value.length > 4 ? fullChat.value.slice(fullChat.value.length - 5, fullChat.value.length - 1) : fullChat.value;

    const completion = await openai.chat.completions.create({
        messages: messagesToSend,
        model: "gpt-3.5-turbo",
        stream: true,
    });
    fullChat.value.push({ role: 'assistant', content: '' });
    for await (const chunk of completion) {
        if (chunk.choices[0].delta.content)
            fullChat.value[fullChat.value.length - 1].content += chunk.choices[0].delta.content;
    }
    saveInDatabase(fullChat.value[fullChat.value.length - 1].role, fullChat.value[fullChat.value.length - 1].content);
    enableInput.value = false;
}

</script>

<template>
    <Head title="ChatGpt" />
    <div class="text-white flex justify-center">
        <div class="container relative min-h-[98vh]">
            <div>
                <div class="flex gap-4 absolute bottom-0 w-full max-h-[500px] justify-center" :aria-disabled="enableInput">
                    <textarea id="message" type="text" @keydown.enter.exact="send" @keydown.enter.shift.exact="text += '\n'"
                        rows="1" class="min-w-[90%] border overflow-auto rounded focus:ring-0  bg-transparent "
                        style="resize: vertical; overflow: auto;" v-model="message" :disabled="enableInput" />
                    <button @click="send" class="bg-green-500 text-white px-4 py-2 rounded" :disabled="enableInput">
                        Envoyer
                    </button>
                </div>
                <div class="p-4">
                    <div class="mb-2" v-for="(item, index) in fullChat" :key="index">
                        <div v-if="item.role == 'assistant'" class="bg-[hsl(0,0%,15%)]  p-2 rounded">
                            <div class="flex items-center">
                                <svg-icon type="mdi" :path="mdiRobot" class="min-w-[5%] text-red-500"></svg-icon>
                                <div>
                                    {{ item.content }}
                                </div>
                            </div>
                        </div>
                        <div v-else class="bg-[hsl(0,0%,30%)] p-2 rounded">
                            <div class="flex items-center">
                                <svg-icon type="mdi" :path="mdiAlphaABox" class="min-w-[5%] text-green-500"></svg-icon>
                                <div class="font-bold">
                                    {{ item.content }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>
