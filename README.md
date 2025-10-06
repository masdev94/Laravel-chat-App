# Laravel Chat App with AI Integration 🤖💬

An advanced Laravel chat application featuring dual chat systems: traditional multi-user rooms and AI-powered conversation rooms with persistent memory and multiple personalities. Built with Laravel, Vue.js 3, Inertia.js, and soketi WebSockets.

## ✨ Features

### 🤖 AI Chat Rooms
- **5 AI Personalities**: Helpful Assistant, Creative Writer, Technical Expert, Tutor, and Brainstorm Partner
- **Persistent Memory**: AI remembers conversation history for contextual responses
- **Private Conversations**: Each AI room is private to the user
- **Configurable Models**: Choose between GPT-3.5-turbo (fast, cost-effective) or GPT-4 (advanced)
- **Real-time Responses**: AI messages appear instantly via WebSockets

### 💬 Traditional Chat Rooms  
- **Multi-user Rooms**: Real-time group conversations
- **Optional AI Integration**: Toggle AI assistant in any room with trigger words
- **Persistent AI Memory**: AI remembers conversation history for contextual responses
- **Online User Tracking**: See who's currently in each room
- **Shareable Links**: Easy room sharing
- **Chat History Management**: View and clear AI conversation history per room

### 🛠 Technical Features
- **Modern Stack**: Laravel 8 + Vue.js 3 + Inertia.js + Tailwind CSS
- **Real-time Communication**: Soketi WebSocket server with Pusher protocol
- **Async AI Processing**: Background job queues for non-blocking user experience
- **Private WebSocket Channels**: Secure AI room communications
- **Responsive Design**: Works on desktop and mobile devices

## 🚀 Quick Start

### Prerequisites

- **PHP 8.0+** with required extensions
- **Composer** for PHP dependency management
- **Node.js 16+** and NPM for frontend assets and soketi
- **SQLite** (default) or MySQL/PostgreSQL for database
- **OpenAI API Key** for AI features ([Get one here](https://platform.openai.com/))

### Installation

1. **Clone the repository**:
```bash
git clone https://github.com/your-username/laravel-chat-app.git
cd laravel-chat-app
```

2. **Install dependencies and setup database**:
```bash
# Install PHP dependencies
composer install --ignore-platform-reqs

# Copy environment file and create database
cp .env.example .env
touch database/database.sqlite

# Generate application key and run migrations
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
```

3. **Configure environment variables**:

Open `.env` and configure the following settings:

```env
# Database (SQLite is default, or configure MySQL/PostgreSQL)
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite

# WebSocket Configuration (soketi)
PUSHER_APP_KEY=app-key
PUSHER_APP_ID=app-id
PUSHER_APP_SECRET=app-secret
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001

MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_HOST="${PUSHER_HOST}"
MIX_PUSHER_PORT="${PUSHER_PORT}"

# OpenAI Configuration (Required for AI features) 
OPENAI_API_KEY=your-openai-api-key-here
OPENAI_MODEL=gpt-3.5-turbo

# Queue Configuration (for AI processing)
QUEUE_CONNECTION=database
```

4. **Install frontend dependencies and build assets**:
```bash
npm install
npm run dev
```

5. **Start the Laravel development server**:
```bash
php artisan serve
```
Your app will be available at `http://127.0.0.1:8000`

6. **Install and start soketi WebSocket server**:

Install soketi globally:
```bash
npm install -g @soketi/soketi@latest
```

Start soketi in a separate terminal (keep it running):
```bash
soketi start
```

7. **Start the queue worker** (for AI processing):

In another terminal, start the queue worker:
```bash
php artisan queue:work
```

## 🎯 Usage

1. **Visit** `http://127.0.0.1:8000`
2. **Register/Login** with any of the test accounts (see Authentication section)
3. **Choose your experience**:
   - **AI Rooms**: Click "AI Rooms" to create dedicated AI conversations
   - **Chat Rooms**: Click "Chat Rooms" for traditional group chat

### Using AI Rooms

1. **Create Room**: Click "➕ New AI Room"
2. **Choose Personality**: Select from 5 available AI personalities
3. **Select Model**: Choose GPT-3.5-turbo (fast) or GPT-4 (advanced)
4. **Start Chatting**: Everything you type is automatically processed by AI
5. **Persistent Memory**: AI remembers your conversation history for context

### Using Traditional Chat Rooms

1. **Join Room**: Navigate to any chat room (e.g., "general")
2. **Enable AI** (optional): Click the AI toggle to activate assistant
3. **Trigger AI**: Use `@ai`, `@bot`, or `ai:` to interact with the assistant
4. **View History**: AI chat history panel shows your conversation context
5. **Manage History**: Refresh or clear your AI conversation history
6. **Group Chat**: Chat with other users in real-time

**New AI Memory Feature**: The AI now remembers your previous conversations in each room, providing better context and continuity across sessions!

## 🤖 AI Features Deep Dive

### AI Personalities

Choose from 5 specialized AI personalities, each optimized for different use cases:

| Personality | Best For | Description |
|-------------|----------|-------------|
| 🤖 **Helpful Assistant** | General queries | Balanced, friendly, and informative responses |
| ✍️ **Creative Writer** | Creative projects | Storytelling, creative writing, and artistic inspiration |
| 💻 **Technical Expert** | Programming | Code help, debugging, technical explanations |
| 🎓 **Tutor** | Learning | Step-by-step teaching, educational content |
| 💡 **Brainstorm Partner** | Ideation | Collaborative thinking, idea generation |

### AI Models

| Model | Speed | Cost | Quality | Best For |
|-------|-------|------|---------|----------|
| **GPT-3.5-turbo** | ⚡ Fast | 💰 Cheaper | ✅ Good | General conversations, quick responses |
| **GPT-4** | 🐌 Slower | 💸 Expensive | 🌟 Excellent | Complex tasks, detailed analysis |

### Traditional Chat AI Integration

For traditional chat rooms, AI integration now includes persistent memory:

**AI Triggers:**
- `@ai your message here`
- `@bot your question`  
- `ai: what do you think?`
- `bot: help me with this`
- `hey ai, can you help?`

**How it works:**
1. Enable AI toggle in any chat room
2. Use trigger words in your messages
3. AI responds contextually using conversation history
4. AI remembers your previous interactions in each room
5. View and manage your chat history in the sidebar
6. Other users see both your message and AI response

**History Features:**
- 📚 **Persistent Memory**: AI remembers up to 8 recent conversations per room
- 🔄 **History Panel**: View your recent AI interactions
- 🗑️ **Clear History**: Remove conversation history when needed
- 📱 **Per-Room Storage**: Each room maintains separate chat history

### Technical AI Features

- 🔄 **Async Processing**: AI responses don't block the UI
- 🧠 **Context Memory**: Recent conversation history for better responses
- 🔒 **Private Channels**: AI room conversations are private
- ⚡ **Real-time**: Instant responses via WebSocket broadcasting  
- 🛡️ **Error Handling**: Graceful fallbacks when AI is unavailable
- 📊 **Usage Tracking**: Monitor API usage and costs
- 🎛️ **Configurable**: Adjust temperature, max tokens, and other parameters

## 🔐 Authentication

### Test Accounts

The application comes with pre-seeded test accounts. All accounts use the password `password`:

| Email | Name | Description |
|-------|------|-------------|
| `test@example.com` | Test User | Main test account with AI rooms |
| `test@test.com` | Test User 1 | Additional test account |
| `test2@test.com` | Test User 2 | Additional test account |
| `test3@test.com` | Test User 3 | Additional test account |

### Registration

You can also create new accounts using the registration form at `/register`.

## 🏗️ Architecture Overview

### Technology Stack

| Component | Technology | Purpose |
|-----------|------------|---------|
| **Backend** | Laravel 8 | API, authentication, database management |
| **Frontend** | Vue.js 3 + Inertia.js | Modern SPA-like experience |
| **Styling** | Tailwind CSS | Utility-first CSS framework |
| **WebSockets** | Soketi | Real-time communication |
| **Database** | SQLite/MySQL | Data persistence |
| **AI Integration** | OpenAI API | GPT-3.5-turbo and GPT-4 |
| **Queue System** | Laravel Queues | Async AI processing |

### Key Components

- **AIRoom Model**: Manages AI room data and settings
- **AIChatHistory Model**: Stores conversation history for context
- **ProcessAIRoomResponse Job**: Handles async AI API calls
- **AIRoomMessage Event**: Broadcasts AI responses via WebSockets
- **OpenAIService**: Manages AI API communication and prompt engineering

## 🛠️ Development

### Available Commands

```bash
# Frontend development
npm run dev          # Build assets for development
npm run watch        # Watch for changes and rebuild
npm run production   # Build optimized assets for production
npm run lint         # Lint Vue.js code

# Backend development  
php artisan serve    # Start development server
php artisan queue:work  # Start queue worker for AI processing
php artisan migrate  # Run database migrations
php artisan db:seed  # Seed database with test data

# New: Chat history management
php artisan db:seed --class=ChatHistorySeeder  # Seed sample chat history

# Code quality
vendor/bin/php-cs-fixer fix  # Fix PHP code style
php artisan test     # Run PHP tests (if available)
```

### WebSocket Development

```bash
# Start soketi server for real-time features
soketi start

# Alternative: Use Laravel WebSockets (if configured)
php artisan websockets:serve
```

### Testing AI Features

```bash
# Test OpenAI connection
php artisan ai:test

# Clear AI chat history (if needed)
php artisan ai:clear-history
```

## 🚀 Deployment

### Production Setup

1. **Environment Configuration**:
```env
APP_ENV=production
APP_DEBUG=false
QUEUE_CONNECTION=redis  # Use Redis for better performance
```

2. **Database Migration**:
```bash
php artisan migrate --force
```

3. **Asset Compilation**:
```bash
npm run production
```

4. **Queue Worker Setup**:
```bash
# Use supervisor or similar for production queue workers
php artisan queue:work --daemon
```

5. **WebSocket Server**:
```bash
# Configure soketi for production with SSL
soketi start --config=soketi.production.json
```

### Scaling Considerations

- **Database**: Consider PostgreSQL or MySQL for production
- **Queue Backend**: Use Redis or SQS for better queue performance  
- **WebSocket Scaling**: Multiple soketi instances with Redis adapter
- **AI Rate Limiting**: Implement user-based quotas for OpenAI API usage
- **Caching**: Add Redis caching for better performance

## 📚 Documentation

- **[Technical Architecture](TECHNICAL_ARCHITECTURE.md)**: Detailed technical decisions and trade-offs
- **API Documentation**: Available at `/docs` (if configured)
- **Database Schema**: See migration files in `database/migrations/`

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Guidelines

- Follow PSR-12 coding standards for PHP
- Use ESLint configuration for JavaScript/Vue
- Write tests for new features
- Update documentation for significant changes
- Use conventional commit messages

## 📄 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## 🙏 Acknowledgments

- **soketi** for excellent WebSocket server
- **OpenAI** for powerful AI capabilities  
- **Laravel** community for the amazing framework
- **Vue.js** team for the reactive frontend framework
