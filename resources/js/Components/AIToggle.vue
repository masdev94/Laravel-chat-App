<template>
    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold text-sm">
                🤖
            </div>
            <div>
                <div class="font-medium text-sm text-gray-900">AI Assistant</div>
                <div class="text-xs text-gray-500">
                    {{ enabled ? 'Active in this room' : 'Click to enable AI in this room' }}
                </div>
            </div>
        </div>

        <button
            @click="toggleAI"
            :disabled="loading"
            :class="[
                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2',
                enabled ? 'bg-blue-600' : 'bg-gray-200',
                loading ? 'opacity-50 cursor-not-allowed' : ''
            ]"
        >
            <span
                :class="[
                    'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                    enabled ? 'translate-x-5' : 'translate-x-0'
                ]"
            />
        </button>
    </div>

    <div v-if="enabled" class="mt-2 p-3 bg-blue-50 rounded-lg border border-blue-200">
        <div class="text-sm text-blue-800">
            <strong>AI is active!</strong> You can interact with the AI by:
        </div>
        <ul class="mt-1 text-xs text-blue-700 list-disc list-inside">
            <li>Starting your message with <code class="bg-blue-100 px-1 rounded">@ai</code></li>
            <li>Using <code class="bg-blue-100 px-1 rounded">@bot</code> or <code class="bg-blue-100 px-1 rounded">hey ai</code></li>
            <li>Starting with <code class="bg-blue-100 px-1 rounded">ai:</code> or <code class="bg-blue-100 px-1 rounded">bot:</code></li>
        </ul>
    </div>
</template>

<script>
export default {
    props: {
        room: {
            type: String,
            required: true
        },
        initialEnabled: {
            type: Boolean,
            default: false
        }
    },

    emits: ['ai-toggled'],

    data() {
        return {
            enabled: this.initialEnabled,
            loading: false
        }
    },

    methods: {
        async toggleAI() {
            if (this.loading) return;

            this.loading = true;
            const newState = !this.enabled;

            try {
                const response = await axios.post(this.route('ai.toggle'), {
                    room: this.room,
                    enabled: newState
                });

                if (response.data.ok) {
                    this.enabled = newState;
                    this.$emit('ai-toggled', newState);
                }
            } catch (error) {
                console.error('Error toggling AI:', error);
                // You could add a notification system here
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
