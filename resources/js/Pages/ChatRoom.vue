<template>
    <Head title="Dashboard" />

    <BreezeAuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Chat room: {{ room }}
                    </h2>
                    <div class="mt-1">
                        Shareable link: <code class="bg-yellow-300 rounded-lg px-2 py-0.5 select-all text-sm">{{ link }}</code>
                    </div>
                </div>
                <div class="text-sm text-gray-600">
                    {{ users.length }} {{ users.length === 1 ? 'user' : 'users' }} online
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex space-x-6">
                    <!-- Sidebar -->
                    <div class="w-80 flex-shrink-0 space-y-4">
                        <!-- AI Toggle -->
                        <AIToggle
                            :room="room"
                            :initial-enabled="ai_enabled"
                            @ai-toggled="onAIToggled"
                        />

                        <!-- Users List -->
                        <div class="bg-white shadow-sm sm:rounded-lg">
                            <div class="px-4 py-3 border-b border-gray-200">
                                <h3 class="text-sm font-medium text-gray-900">Online Users</h3>
                            </div>
                            <div class="p-3">
                                <div
                                    v-for="user in users"
                                    :key="user.id"
                                    class="flex items-center space-x-2 py-1"
                                >
                                    <div
                                        :class="[
                                            'w-2 h-2 rounded-full',
                                            user.is_ai ? 'bg-blue-500' : 'bg-green-500'
                                        ]"
                                    ></div>
                                    <span
                                        :class="[
                                            'text-sm',
                                            $page.props.auth.user.id === user.id ? 'font-bold text-blue-600' : 'text-gray-700'
                                        ]"
                                    >
                                        {{ user.name }}
                                        <span v-if="user.is_ai" class="text-xs text-blue-500">(AI)</span>
                                        <span v-if="$page.props.auth.user.id === user.id" class="text-xs text-blue-500">(You)</span>
                                    </span>
                                </div>
                                <div v-if="aiEnabled && !users.some(u => u.is_ai)" class="flex items-center space-x-2 py-1 opacity-60">
                                    <div class="w-2 h-2 rounded-full bg-blue-400"></div>
                                    <span class="text-sm text-gray-500">AI Assistant (Ready)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chat Area -->
                    <div class="flex-1 flex flex-col">
                        <!-- Messages Container -->
                        <div
                            ref="messagesContainer"
                            class="flex-1 space-y-3 overflow-y-auto bg-gray-50 rounded-lg p-4"
                            style="height: 500px;"
                        >
                            <!-- Welcome Message -->
                            <div v-if="lines.length === 0" class="text-center py-8">
                                <div class="text-gray-500 text-sm">
                                    <div class="text-4xl mb-2">💬</div>
                                    <div>Welcome to the chat room!</div>
                                    <div class="mt-1">Start a conversation...</div>
                                </div>
                            </div>

                            <!-- Messages -->
                            <template v-for="(line, i) in lines" :key="i">
                                <!-- System Messages -->
                                <div
                                    v-if="line.type === 'system'"
                                    class="text-center"
                                >
                                    <span class="inline-block px-3 py-1 bg-gray-200 rounded-full text-xs text-gray-600 italic">
                                        {{ line.message }}
                                    </span>
                                </div>

                                <!-- Error Messages -->
                                <div
                                    v-else-if="line.type === 'error'"
                                    class="text-center"
                                >
                                    <span class="inline-block px-3 py-1 bg-red-100 rounded-full text-xs text-red-600">
                                        ⚠ {{ line.message }}
                                    </span>
                                </div>

                                <!-- Regular Messages -->
                                <MessageItem
                                    v-else-if="line.type === 'message'"
                                    :message="line"
                                />
                            </template>

                            <!-- Typing Indicator -->
                            <TypingIndicator :show="aiTyping" />
                        </div>

                        <!-- Message Input -->
                        <div class="mt-4 bg-white rounded-lg shadow-sm border">
                            <div class="p-4">
                                <div class="flex space-x-3">
                                    <div class="flex-1">
                                        <breeze-input
                                            v-model="message"
                                            type="text"
                                            class="w-full"
                                            :placeholder="getPlaceholderText()"
                                            @keyup.enter="sendMessage"
                                            :disabled="sendingMessage"
                                        />
                                    </div>
                                    <button
                                        @click="sendMessage"
                                        :disabled="!message.trim() || sendingMessage"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                    >
                                        <span v-if="sendingMessage">...</span>
                                        <span v-else>Send</span>
                                    </button>
                                </div>
                                <div v-if="aiEnabled" class="mt-2 text-xs text-gray-500">
                                    💡 Tip: Start with <code class="bg-gray-100 px-1 rounded">@ai</code> to talk to the AI assistant
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue'
import BreezeInput from '@/Components/Input.vue'
import MessageItem from '@/Components/MessageItem.vue'
import TypingIndicator from '@/Components/TypingIndicator.vue'
import AIToggle from '@/Components/AIToggle.vue'
import { Head } from '@inertiajs/inertia-vue3';

export default {
    components: {
        BreezeAuthenticatedLayout,
        BreezeInput,
        MessageItem,
        TypingIndicator,
        AIToggle,
        Head,
    },

    props: [
        'room',
        'link',
        'ai_enabled',
    ],

    data() {
        return {
            lines: [],
            users: [],
            message: '',
            aiEnabled: this.ai_enabled || false,
            aiTyping: false,
            sendingMessage: false,
        };
    },

    mounted() {
        this.initializeEcho();
        this.addWelcomeMessage();
    },

    methods: {
        initializeEcho() {
            window.Echo
                .join(`room.${this.room}`)
                .here(users => {
                    this.users = users;
                    this.scrollToBottom();
                })
                .joining(user => {
                    this.users.push(user);
                    this.systemMessage(`${user.name} joined the channel.`);
                })
                .leaving(user => {
                    this.users.splice(this.users.findIndex(u => u.id === user.id), 1);
                    this.systemMessage(`${user.name} left the channel.`);
                })
                .error((error) => {
                    this.errorMessage(`Connection error: ${JSON.stringify(error)}`);
                })
                .listen('.room.message', (data) => {
                    const { message, user, is_ai_message } = data;

                    // Hide typing indicator if AI message
                    if (is_ai_message) {
                        this.aiTyping = false;
                    }

                    this.userMessage(message, user, is_ai_message);
                });
        },

        addWelcomeMessage() {
            this.systemMessage('Welcome to the chat room! You can now start chatting.');
            if (this.aiEnabled) {
                this.systemMessage('AI Assistant is enabled. Use @ai to interact with it.');
            }
        },

        systemMessage(message) {
            this.lines.push({
                message,
                type: 'system',
                timestamp: new Date().toISOString()
            });
            this.scrollToBottom();
        },

        errorMessage(message) {
            this.lines.push({
                message,
                type: 'error',
                timestamp: new Date().toISOString()
            });
            this.scrollToBottom();
        },

        userMessage(message, user, isAI = false) {
            this.lines.push({
                message,
                user: {
                    ...user,
                    is_ai: isAI || user.is_ai || false
                },
                type: 'message',
                is_ai_message: isAI,
                timestamp: new Date().toISOString()
            });
            this.scrollToBottom();
        },

        async sendMessage() {
            if (!this.message.trim() || this.sendingMessage) return;

            const messageText = this.message.trim();
            this.message = '';
            this.sendingMessage = true;

            // Show AI typing indicator if message is for AI
            if (this.aiEnabled && this.isMessageForAI(messageText)) {
                this.aiTyping = true;
            }

            try {
                await axios.post(this.route('send.message'), {
                    room: this.room,
                    message: messageText
                });
            } catch (error) {
                console.error('Error sending message:', error);
                this.errorMessage('Failed to send message. Please try again.');
                this.aiTyping = false;
            } finally {
                this.sendingMessage = false;
            }
        },

        isMessageForAI(message) {
            const aiTriggers = ['@ai', '@bot', 'ai:', 'bot:', 'hey ai'];
            const lowerMessage = message.toLowerCase();
            return aiTriggers.some(trigger => lowerMessage.includes(trigger));
        },

        getPlaceholderText() {
            if (this.aiEnabled) {
                return "Type your message... (Use @ai to chat with AI)";
            }
            return "Type your message and press ENTER...";
        },

        onAIToggled(enabled) {
            this.aiEnabled = enabled;
            if (enabled) {
                this.systemMessage('AI Assistant has been enabled for this room.');
            } else {
                this.systemMessage('AI Assistant has been disabled for this room.');
                this.aiTyping = false;
            }
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
