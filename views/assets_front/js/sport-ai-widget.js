/**
 * NutriMind Sport AI Chat Widget — Widget Controller
 * Injecte le HTML du widget et gère les interactions
 */

(function () {
  'use strict';

  function injectWidget() {
    const html = `
      <button id="nm-chat-toggle" title="Coach Sportif IA" aria-label="Ouvrir le chat coach sportif">
        <span id="nm-chat-badge">1</span>
        <svg id="nm-icon-open" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 2C6.48 2 2 6.48 2 12c0 1.85.5 3.58 1.38 5.06L2 22l4.94-1.38C8.42 21.5 10.15 22 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2zm0 18c-1.69 0-3.27-.46-4.63-1.26l-.33-.2-3.44.96.96-3.44-.2-.33C3.46 14.27 3 12.69 3 11 3 6.58 6.58 3 11 3s8 3.58 8 8-3.58 8-8 8z"/>
          <circle cx="8.5" cy="12" r="1.2"/><circle cx="12" cy="12" r="1.2"/><circle cx="15.5" cy="12" r="1.2"/>
        </svg>
        <svg id="nm-icon-close" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="display:none">
          <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
        </svg>
      </button>

      <div id="nm-chat-container" role="dialog" aria-label="Chat Coach Sportif NutriMind" aria-modal="true">
        <div class="nm-chat-header">
          <div class="nm-bot-avatar" aria-hidden="true">🤖</div>
          <div class="nm-bot-info">
            <div class="nm-bot-name">NutriBot — Coach Sportif IA</div>
            <div class="nm-bot-status">
              <span class="nm-status-dot"></span>
              En ligne · Prêt à vous conseiller
            </div>
          </div>
          <button class="nm-chat-close" id="nm-chat-close-btn" aria-label="Fermer le chat">✕</button>
        </div>

        <div class="nm-quick-actions" role="toolbar" aria-label="Questions rapides">
          <button class="nm-quick-btn" data-q="J'ai mal au dos">🦴 Mal au dos</button>
          <button class="nm-quick-btn" data-q="Je veux perdre du poids">⚖️ Perdre du poids</button>
          <button class="nm-quick-btn" data-q="Je suis stressé">😣 Stress</button>
          <button class="nm-quick-btn" data-q="Je veux des muscles">💪 Muscles</button>
          <button class="nm-quick-btn" data-q="J'ai peu de temps">⏱️ Peu de temps</button>
        </div>

        <div class="nm-messages" id="nm-messages" role="log" aria-live="polite" aria-label="Messages du chat"></div>

        <div class="nm-chat-footer">
          <div class="nm-input-wrap">
            <textarea id="nm-chat-input" placeholder="Décrivez votre problème ou objectif sportif..." rows="1" aria-label="Message au coach sportif" maxlength="500"></textarea>
            <button class="nm-send-btn" id="nm-send-btn" aria-label="Envoyer le message" disabled>
              <svg viewBox="0 0 24 24" fill="white"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
            </button>
          </div>
          <div class="nm-footer-hint">NutriMind IA · Vos données restent privées 🔒</div>
        </div>
      </div>
    `;
    const wrapper = document.createElement('div');
    wrapper.id = 'nm-chat-widget-root';
    wrapper.innerHTML = html;
    document.body.appendChild(wrapper);
  }

  function getTime() {
    return new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
  }

  function autoResize(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = Math.min(textarea.scrollHeight, 100) + 'px';
  }

  function scrollToBottom(container) {
    requestAnimationFrame(() => { container.scrollTop = container.scrollHeight; });
  }

  function createBotMsg(contentHTML) {
    const div = document.createElement('div');
    div.className = 'nm-msg bot';
    div.innerHTML = `
      <div class="nm-msg-avatar" aria-hidden="true">🤖</div>
      <div class="nm-msg-content">
        <div class="nm-bubble">${contentHTML}</div>
        <span class="nm-msg-time">${getTime()}</span>
      </div>
    `;
    return div;
  }

  function createUserMsg(text) {
    const div = document.createElement('div');
    div.className = 'nm-msg user';
    const safeText = text.replace(/</g, '&lt;').replace(/>/g, '&gt;');
    div.innerHTML = `
      <div class="nm-msg-avatar" aria-hidden="true">👤</div>
      <div class="nm-msg-content">
        <div class="nm-bubble">${safeText}</div>
        <span class="nm-msg-time">${getTime()}</span>
      </div>
    `;
    return div;
  }

  function createTypingIndicator() {
    const div = document.createElement('div');
    div.className = 'nm-msg bot';
    div.id = 'nm-typing-indicator';
    div.innerHTML = `
      <div class="nm-msg-avatar" aria-hidden="true">🤖</div>
      <div class="nm-msg-content">
        <div class="nm-typing" aria-label="NutriBot est en train d'écrire">
          <span></span><span></span><span></span>
        </div>
      </div>
    `;
    return div;
  }

  function buildResponseHTML(response) {
    var type = response.type;
    if (type === 'text')  return response.text;
    if (type === 'card')  return response.text + response.card;
    if (type === 'mixed') return response.text + response.card + (response.extra || '');
    if (type === 'suggestion') {
      var chips = (response.suggestions || []).map(function(s) {
        return '<button class="nm-suggestion-chip" data-q="' + s.replace(/"/g, '&quot;') + '">' + s + '</button>';
      }).join('');
      return response.text + '<div class="nm-suggestions">' + chips + '</div>';
    }
    return response.text || '...';
  }

  function sendMessage(text, messagesContainer) {
    if (!text.trim()) return;
    messagesContainer.appendChild(createUserMsg(text));
    scrollToBottom(messagesContainer);

    const typingEl = createTypingIndicator();
    messagesContainer.appendChild(typingEl);
    scrollToBottom(messagesContainer);

    const delay = 900 + Math.random() * 700;
    setTimeout(() => {
      const typing = document.getElementById('nm-typing-indicator');
      if (typing) typing.remove();

      const response = SportAI.getResponse(text);
      const html = buildResponseHTML(response);
      const botMsg = createBotMsg(html);
      messagesContainer.appendChild(botMsg);
      scrollToBottom(messagesContainer);

      botMsg.querySelectorAll('.nm-suggestion-chip, .nm-quick-btn').forEach(chip => {
        chip.addEventListener('click', () => {
          const q = chip.getAttribute('data-q');
          if (q) sendMessage(q, messagesContainer);
        });
      });
    }, delay);
  }

  function init() {
    injectWidget();

    const toggle    = document.getElementById('nm-chat-toggle');
    const container = document.getElementById('nm-chat-container');
    const closeBtn  = document.getElementById('nm-chat-close-btn');
    const input     = document.getElementById('nm-chat-input');
    const sendBtn   = document.getElementById('nm-send-btn');
    const messages  = document.getElementById('nm-messages');
    const badge     = document.getElementById('nm-chat-badge');
    const iconOpen  = document.getElementById('nm-icon-open');
    const iconClose = document.getElementById('nm-icon-close');
    const quickBtns = document.querySelectorAll('.nm-quick-btn');

    let isOpen = false;

    function openChat() {
      isOpen = true;
      container.classList.add('open');
      toggle.classList.add('open');
      iconOpen.style.display = 'none';
      iconClose.style.display = 'block';
      if (badge) badge.style.display = 'none';
      setTimeout(() => input.focus(), 400);
    }

    function closeChat() {
      isOpen = false;
      container.classList.remove('open');
      toggle.classList.remove('open');
      iconOpen.style.display = 'block';
      iconClose.style.display = 'none';
    }

    toggle.addEventListener('click', () => isOpen ? closeChat() : openChat());
    closeBtn.addEventListener('click', closeChat);
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && isOpen) closeChat(); });

    input.addEventListener('input', () => {
      autoResize(input);
      sendBtn.disabled = !input.value.trim();
    });

    input.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); handleSend(); }
    });

    sendBtn.addEventListener('click', handleSend);

    function handleSend() {
      const text = input.value.trim();
      if (!text) return;
      input.value = '';
      input.style.height = 'auto';
      sendBtn.disabled = true;
      sendMessage(text, messages);
    }

    quickBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        const q = btn.getAttribute('data-q');
        if (q) {
          if (!isOpen) openChat();
          setTimeout(() => sendMessage(q, messages), isOpen ? 0 : 500);
        }
      });
    });

    setTimeout(() => {
      const welcomeHTML = `
        👋 <strong>Bonjour ! Je suis NutriBot</strong>, votre coach sportif IA ! 🏋️<br><br>
        Dites-moi votre <strong>problème</strong> ou votre <strong>objectif</strong> et je vous recommanderai l'activité et les exercices parfaits.<br><br>
        <em style="color:#94a3b8">Exemple : "j'ai mal au dos", "je veux perdre du poids", "je suis stressé"...</em>
      `;
      messages.appendChild(createBotMsg(welcomeHTML));
    }, 300);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
