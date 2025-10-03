<template>
    <div
        :class="[
            'bg-white shadow-sm sm:rounded-lg p-4 w-full transition-all duration-200',
            { 'border-l-4 border-blue-500 bg-blue-50': isAI }
        ]"
    >
        <div class="flex items-start space-x-3">
            <!-- Avatar -->
            <div
                :class="[
                    'flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-white font-semibold text-sm',
                    isAI ? 'bg-blue-500' : 'bg-gray-500'
                ]"
            >
                {{ isAI ? '🤖' : avatarText }}
            </div>

            <!-- Message Content -->
            <div class="flex-1 min-w-0">
                <!-- Header -->
                <div class="flex items-center space-x-2 mb-1">
                    <span
                        :class="[
                            'font-semibold text-sm',
                            isAI ? 'text-blue-700' : 'text-gray-900'
                        ]"
                    >
                        {{ message.user.name }}
                    </span>
                    <span
                        v-if="isAI"
                        class="px-2 py-0.5 bg-blue-100 text-blue-800 text-xs rounded-full"
                    >
                        AI
                    </span>
                    <span class="text-xs text-gray-500">
                        {{ formattedTime }}
                    </span>
                </div>

                <!-- Message Text -->
                <div
                    :class="[
                        'text-sm break-words',
                        isAI ? 'text-blue-900' : 'text-gray-800'
                    ]"
                >
                    {{ message.message }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        message: {
            type: Object,
            required: true
        }
    },

    computed: {
        isAI() {
            return this.message.user.is_ai || this.message.is_ai_message || false;
        },

        avatarText() {
            return this.message.user.name.charAt(0).toUpperCase();
        },

        formattedTime() {
            if (this.message.timestamp) {
                return new Date(this.message.timestamp).toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }
            return new Date().toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    }
}
</script>
