<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                {{ $page.props.auth.user.name }}
            </h2>
        </template>

        <div class="mt-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white sm:rounded-lg shadow-lg"
                >
                    <div class="text-gray-900 flex">
                        <div class="border-r-2 p-3 shadow w-1/4">
                            <h1 class="text-2xl font-bold">Discussion</h1>
                            <div class="mt-5">
                                <h1 class="text-xl font-bold mb-3">Mes contacts</h1>

                               <div :class="'cursor-pointer py-2 font-mono font-bold border-b shadow-sm hover:bg-gray-100 ' + (contact.id === form.receive_id ? 'bg-gray-100' : '')"
                                    v-for="contact in $page.props.contacts"
                                    @click="showChat(contact)"
                               >
                                   {{ contact.name }}
                               </div>
                            </div>
                        </div>

                        <div class="w-3/4">
                            <div class="header border-b shadow p-3">
                                <h1 class="text-xl font-bold">
                                    {{ contactSelected ? contactSelected.name : 'Sélectionner un contact' }}
                                </h1>
                            </div>

                            <div class="body px-3 overflow-y-auto">
                                <div ref="messagesContainer" class="p-4 h-[500px] overflow-y-auto">
                                    <div
                                        v-for="message in messages"
                                        :key="message.id"
                                        class="flex items-center mb-2"
                                    >
                                        <div
                                            v-if="message.send_id === $page.props.auth.user.id"
                                            class="p-2 ml-auto text-white bg-blue-500 rounded-lg"
                                        >
                                            {{ message.value }}
                                        </div>
                                        <div v-else class="p-2 mr-auto bg-gray-200 rounded-lg">
                                            {{ message.value }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="footer border-t shadow p-3">
                                <form @submit.prevent="sendMessage">
                                    <div class="flex gap-3">
                                        <input type="text"
                                               class="w-full border rounded p-2"
                                               placeholder="Message"
                                               v-model="form.value"
                                        >
                                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                            Envoyer
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm, usePage} from '@inertiajs/vue3';
import {nextTick, onMounted, ref, watch} from "vue";

let messages = ref({});

let form = useForm({
    value: '',
    type: 'text',
    receive_id: '',
    group_id: '',
});

let contactSelected = ref('');
const messagesContainer = ref(null);
const isFriendTyping = ref(false);
const isFriendTypingTimer = ref(null);

const showChat = (contact) => {
    contactSelected.value = contact;
    form.receive_id = contact.id;

    // Get Messages
    axios.get(route('messages', {
        receive_id: contact.id,
        send_id: usePage().props.auth.user.id,
    })).then((response) => {
        messages.value = response.data.messages;
        console.log(messages.value);
    });
}

const sendMessage = () => {
    form.post(route('messages:store'), {
        onFinish: () => {
            form.reset();
            form.value = '';
            sendTypingEvent();
            showChat(contactSelected.value);
        },
    });
}

watch(messages, () => {
        nextTick(() => {
            messagesContainer.value.scrollTo({
                bottom: messagesContainer.value.scrollHeight,
                behavior: "smooth",
            });
        });
    },
    { deep: true }
);

const sendTypingEvent = () => {
    Echo.private(`chat.${contactSelected.id}`).whisper("typing", {
        userID: usePage().props.auth.user.id,
    });
};

onMounted(() => {
    Echo.private(`chat.${contactSelected.id}`)
        .listen("MessageSent", (response) => {
            messages.value.push(response.message);
        })
        .listenForWhisper("typing", (response) => {
            isFriendTyping.value = response.userID === usePage().props.auth.user.id;

            if (isFriendTypingTimer.value) {
                clearTimeout(isFriendTypingTimer.value);
            }

            isFriendTypingTimer.value = setTimeout(() => {
                isFriendTyping.value = false;
            }, 1000);
        });
});
</script>
