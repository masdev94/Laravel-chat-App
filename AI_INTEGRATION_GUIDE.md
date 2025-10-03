# Laravel Chat App with AI Integration 🚀

A modern real-time chat application built with Laravel, Vue.js 3, Soketi WebSocket server, and OpenAI integration.

## ✨ Features

### Core Chat Features
- **Real-time messaging** via WebSockets (Soketi)
- **Multiple chat rooms** with shareable links
- **User presence indicators** (online/offline status)
- **Responsive design** with Tailwind CSS
- **Laravel Breeze authentication**

### AI Integration Features
- **OpenAI GPT integration** for intelligent chat responses
- **Per-room AI toggle** - enable/disable AI for each chat room
- **Smart AI triggers** - multiple ways to interact with AI
- **Real-time AI responses** via WebSockets
- **Typing indicators** when AI is generating responses
- **Enhanced UI** for AI messages with special styling

## 🛠️ Installation

### Prerequisites
- PHP 8.0+
- Composer
- Node.js 16+ & NPM
- SQLite (or MySQL/PostgreSQL)
- OpenAI API key (for AI features)

### Quick Setup

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd laravel-chat-app
   ```

2. **Run the setup script:**
   ```bash
   # Linux/Mac
   chmod +x setup.sh
   ./setup.sh
   
   # Windows
   setup.bat
   ```

3. **Configure OpenAI (Required for AI features):**
   - Get an API key from [OpenAI Platform](https://platform.openai.com/)
   - Add to your `.env` file:
     ```env
     OPENAI_API_KEY=your-actual-api-key-here
     OPENAI_MODEL=gpt-3.5-turbo
     ```

4. **Start the servers:**
   ```bash
   # Terminal 1: Laravel server
   php artisan serve
   
   # Terminal 2: Soketi WebSocket server
   soketi start
   ```

5. **Visit the app:**
   - Go to `http://127.0.0.1:8000`
   - Login with: `test@test.com` / `password`

### Manual Installation

If the setup script doesn't work, follow these steps:

```bash
# Install dependencies
composer install --ignore-platform-reqs
npm install

# Environment setup
cp .env.example .env
touch database/database.sqlite
php artisan key:generate

# Database setup
php artisan migrate:fresh --seed
php artisan storage:link

# Build assets
npm run dev
```

## 🤖 AI Features Usage

### Enabling AI in Chat Rooms

1. **Join a chat room** (or create a new one by visiting any URL like `/my-room`)
2. **Click the AI toggle** in the sidebar to enable AI for that room
3. **Start chatting with AI** using the triggers below

### AI Interaction Triggers

The AI will respond when you use any of these patterns:

- `@ai your message here`
- `@bot your question`  
- `ai: what do you think?`
- `bot: help me with this`
- `hey ai, can you help?`

### Examples

```
@ai What's the weather like today?
@bot Can you help me with JavaScript?
ai: Tell me a joke
hey ai, what do you recommend for dinner?
```

## 🧪 Testing the AI Integration

Test if your OpenAI integration is working:

```bash
php artisan ai:test "Hello, how are you?"
```

This command will test the AI service directly without the chat interface.

## 🏗️ Architecture

### Backend (Laravel)
- **Controllers**: Handle HTTP requests and WebSocket integration
- **Events**: Broadcast messages to WebSocket clients
- **Jobs**: Process AI responses asynchronously  
- **Services**: OpenAI integration and message processing
- **Routes**: API endpoints for chat and AI management

### Frontend (Vue.js 3)
- **Components**: Modular UI components for messages, AI toggle, typing indicators
- **Real-time**: Laravel Echo + Pusher.js for WebSocket communication
- **Responsive**: Tailwind CSS for modern, mobile-friendly design

### WebSocket (Soketi)
- **Presence channels**: Track users in each room
- **Real-time events**: Instant message delivery
- **Broadcasting**: Server-to-client event propagation

### AI Integration (OpenAI)
- **GPT-3.5-turbo**: Smart, context-aware responses
- **Trigger detection**: Automatic AI activation based on message content
- **Error handling**: Graceful fallbacks when AI is unavailable

## 📁 Key Files

```
app/
├── Services/OpenAIService.php          # AI integration logic
├── Jobs/ProcessAIResponse.php          # Async AI response processing
├── Events/AIMessage.php                # AI message broadcasting
├── Http/Controllers/
│   ├── AIController.php               # AI management endpoints
│   └── SendMessageController.php      # Message handling with AI
└── Console/Commands/TestAICommand.php  # AI testing command

resources/js/
├── Pages/ChatRoom.vue                  # Main chat interface
└── Components/
    ├── MessageItem.vue                # Individual message display
    ├── TypingIndicator.vue            # AI typing animation
    └── AIToggle.vue                   # AI enable/disable control
```

## 🚀 Deployment

### Production Considerations

1. **Queue Workers**: Use Redis/database queues for AI processing:
   ```bash
   QUEUE_CONNECTION=redis
   php artisan queue:work
   ```

2. **WebSocket Server**: Deploy Soketi with proper SSL:
   ```bash
   soketi start --config=soketi.config.json
   ```

3. **Environment Variables**: Set production values:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   OPENAI_API_KEY=your-production-key
   BROADCAST_DRIVER=pusher
   ```

## 🐛 Troubleshooting

### Common Issues

**AI not responding:**
- Check OpenAI API key in `.env`
- Verify API key has sufficient credits
- Test with: `php artisan ai:test`

**WebSocket connection failed:**
- Ensure Soketi is running: `soketi start`
- Check browser console for connection errors
- Verify PUSHER_* credentials match

**Messages not appearing:**
- Check Laravel logs: `tail -f storage/logs/laravel.log`
- Verify database connection
- Ensure queue worker is running (if using queues)

### Debug Commands

```bash
# Test AI integration
php artisan ai:test "Hello world"

# Check queue jobs
php artisan queue:failed

# Clear caches
php artisan config:clear
php artisan cache:clear
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/amazing-feature`
3. Make your changes and test thoroughly
4. Commit your changes: `git commit -m 'Add amazing feature'`
5. Push to the branch: `git push origin feature/amazing-feature`
6. Open a Pull Request

## 📝 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## 🙋‍♂️ Support

If you encounter any issues or have questions:

1. Check the troubleshooting section above
2. Search existing GitHub issues
3. Create a new issue with:
   - Laravel version
   - PHP version  
   - Error messages/logs
   - Steps to reproduce

---

**Happy chatting with AI! 🤖💬**
