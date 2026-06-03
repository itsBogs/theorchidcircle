document.addEventListener('click', async e => {
  if (e.target.matches('.viewBtn')) {
    const id = e.target.dataset.id;
    try {
      const res = await fetch('api/profile.php?id=' + id);
      const data = await res.json();
      showModal(`
        <h2 style="margin-bottom: 0.5rem;">${data.name}</h2>
        <p style="color: var(--text-light); margin-bottom: 1.5rem;">${data.description}</p>
        <div style="font-weight: 600; font-size: 1.1rem; margin-bottom: 1.5rem; color: var(--primary-navy);">Rate: $${data.rate_per_hour}/hr</div>
        <button onclick="startBooking(${data.id})" class="btn-gold" style="width: 100%;">Book This Profile</button>
      `);
    } catch (err) {
      alert("Error loading profile");
    }
  }
  if (e.target.matches('.bookBtn')) {
    const id = e.target.dataset.id;
    startBooking(id);
  }
});

function showModal(html) {
  const modal = document.getElementById('modal');
  modal.innerHTML = `
    <div class="card" style="max-width: 500px; padding: 2.5rem; border-top: 4px solid var(--accent-gold);">
      <button class="modal-close" id="closeModal">&times;</button>
      ${html}
    </div>
  `;
  modal.classList.remove('hidden');
  document.getElementById('closeModal').onclick = () => modal.classList.add('hidden');
}

function startBooking(id) {
  showModal(`
    <h3 style="margin-bottom: 1.5rem; color: var(--primary-navy);">Complete Your Booking</h3>
    <form id="bookingForm">
      <input name="profile_id" type="hidden" value="${id}">
      <label>Full Name</label>
      <input name="customer_name" required placeholder="John Doe">
      <label>Date</label>
      <input name="booking_date" type="date" required>
      <label>Hours</label>
      <input name="hours" type="number" min="1" value="1">
      <label>Additional Request / Message</label>
      <textarea name="message" placeholder="Any special requests?"></textarea>
      <button type="submit">Submit Request</button>
    </form>
  `);
  document.getElementById('bookingForm').addEventListener('submit', async e => {
    e.preventDefault();
    const form = new FormData(e.target);
    try {
      const res = await fetch('api/book.php', { method: 'POST', body: form });
      const data = await res.json();
      alert(data.message);
      document.getElementById('modal').classList.add('hidden');
    } catch(err) {
      alert("Error submitting booking.");
    }
  });
}

// VIP System
document.getElementById('vipBtn')?.addEventListener('click', async () => {
  const code = document.getElementById('vipCode').value.trim();
  if (!code) return;
  try {
    const res = await fetch('api/vip_check.php?code=' + encodeURIComponent(code));
    const data = await res.json();
    const msgEl = document.getElementById('vipMsg');
    if (data.valid) {
      msgEl.textContent = 'VIP unlocked successfully! Premium profiles are now visible.';
      msgEl.style.color = '#166534';
      localStorage.setItem('vip', '1');
      // In a real system we would reload the page or fetch VIP profiles dynamically
      setTimeout(()=> location.reload(), 1500);
    } else {
      msgEl.textContent = 'Invalid VIP code. Please try again.';
      msgEl.style.color = '#dc2626';
    }
  } catch(err) {
    alert("Error checking VIP code.");
  }
});

// Chat toggle
const toggleChat = document.getElementById('toggleChat');
const chatBody = document.getElementById('chatBody');
const chatIcon = document.getElementById('chatToggleIcon');
if (toggleChat) {
  toggleChat.addEventListener('click', () => {
    if (chatBody.style.display === 'none') {
      chatBody.style.display = 'block';
      chatIcon.textContent = '▼';
    } else {
      chatBody.style.display = 'none';
      chatIcon.textContent = '▲';
    }
  });
}

// Public chat
async function fetchPublicChat() {
  try {
    const res = await fetch('api/chat.php?action=fetch');
    const data = await res.json();
    const msgs = data.map(m => `
      <div class="msg" style="border-left: 3px solid ${m.sender_type==='admin' ? 'var(--accent-gold)' : 'var(--primary-navy)'}">
        <strong>${m.sender_name}:</strong> ${m.message}
      </div>
    `).join('');
    const chatMsgs = document.getElementById('chatMessages');
    if (chatMsgs && chatMsgs.innerHTML !== msgs) {
      chatMsgs.innerHTML = msgs;
      document.getElementById('publicChat').scrollTop = document.getElementById('publicChat').scrollHeight;
    }
  } catch (e) { console.error(e) }
}
setInterval(fetchPublicChat, 3000);
fetchPublicChat();

document.getElementById('chatForm')?.addEventListener('submit', async e => {
  e.preventDefault();
  const name = document.getElementById('chatName').value.trim() || 'Guest';
  const message = document.getElementById('chatMessage').value.trim();
  if (!message) return;
  try {
    await fetch('api/chat.php?action=send', { 
      method: 'POST', 
      body: new URLSearchParams({ sender_type: 'user', sender_name: name, message: message }) 
    });
    document.getElementById('chatMessage').value = '';
    fetchPublicChat();
  } catch(err) {
    console.error(err);
  }
});

// Carousel behavior
function initCarousel() {
  const track = document.querySelector('.carousel-track');
  if (!track) return;
  const prev = document.querySelector('.carousel-btn.prev');
  const next = document.querySelector('.carousel-btn.next');
  let itemWidth = document.querySelector('.carousel-item')?.offsetWidth || 280;
  
  prev.addEventListener('click', () => {
    track.scrollBy({ left: -(itemWidth + 24), behavior: 'smooth' });
  });
  next.addEventListener('click', () => {
    track.scrollBy({ left: itemWidth + 24, behavior: 'smooth' });
  });
}
window.addEventListener('load', initCarousel);
window.addEventListener('resize', initCarousel);

// Render placeholder initials
document.querySelectorAll('.placeholder').forEach(el => {
  const name = el.dataset.name || '';
  const initials = name.split(' ').map(x => x[0]).slice(0, 2).join('').toUpperCase();
  el.textContent = initials;
});
