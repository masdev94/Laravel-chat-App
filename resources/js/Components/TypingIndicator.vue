<template>
    <div
        v-if="show"
        class="shadow-sm sm:rounded-lg p-4 w-full border-l-4 border-blue-300 bg-blue-50"
    >
        <div class="flex items-center space-x-3">
            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold text-sm">
                🤖
            </div>
            <div class="flex-1">
                <div class="flex items-center space-x-2">
                    <span class="font-semibold text-sm text-blue-700">AI Assistant</span>
                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-xs rounded-full">AI</span>
                </div>
                <div class="flex items-center space-x-1 mt-1">
                    <span class="text-blue-600 text-sm">Thinking</span>
                    <div class="flex space-x-1">
                        <div class="w-2 h-2 bg-blue-400 rounded-full animate-bounce"></div>
                        <div class="w-2 h-2 bg-blue-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                        <div class="w-2 h-2 bg-blue-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        show: {
            type: Boolean,
            default: false
        }
    }
}
</script>
