<script setup lang="ts">
import { ref, nextTick, watch, onMounted } from "vue";

interface Message {
  role: "user" | "assistant";
  text: string;
}

const STORAGE_KEY = "arva_chat";
const DEFAULT_MSG: Message = { role: "assistant", text: "Sveiki! Es esmu ARVA personīgais stilists. Pastāsti — ko meklē? (piem. ikdienas krekls, elegants apģērbs, dāvana...)" };

const open    = ref(false);
const input   = ref("");
const loading = ref(false);
const messagesEl = ref<HTMLElement | null>(null);

function loadMessages(): Message[] {
  try {
    const saved = localStorage.getItem(STORAGE_KEY);
    if (saved) return JSON.parse(saved);
  } catch {}
  return [DEFAULT_MSG];
}

const messages = ref<Message[]>(loadMessages());

watch(messages, (val) => {
  try { localStorage.setItem(STORAGE_KEY, JSON.stringify(val)); } catch {}
}, { deep: true });

onMounted(() => scrollDown());

function toggle() { open.value = !open.value; }

function clearChat() {
  messages.value = [DEFAULT_MSG];
  localStorage.removeItem(STORAGE_KEY);
}

async function send() {
  const text = input.value.trim();
  if (!text || loading.value) return;

  messages.value.push({ role: "user", text });
  input.value = "";
  loading.value = true;
  await scrollDown();

  const csrf = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? "";

  try {
    const res = await fetch("/ai/chat", {
      method: "POST",
      headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": csrf },
      body: JSON.stringify({
        message: text,
        history: messages.value.slice(-10, -1),
      }),
    });
    const data = await res.json();
    messages.value.push({ role: "assistant", text: data.reply });
  } catch {
    messages.value.push({ role: "assistant", text: "Radās kļūda. Lūdzu mēģini vēlreiz." });
  } finally {
    loading.value = false;
    await scrollDown();
  }
}

async function scrollDown() {
  await nextTick();
  if (messagesEl.value) {
    messagesEl.value.scrollTop = messagesEl.value.scrollHeight;
  }
}
</script>

<template>
  <div class="chat-widget">
    <!-- Poga -->
    <button class="chat-toggle" @click="toggle" :title="open ? 'Aizvērt' : 'AI Asistents'">
      <span v-if="!open">💬</span>
      <span v-else>✕</span>
    </button>

    <!-- Logs -->
    <div v-if="open" class="chat-box">
      <div class="chat-header">
        <div>
          <span class="chat-title">🛍️ ARVA Asistents</span>
          <span class="chat-subtitle">Palīdzēšu atrast ko pirkt</span>
        </div>
        <button class="chat-clear" @click="clearChat" title="Notīrīt sarunu">✕</button>
      </div>

      <div class="chat-messages" ref="messagesEl">
        <div
          v-for="(msg, i) in messages"
          :key="i"
          class="msg"
          :class="msg.role"
        >
          <div class="bubble">{{ msg.text }}</div>
        </div>

        <div v-if="loading" class="msg assistant">
          <div class="bubble typing">
            <span></span><span></span><span></span>
          </div>
        </div>
      </div>

      <div class="chat-input-row">
        <input
          v-model="input"
          placeholder="Raksti ziņu..."
          class="chat-input"
          @keyup.enter="send"
          :disabled="loading"
        />
        <button class="chat-send" @click="send" :disabled="loading || !input.trim()">
          ➤
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.chat-widget {
  position: fixed;
  bottom: 24px;
  right: 24px;
  z-index: 9999;
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 12px;
}

.chat-toggle {
  width: 52px;
  height: 52px;
  border-radius: 50%;
  background: #072536;
  color: #fff;
  border: none;
  font-size: 22px;
  cursor: pointer;
  box-shadow: 0 4px 16px rgba(7,37,54,.35);
  transition: transform .15s, background .15s;
  display: flex;
  align-items: center;
  justify-content: center;
}
.chat-toggle:hover { background: #0f3a4c; transform: scale(1.08); }

.chat-box {
  width: 320px;
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 8px 32px rgba(7,37,54,.18);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  max-height: 460px;
}

.chat-header {
  background: #072536;
  padding: 14px 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.chat-title { color: #fff; font-weight: 800; font-size: 14px; display: block; }
.chat-subtitle { color: rgba(255,255,255,.6); font-size: 11px; display: block; }
.chat-clear {
  background: none; border: none; cursor: pointer;
  font-size: 16px; opacity: .6; transition: opacity .15s; padding: 4px;
}
.chat-clear:hover { opacity: 1; }

.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 12px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  min-height: 0;
}

.msg { display: flex; }
.msg.user { justify-content: flex-end; }
.msg.assistant { justify-content: flex-start; }

.bubble {
  max-width: 85%;
  padding: 9px 13px;
  border-radius: 14px;
  font-size: 13px;
  line-height: 1.5;
  white-space: pre-wrap;
}
.msg.user .bubble {
  background: #072536;
  color: #fff;
  border-bottom-right-radius: 4px;
}
.msg.assistant .bubble {
  background: #f0f4f6;
  color: #072536;
  border-bottom-left-radius: 4px;
}

.typing {
  display: flex;
  gap: 4px;
  align-items: center;
  padding: 12px 14px;
}
.typing span {
  width: 7px; height: 7px;
  border-radius: 50%;
  background: #aaa;
  animation: bounce .9s infinite;
}
.typing span:nth-child(2) { animation-delay: .2s; }
.typing span:nth-child(3) { animation-delay: .4s; }
@keyframes bounce {
  0%,60%,100% { transform: translateY(0); }
  30% { transform: translateY(-6px); }
}

.chat-input-row {
  display: flex;
  gap: 8px;
  padding: 10px 12px;
  border-top: 1px solid #eee;
}
.chat-input {
  flex: 1;
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 10px;
  font-size: 13px;
  outline: none;
  font-family: inherit;
}
.chat-input:focus { border-color: #072536; }
.chat-send {
  width: 36px; height: 36px;
  border-radius: 10px;
  background: #072536;
  color: #fff;
  border: none;
  cursor: pointer;
  font-size: 14px;
  display: flex; align-items: center; justify-content: center;
  transition: background .15s;
}
.chat-send:hover { background: #0f3a4c; }
.chat-send:disabled { opacity: .4; cursor: not-allowed; }
</style>
