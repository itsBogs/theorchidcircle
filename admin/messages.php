<?php
require_once __DIR__ . '/../db.php';
require_admin();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin Messages</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/style.css">
  <style>
    .chat-container { display: flex; height: 75vh; border: 1px solid rgba(212,175,55,0.2); border-radius: var(--radius-md); overflow: hidden; background: #1a1a1a; }
    .session-list { width: 300px; border-right: 1px solid rgba(212,175,55,0.2); background: #111; overflow-y: auto; }
    .session-item { padding: 1rem; border-bottom: 1px solid rgba(212,175,55,0.2); cursor: pointer; transition: background 0.2s; }
    .session-item:hover { background: #f1f5f9; }
    .session-item.active { background: #e2e8f0; border-left: 4px solid var(--accent-gold); }
    .session-id { font-weight: 600; font-size: 0.9rem; color: var(--gold); margin-bottom: 0.3rem; }
    .session-meta { font-size: 0.75rem; color: #a1a1aa; display: flex; justify-content: space-between; }
    
    .chat-main { flex: 1; display: flex; flex-direction: column; background: #1a1a1a; }
    .chat-header { padding: 1rem; border-bottom: 1px solid rgba(212,175,55,0.2); background: #1a1a1a; font-weight: 600; color: var(--gold); display: flex; justify-content: space-between; }
    .chat-messages { flex: 1; overflow-y: auto; padding: 1.5rem; background: #151515; }
    .chat-form { display: flex; gap: 0.5rem; padding: 1rem; border-top: 1px solid rgba(212,175,55,0.2); background: #1a1a1a; }
    
    .badge-handled { background: #dcfce7; color: #166534; padding: 2px 6px; border-radius: 4px; font-size: 0.7rem; font-weight: 700; }
    .badge-waiting { background: #fef08a; color: #854d0e; padding: 2px 6px; border-radius: 4px; font-size: 0.7rem; font-weight: 700; }
  </style>
</head>
<body style="background: var(--bg-color); margin: 0; padding: 0;">
  <div class="admin-layout">
    <aside class="sidebar">
      <h2 style="display:flex; align-items:center; gap:8px; text-transform:none; letter-spacing:0.5px; font-size:1.1rem;"><img src="../uploads/logo-removebg-preview.png" style="height:28px; object-fit:contain;"> The Orchid Circle</h2>
      <nav style="margin-top: 1rem;">
        <a href="dashboard.php">Dashboard</a>
        <a href="profiles.php">Profiles</a>
        <a href="bookings.php">Bookings</a>
        <a href="messages.php" style="border-left: 4px solid var(--accent-gold); background: rgba(255,255,255,0.05); color: var(--accent-gold);">Messages</a>
        <a href="users.php">Admin Users</a>
        <a href="logout.php" style="margin-top: 2rem; color: #ef4444;">Logout</a>
      </nav>
    </aside>
    
    <main class="admin-content">
      <header style="margin-bottom: 2rem;">
        <h1 style="margin: 0; color: var(--gold);">Customer Chats</h1>
        <p style="margin: 0.3rem 0 0; color: #a1a1aa; font-size: 0.9rem;">Select a customer on the left to reply to them privately.</p>
      </header>

      <div class="chat-container">
        
        <!-- LEFT: Session List -->
        <div class="session-list" id="sessionList">
          <div style="padding: 1rem; text-align: center; color: #94a3b8; font-size: 0.9rem;">Loading chats...</div>
        </div>

        <!-- RIGHT: Chat Area -->
        <div class="chat-main">
          <div class="chat-header" id="chatHeader">
            <span>Select a conversation...</span>
            <span id="chatStatus"></span>
          </div>
          
          <div id="chat" class="chat-messages">
            <div style="text-align: center; color: #94a3b8; margin-top: 2rem;">No chat selected</div>
          </div>
          
          <form id="sendForm" class="chat-form" style="align-items: center;">
            <select id="senderName" style="width: 130px; flex-shrink: 0; padding: 0.75rem; border: 1px solid rgba(212,175,55,0.2); border-radius: var(--radius-md); background: #1a1a1a; font-weight: 600; cursor: pointer;">
              <option value="Admin">Admin</option>
              <option value="Agent">Agent</option>
            </select>
            <input id="msgText" placeholder="Type message..." style="flex: 1; min-width: 200px; padding: 0.75rem 1rem; border: 1px solid rgba(212,175,55,0.2); border-radius: var(--radius-md); font-size: 1rem;" disabled>
            <button id="sendBtn" type="submit" class="btn-gold" style="flex-shrink: 0; padding: 0.75rem 1.5rem; font-weight: 600;" disabled>Send Reply</button>
          </form>
        </div>

      </div>
    </main>
  </div>
<script>
let activeSession = null;
let lastFetchTime = null;
const sessionList = document.getElementById('sessionList');
const chat = document.getElementById('chat');
const chatHeader = document.getElementById('chatHeader');
const chatStatus = document.getElementById('chatStatus');
const msgText = document.getElementById('msgText');
const sendBtn = document.getElementById('sendBtn');

function formatTime(dateStr) {
  const d = new Date(dateStr);
  let h = d.getHours(); let m = d.getMinutes();
  const ampm = h >= 12 ? 'PM' : 'AM';
  return ((h%12)||12) + ':' + String(m).padStart(2,'0') + ' ' + ampm;
}

// 1. Fetch the list of sessions (sidebar)
async function fetchSessions() {
  try {
    const res = await fetch('../api/chat.php?action=fetch_sessions');
    const data = await res.json();
    
    if (data.length === 0) {
      sessionList.innerHTML = `<div style="padding: 1rem; text-align: center; color: #94a3b8; font-size: 0.9rem;">No active chats</div>`;
      return;
    }

    sessionList.innerHTML = data.map((s, idx) => {
      let displayName = 'Guest ' + s.session_id.substring(s.session_id.length - 4);
      if (s.session_id === 'global_legacy') displayName = 'Legacy Chat';
      
      let badge = '';
      if (s.handled_by && s.handled_by !== 'Orchidcircle Bot') {
        badge = `<span class="badge-handled">Handled by: ${s.handled_by}</span>`;
      } else {
        badge = `<span class="badge-waiting">Waiting</span>`;
      }

      return `
        <div class="session-item ${activeSession === s.session_id ? 'active' : ''}" onclick="selectSession('${s.session_id}', '${displayName}')">
          <div class="session-id">${displayName}</div>
          <div class="session-meta">
            <span>${formatTime(s.last_activity)}</span>
            ${badge}
          </div>
        </div>
      `;
    }).join('');
  } catch(e) {}
}

// 2. Click a session to open it
window.selectSession = function(sid, displayName) {
  activeSession = sid;
  lastFetchTime = null;
  chat.innerHTML = `<div style="text-align: center; color: #94a3b8; margin-top: 2rem;">Loading...</div>`;
  chatHeader.innerHTML = `<span>Chat with ${displayName}</span>`;
  msgText.disabled = false;
  sendBtn.disabled = false;
  
  // Re-render session list to show active highlight
  fetchSessions();
  // Fetch messages immediately
  fetchMsgs(true);
}

// 3. Fetch messages for active session
async function fetchMsgs(scroll = false) {
  if (!activeSession) return;
  try {
    // If scrolling is requested, we fetch all from start, otherwise just fetch new
    const sinceParam = scroll ? '' : '&since=' + encodeURIComponent(lastFetchTime || '1970-01-01 00:00:00');
    const res = await fetch('../api/chat.php?action=fetch&session_id=' + encodeURIComponent(activeSession) + sinceParam);
    const data = await res.json();
    
    if (scroll) chat.innerHTML = ''; // clear loading state
    
    if (data.length > 0) {
      const html = data.map(m => `
        <div class="msg" style="margin-bottom: 1rem; padding: 1rem; border-radius: var(--radius-md); background: #1a1a1a; box-shadow: var(--shadow-sm); border-left: 4px solid ${m.sender_type==='admin' ? 'var(--accent-gold)' : 'var(--gold)'}">
          <div style="display:flex; justify-content:space-between;">
            <strong style="font-size: 0.85rem; color: var(--gold); margin-bottom: 0.25rem;">${m.sender_name}</strong>
            <span style="font-size: 0.7rem; color: #94a3b8;">${formatTime(m.created_at)}</span>
          </div>
          <div style="margin-top: 0.2rem;">${m.message}</div>
        </div>
      `).join('');
      
      chat.insertAdjacentHTML('beforeend', html);
      if (scroll || chat.scrollTop + chat.clientHeight >= chat.scrollHeight - 100) {
        chat.scrollTop = chat.scrollHeight;
      }
      lastFetchTime = data[data.length-1].created_at;
    }
  } catch(e) {}
}

// Initial loads and polling
fetchSessions();
setInterval(fetchSessions, 5000); // refresh sidebar every 5s
setInterval(() => fetchMsgs(false), 3000); // refresh active chat every 3s

// 4. Send a message
document.getElementById('sendForm').addEventListener('submit', async e=>{
  e.preventDefault();
  if (!activeSession) return;
  const text = msgText.value.trim();
  const senderName = document.getElementById('senderName').value;
  if(!text) return;
  
  // Optimistic UI clear
  msgText.value = ''; 
  
  await fetch('../api/chat.php?action=send', {
    method: 'POST',
    body: new URLSearchParams({
      session_id: activeSession, 
      sender_type: 'admin', 
      sender_name: senderName, 
      message: text
    })
  });
  
  fetchMsgs(false);
  fetchSessions(); // Update sidebar "handled by" badge immediately
});
</script>
</body>
</html>

