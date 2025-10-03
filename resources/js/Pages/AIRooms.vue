<template>
    <Head title="AI Chat Rooms" />

    <BreezeAuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        🤖 AI Chat Rooms
                    </h2>
                    <p class="text-gray-600 text-sm mt-1">
                        Have persistent conversations with AI assistants
                    </p>
                </div>
                <button
                    @click="showCreateModal = true"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors"
                >
                    ➕ New AI Room
                </button>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Quick Access Regular Chat -->
                <div class="mb-6">
                    <div class="bg-gradient-to-r from-purple-100 to-pink-100 rounded-lg p-4 border border-purple-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-purple-800">💬 Regular Chat Rooms</h3>
                                <p class="text-purple-600 text-sm">Chat with other users in real-time</p>
                            </div>
                            <a
                                :href="route('chat.room', { room: 'general' })"
                                class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                            >
                                Join General Chat
                            </a>
                        </div>
                    </div>
                </div>

                <!-- AI Rooms Grid -->
                <div v-if="ai_rooms.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="room in ai_rooms"
                        :key="room.id"
                        class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow cursor-pointer"
                        @click="enterRoom(room.room_id)"
                    >
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <div class="text-2xl">
                                        {{ getPersonalityIcon(room.ai_settings?.personality) }}
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">{{ room.title }}</h3>
                                        <p class="text-sm text-gray-500">
                                            {{ getPersonalityName(room.ai_settings?.personality) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex space-x-1">
                                    <button
                                        @click.stop="editRoom(room)"
                                        class="p-1 text-gray-400 hover:text-gray-600 rounded"
                                        title="Edit Room"
                                    >
                                        ✏️
                                    </button>
                                    <button
                                        @click.stop="deleteRoom(room)"
                                        class="p-1 text-gray-400 hover:text-red-600 rounded"
                                        title="Delete Room"
                                    >
                                        🗑️
                                    </button>
                                </div>
                            </div>

                            <p v-if="room.description" class="text-gray-600 text-sm mb-4">
                                {{ room.description }}
                            </p>

                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <span>{{ room.ai_settings?.model || 'gpt-3.5-turbo' }}</span>
                                <span>{{ formatDate(room.last_activity_at || room.updated_at) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-12">
                    <div class="text-6xl mb-4">🤖</div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No AI Rooms Yet</h3>
                    <p class="text-gray-600 mb-6">Create your first AI chat room to start having persistent conversations with AI assistants.</p>
                    <button
                        @click="showCreateModal = true"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors"
                    >
                        Create Your First AI Room
                    </button>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <div
            v-if="showCreateModal || editingRoom"
            class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
            @click="closeModal"
        >
            <div
                class="relative top-20 mx-auto p-5 border w-full max-w-lg bg-white rounded-lg shadow-lg"
                @click.stop
            >
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ editingRoom ? 'Edit AI Room' : 'Create New AI Room' }}
                    </h3>
                </div>

                <form @submit.prevent="submitForm">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Room Title</label>
                        <input
                            v-model="form.title"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="e.g., Creative Writing Assistant"
                            required
                        />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description (Optional)</label>
                        <textarea
                            v-model="form.description"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            rows="3"
                            placeholder="What will you use this AI assistant for?"
                        ></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">AI Personality</label>
                        <select
                            v-model="form.personality"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required
                        >
                            <option v-for="(info, key) in personalities" :key="key" :value="key">
                                {{ info.icon }} {{ info.name }} - {{ info.description }}
                            </option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">AI Model</label>
                        <select
                            v-model="form.model"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required
                        >
                            <option value="gpt-3.5-turbo">GPT-3.5 Turbo (Faster, Cheaper)</option>
                            <option value="gpt-4">GPT-4 (More Advanced, Slower)</option>
                        </select>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button
                            type="button"
                            @click="closeModal"
                            class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="submitting"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors disabled:opacity-50"
                        >
                            {{ submitting ? 'Saving...' : (editingRoom ? 'Update Room' : 'Create Room') }}
                        </button>
                    </div>
                </form>
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
        'ai_rooms',
        'personalities',
    ],

    data() {
        return {
            showCreateModal: false,
            editingRoom: null,
            submitting: false,
            form: {
                title: '',
                description: '',
                personality: 'helpful_assistant',
                model: 'gpt-3.5-turbo',
            }
        }
    },

    methods: {
        enterRoom(roomId) {
            window.location.href = this.route('ai.room', { roomId });
        },

        editRoom(room) {
            this.editingRoom = room;
            this.form = {
                title: room.title,
                description: room.description || '',
                personality: room.ai_settings?.personality || 'helpful_assistant',
                model: room.ai_settings?.model || 'gpt-3.5-turbo',
            };
        },

        async deleteRoom(room) {
            if (!confirm(`Are you sure you want to delete "${room.title}"? This will remove all chat history.`)) {
                return;
            }

            try {
                await axios.delete(this.route('ai.room.destroy', { roomId: room.room_id }));

                // Remove from list
                const index = this.ai_rooms.findIndex(r => r.id === room.id);
                if (index !== -1) {
                    this.ai_rooms.splice(index, 1);
                }
            } catch (error) {
                console.error('Error deleting room:', error);
                alert('Failed to delete room. Please try again.');
            }
        },

        async submitForm() {
            if (this.submitting) return;

            this.submitting = true;

            try {
                if (this.editingRoom) {
                    // Update existing room
                    const response = await axios.put(
                        this.route('ai.room.update', { roomId: this.editingRoom.room_id }),
                        this.form
                    );

                    // Update room in list
                    const index = this.ai_rooms.findIndex(r => r.id === this.editingRoom.id);
                    if (index !== -1) {
                        this.ai_rooms[index] = response.data.room;
                    }
                } else {
                    // Create new room
                    const response = await axios.post(this.route('ai.rooms.store'), this.form);

                    if (response.data.redirect_url) {
                        window.location.href = response.data.redirect_url;
                        return;
                    }
                }

                this.closeModal();
            } catch (error) {
                console.error('Error saving room:', error);
                alert('Failed to save room. Please try again.');
            } finally {
                this.submitting = false;
            }
        },

        closeModal() {
            this.showCreateModal = false;
            this.editingRoom = null;
            this.form = {
                title: '',
                description: '',
                personality: 'helpful_assistant',
                model: 'gpt-3.5-turbo',
            };
        },

        getPersonalityIcon(personality) {
            return this.personalities[personality]?.icon || '🤖';
        },

        getPersonalityName(personality) {
            return this.personalities[personality]?.name || 'AI Assistant';
        },

        formatDate(dateString) {
            if (!dateString) return 'Never';

            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));

            if (diffDays === 0) {
                return 'Today';
            } else if (diffDays === 1) {
                return 'Yesterday';
            } else if (diffDays < 7) {
                return `${diffDays} days ago`;
            } else {
                return date.toLocaleDateString();
            }
        }
    }
}
</script>
