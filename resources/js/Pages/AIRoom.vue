<template>
    <Head :title="room.title" />

    <BreezeAuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a
                        :href="route('ai.rooms.index')"
                        class="text-gray-500 hover:text-gray-700 text-xl"
                        title="Back to AI Rooms"
                    >
                        ←
                    </a>
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="text-2xl">{{ getPersonalityIcon() }}</span>
                            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                                {{ room.title }}
                            </h2>
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                {{ getPersonalityName() }}
                            </span>
                        </div>
                        <p v-if="room.description" class="text-gray-600 text-sm mt-1">
                            {{ room.description }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button
                        @click="showSettings = !showSettings"
                        class="p-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100"
                        title="Room Settings"
                    >
                        ⚙️
                    </button>
                    <button
                        @click="clearHistory"
                        class="p-2 text-gray-500 hover:text-red-600 rounded-lg hover:bg-gray-100"
                        title="Clear Chat History"
                    >
                        🗑️
                    </button>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Settings Panel -->
                <div v-if="showSettings" class="mb-6 bg-white rounded-lg shadow-sm border p-4">
                    <h3 class="font-medium text-gray-900 mb-3">Room Settings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">AI Model:</span>
                            <span class="ml-2 font-medium">{{ room.ai_settings?.model || 'gpt-3.5-turbo' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Personality:</span>
                            <span class="ml-2 font-medium">{{ getPersonalityName() }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Messages:</span>
                            <span class="ml-2 font-medium">{{ messages.length }}</span>
                        </div>
                    </div>
                </div>

                <!-- Chat Container -->
                <div class="bg-white rounded-lg shadow-sm border">
                    <!-- Messages Area -->
                    <div
                        ref="messagesContainer"
                        class="h-96 overflow-y-auto p-4 space-y-4 bg-gray-50"
                        style="scroll-behavior: smooth;"
                    >
                        <!-- Welcome Message -->
                        <div v-if="messages.length === 0" class="text-center py-8">
                            <div class="text-4xl mb-4">{{ getPersonalityIcon() }}</div>
                            <div class="text-gray-600">
                                <div class="font-medium">Hello! I'm your {{ getPersonalityName() }}.</div>
                                <div class="text-sm mt-1">How can I help you today?</div>
                            </div>
                        </div>

                        <!-- Chat Messages -->
                        <div
                            v-for="(message, index) in messages"
                            :key="index"
                            class="flex space-x-3"
                            :class="{ 'flex-row-reverse space-x-reverse': message.type === 'user' }"
                        >
                            <!-- Avatar -->
                            <div
                                :class="[
                                    'flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-white font-semibold text-sm',
                                    message.type === 'user' ? 'bg-blue-500' : 'bg-green-500'
                                ]"
                            >
                                {{ message.type === 'user' ? getInitials() : getPersonalityIcon() }}
                            </div>

                            <!-- Message Bubble -->
                            <div
                                :class="[
                                    'max-w-xs lg:max-w-md px-4 py-2 rounded-lg',
                                    message.type === 'user'
                                        ? 'bg-blue-500 text-white'
                                        : 'bg-white border shadow-sm'
                                ]"
                            >
                                <div class="text-sm whitespace-pre-wrap">{{ message.content }}</div>
                                <div
                                    :class="[
                                        'text-xs mt-1',
                                        message.type === 'user' ? 'text-blue-100' : 'text-gray-500'
                                    ]"
                                >
                                    {{ formatTime(message.timestamp) }}
                                </div>
                            </div>
                        </div>

                        <!-- Typing Indicator -->
                        <div v-if="aiTyping" class="flex space-x-3">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white font-semibold text-sm">
                                {{ getPersonalityIcon() }}
                            </div>
                            <div class="bg-white border shadow-sm rounded-lg px-4 py-2">
                                <div class="flex space-x-1">
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Message Input -->
                    <div class="border-t p-4">
                        <form @submit.prevent="sendMessage" class="flex space-x-3">
                            <div class="flex-1">
                                <textarea
                                    v-model="newMessage"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                                    rows="2"
                                    placeholder="Type your message here..."
                                    :disabled="sending || aiTyping"
                                    @keydown.enter.exact.prevent="sendMessage"
                                    @keydown.enter.shift.exact="newMessage += '\n'"
                                ></textarea>
                            </div>
                            <button
                                type="submit"
                                :disabled="!newMessage.trim() || sending || aiTyping"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors self-end"
                            >
                                <span v-if="sending">⏳</span>
                                <span v-else>Send</span>
                            </button>
                        </form>
                        <div class="mt-2 text-xs text-gray-500">
                            Press Enter to send, Shift+Enter for new line
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue'
import { Head } from '@inertiajs/inertia-vue3';

export default {
    components: {
        BreezeAuthenticatedLayout,
        Head,
    },

    props: [
        'room',
        'chat_history',
        'personalities',
    ],

    data() {
        return {
            messages: [],
            newMessage: '',
            sending: false,
            aiTyping: false,
            showSettings: false,
        }
    },

    mounted() {
        this.loadChatHistory();
        this.initializeEcho();
        this.scrollToBottom();
    },

    methods: {
        loadChatHistory() {
            // Convert chat history to message format
            this.chat_history.forEach(chat => {
                // Add user message
                this.messages.push({
                    type: 'user',
                    content: chat.user_message,
                    timestamp: chat.created_at,
                });

                // Add AI response
                this.messages.push({
                    type: 'ai',
                    content: chat.ai_response,
                    timestamp: chat.created_at,
                });
            });
        },

        initializeEcho() {
            // Listen for AI responses on private channel
            window.Echo
                .private(`ai-room.${this.$page.props.auth.user.id}.${this.room.room_id}`)
                .listen('.ai.message', (data) => {
                    this.aiTyping = false;

                    // Add AI response to messages
                    this.messages.push({
                        type: 'ai',
                        content: data.ai_response,
                        timestamp: data.timestamp,
                    });

                    this.scrollToBottom();
                });
        },

        async sendMessage() {
            if (!this.newMessage.trim() || this.sending || this.aiTyping) return;

            const messageText = this.newMessage.trim();
            this.newMessage = '';
            this.sending = true;

            // Add user message to chat immediately
            this.messages.push({
                type: 'user',
                content: messageText,
                timestamp: new Date().toISOString(),
            });

            this.scrollToBottom();

            // Show AI typing indicator
            this.aiTyping = true;

            try {
                await axios.post(this.route('ai.message.send'), {
                    room_id: this.room.room_id,
                    message: messageText
                });
            } catch (error) {
                console.error('Error sending message:', error);
                this.aiTyping = false;
                alert('Failed to send message. Please try again.');
            } finally {
                this.sending = false;
            }
        },

        async clearHistory() {
            if (!confirm('Are you sure you want to clear all chat history? This cannot be undone.')) {
                return;
            }

            try {
                await axios.delete(this.route('ai.history.clear', { roomId: this.room.room_id }));
                this.messages = [];
                alert('Chat history cleared successfully.');
            } catch (error) {
                console.error('Error clearing history:', error);
                alert('Failed to clear chat history. Please try again.');
            }
        },

        getPersonalityIcon() {
            const personality = this.room.ai_settings?.personality || 'helpful_assistant';
            return this.personalities[personality]?.icon || '🤖';
        },

        getPersonalityName() {
            const personality = this.room.ai_settings?.personality || 'helpful_assistant';
            return this.personalities[personality]?.name || 'AI Assistant';
        },

        getInitials() {
            const name = this.$page.props.auth.user.name;
            return name.split(' ').map(n => n[0]).join('').toUpperCase();
        },

        formatTime(timestamp) {
            if (!timestamp) return '';

            const date = new Date(timestamp);
            return date.toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.messagesContainer;
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        }
    }
}
</script>
