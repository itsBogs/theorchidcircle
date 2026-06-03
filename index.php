<?php
require_once __DIR__ . '/db.php';
$profiles = $pdo->query('SELECT * FROM profiles WHERE category = "regular" AND status = "Available" ORDER BY id ASC')->fetchAll();

$isVIP = isset($_COOKIE['vip_unlocked']) && $_COOKIE['vip_unlocked'] == '1';
$vipProfiles = [];
if ($isVIP) {
    $vipProfiles = $pdo->query('SELECT * FROM profiles WHERE category = "vip" ORDER BY id ASC')->fetchAll();
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Orchidcircle — Premium Companions</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css">
  <style>
    html { scroll-behavior: smooth; }

    .site-header {
      min-height: 86px;
      padding: 1rem 3rem;
      background: rgba(8, 8, 8, 0.94);
      backdrop-filter: blur(16px);
      border-bottom: 1px solid rgba(193, 154, 82, 0.22);
      position: fixed;
      left: 0;
      right: 0;
      top: 0;
    }
    .header-left { gap: 0.9rem; }
    .site-logo {
      width: 58px;
      height: 58px;
      padding: 7px;
      border: 1px solid rgba(193, 154, 82, 0.76);
      border-radius: 50%;
      object-fit: contain;
      margin-right: 0;
      background: radial-gradient(circle, rgba(193,154,82,0.18), rgba(0,0,0,0.1));
    }
    .header-titles h1 {
      font-family: 'Cormorant Garamond', serif;
      color: #d9bd7a;
      font-size: 1.55rem;
      font-weight: 600;
      letter-spacing: 0.22em;
      line-height: 1;
      text-transform: uppercase;
    }
    .header-subtitle {
      margin-top: 0.45rem;
      color: #c9a96f;
      font-family: var(--font);
      font-size: 0.58rem;
      font-weight: 600;
      letter-spacing: 0.26em;
      text-transform: uppercase;
    }
    .main-nav {
      display: flex;
      align-items: center;
      gap: 2rem;
      margin-left: auto;
    }
    .main-nav a {
      color: rgba(255,255,255,0.72);
      font-size: 0.7rem;
      letter-spacing: 0.16em;
      text-decoration: none;
      text-transform: uppercase;
      transition: color 0.2s ease;
      position: relative;
    }
    .main-nav a:hover,
    .main-nav a.active { color: #d9bd7a; }
    .main-nav a.active::after {
      content: '';
      position: absolute;
      left: 0;
      right: 0;
      bottom: -14px;
      height: 1px;
      background: #d9bd7a;
      box-shadow: 0 0 10px rgba(217,189,122,0.7);
    }
    /* ── HERO ── */
    .hero {
      min-height: auto;
      aspect-ratio: auto;
      background:
        linear-gradient(90deg, rgba(6,6,6,0.96) 0%, rgba(8,8,8,0.86) 34%, rgba(8,8,8,0.4) 58%, rgba(8,8,8,0.24) 100%),
        linear-gradient(180deg, rgba(7,7,7,0.45) 0%, rgba(7,7,7,0.12) 58%, #10100f 100%);
      background-color: #0d0d0d;
      color: #fff;
      text-align: left;
      padding: 0;
      position: relative;
      overflow: hidden;
      display: block;
      margin-top: 86px;
    }
    .hero-bg {
      position: relative;
      width: 100%;
      height: auto;
      object-fit: contain;
      object-position: top center;
      display: block;
      z-index: 0;
    }
    .hero::before {
      content: '';
      position: absolute;
      inset: 0;
      background: radial-gradient(circle at 14% 28%, rgba(207,165,84,0.18), transparent 18%),
                  linear-gradient(180deg, transparent 72%, #111 100%);
      pointer-events: none;
      z-index: 1;
    }
    .hero-inner {
      width: min(1180px, calc(100% - 6rem));
      position: absolute;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -39%);
      z-index: 2;
    }
    .hero h2 {
      max-width: 560px;
      font-family: 'Cormorant Garamond', serif;
      font-size: clamp(2rem, 4vw, 3.2rem);
      line-height: 0.98;
      font-weight: 500;
      color: #f7f0e2;
      letter-spacing: 0.16em;
      text-transform: uppercase;
      margin-bottom: 1.4rem;
      text-shadow: 0 12px 42px rgba(0,0,0,0.72);
    }
    .hero h2 span { color: #d3ad68; }
    .hero-divider {
      width: 285px;
      height: 18px;
      margin-bottom: 1.55rem;
      position: relative;
    }
    .hero-divider::before,
    .hero-divider::after {
      content: '';
      position: absolute;
      top: 8px;
      height: 1px;
      background: linear-gradient(90deg, rgba(217,189,122,0), rgba(217,189,122,0.85));
    }
    .hero-divider::before { left: 0; width: 126px; }
    .hero-divider::after {
      left: 158px;
      width: 126px;
      background: linear-gradient(90deg, rgba(217,189,122,0.85), rgba(217,189,122,0));
    }
    .hero-divider span {
      position: absolute;
      left: 132px;
      top: 0;
      color: #d9bd7a;
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.1rem;
    }
    .hero p {
      color: rgba(255,255,255,0.74);
      font-size: 1.03rem;
      max-width: 440px;
      margin: 0 0 2rem;
      line-height: 1.8;
    }
    .hero-actions {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      gap: 1.15rem;
    }
    .hero-cta {
      min-width: 218px;
      justify-content: center;
      padding: 1rem 1.5rem;
      border: 1px solid rgba(217,189,122,0.82);
      border-radius: 0;
      background: rgba(8,8,8,0.18);
      color: #f4dfac;
      font-size: 0.78rem;
      letter-spacing: 0.18em;
      text-transform: uppercase;
    }
    .hero-cta:hover {
      background: rgba(217,189,122,0.13);
      transform: none;
      box-shadow: 0 0 24px rgba(217,189,122,0.18);
    }
    .confidential-note {
      color: rgba(255,255,255,0.62);
      font-size: 0.73rem;
      letter-spacing: 0.18em;
      text-transform: uppercase;
    }
    .hero-features {
      background: linear-gradient(180deg, #111 0%, #151413 100%);
      border-top: 1px solid rgba(217,189,122,0.1);
      border-bottom: 1px solid rgba(217,189,122,0.1);
      padding: 2.1rem 3rem;
    }
    .feature-grid {
      max-width: 1180px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(4, 1fr);
    }
    .feature-item {
      min-height: 130px;
      padding: 0.35rem 2rem;
      text-align: center;
      border-left: 1px solid rgba(217,189,122,0.22);
    }
    .feature-item:first-child { border-left: 0; }
    .feature-icon {
      color: #cda866;
      font-family: 'Cormorant Garamond', serif;
      font-size: 2.55rem;
      line-height: 1;
      margin-bottom: 0.7rem;
    }
    .feature-item h3 {
      color: #f4ead8;
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.05rem;
      font-weight: 500;
      letter-spacing: 0.2em;
      text-transform: uppercase;
      margin-bottom: 0.45rem;
    }
    .feature-item p {
      color: rgba(255,255,255,0.62);
      font-size: 0.8rem;
      line-height: 1.65;
      max-width: 210px;
      margin: 0 auto;
    }

    /* ── VIP BAR ── */
    .vip-bar {
      background: var(--black);
      padding: 1rem 2rem;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 1rem;
      border-bottom: 1px solid rgba(212,175,55,0.15);
    }
    .vip-bar label { color: var(--gold); font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
    .vip-bar input {
      padding: 0.6rem 1rem;
      border: 1.5px solid rgba(212,175,55,0.3);
      border-radius: var(--radius-md);
      background: rgba(255,255,255,0.05);
      color: #fff;
      width: 200px;
      outline: none;
      font-family: var(--font);
    }
    .vip-bar input::placeholder { color: #666; }
    .vip-bar input:focus { border-color: var(--gold); background: rgba(255,255,255,0.08); }
    #vipMsg { font-size: 0.85rem; font-weight: 600; }

    /* ── PROFILES GRID ── */
    .profiles-section {
      padding: 3.5rem 2rem;
      max-width: 1300px;
      margin: 0 auto;
    }
    .profiles-section h3 {
      color: var(--black);
      font-size: 1.6rem;
      font-weight: 800;
      margin-bottom: 0.5rem;
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    .profiles-section .subtitle {
      color: var(--gray-400);
      font-size: 0.95rem;
      margin-bottom: 2.5rem;
    }
    .profiles-list {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
      gap: 1.5rem;
    }

    .companions-section {
      max-width: none;
      margin: 0;
      padding: 3.2rem 3.5rem 3.8rem;
      background: #0d0d0f;
      position: relative;
      overflow: hidden;
      border-top: 1px solid rgba(217,189,122,0.08);
    }
    .companions-section::before {
      content: '';
      position: absolute;
      inset: 0;
      background: radial-gradient(circle at 50% 0%, rgba(217,189,122,0.08), transparent 28%);
      pointer-events: none;
    }
    .companions-heading {
      position: relative;
      z-index: 1;
      text-align: center;
      margin-bottom: 1.8rem;
    }
    .companions-kicker {
      color: #b89250;
      font-size: 0.78rem;
      font-weight: 600;
      letter-spacing: 0.28em;
      text-transform: uppercase;
      margin-bottom: 0.35rem;
    }
    .companions-section h3 {
      color: #f2ebdc;
      font-family: 'Cormorant Garamond', serif;
      font-size: clamp(1.8rem, 3vw, 2.6rem);
      font-weight: 500;
      letter-spacing: 0.18em;
      text-transform: uppercase;
      margin-bottom: 0.35rem;
    }
    .companions-divider {
      color: #b89250;
      font-family: 'Cormorant Garamond', serif;
      font-size: 1rem;
      line-height: 1;
    }
    .companions-frame {
      position: relative;
      z-index: 1;
      max-width: 1120px;
      margin: 0 auto;
      padding: 0 2.2rem;
    }
    .companions-section .profiles-list {
      display: grid;
      grid-auto-flow: column;
      grid-auto-columns: calc((100% - 48px) / 5);
      grid-template-columns: none;
      gap: 12px;
      overflow-x: auto;
      scroll-snap-type: x mandatory;
      scrollbar-width: none;
      padding: 0.15rem 0 0.6rem;
    }
    .companions-section .profiles-list::-webkit-scrollbar { display: none; }
    .companions-section .profile-card {
      min-width: 170px;
      background: #16110d;
      border: 1px solid rgba(217,189,122,0.12);
      border-radius: 0;
      box-shadow: none;
      scroll-snap-align: start;
      isolation: isolate;
    }
    .companions-section .profile-card:hover {
      transform: translateY(-4px);
      border-color: rgba(217,189,122,0.45);
      box-shadow: 0 18px 40px rgba(0,0,0,0.42);
    }
    .companions-section .profile-card .cover,
    .companions-section .profile-card .cover-placeholder {
      aspect-ratio: 3 / 4.05;
      filter: saturate(0.82) contrast(1.06) brightness(0.78);
    }
    .companions-section .profile-card:hover .cover {
      filter: saturate(0.98) contrast(1.08) brightness(0.9);
    }
    .companions-section .avail-badge { display: none; }
    .companions-section .card-label {
      position: absolute;
      left: 0;
      right: 0;
      bottom: 0;
      padding: 2rem 0.9rem 0.85rem;
      background: linear-gradient(180deg, transparent, rgba(0,0,0,0.84));
      justify-content: center;
      gap: 0.45rem;
    }
    .companions-section .name {
      color: #f4ead8;
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.03rem;
      font-weight: 500;
      letter-spacing: 0.11em;
      text-transform: uppercase;
    }
    .companions-section .age {
      color: rgba(244,234,216,0.65);
      font-size: 0.72rem;
    }
    .companions-nav {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      width: 34px;
      height: 58px;
      border: 0;
      border-radius: 0;
      background: transparent;
      color: rgba(217,189,122,0.62);
      font-family: 'Cormorant Garamond', serif;
      font-size: 2.6rem;
      font-weight: 300;
      padding: 0;
      z-index: 2;
    }
    .companions-nav:hover {
      background: transparent;
      color: #d9bd7a;
      box-shadow: none;
      transform: translateY(-50%);
    }
    .companions-nav.prev { left: 0; }
    .companions-nav.next { right: 0; }
    .companions-actions {
      position: relative;
      z-index: 1;
      text-align: center;
      margin-top: 0.65rem;
    }
    .companions-view-all {
      min-width: 218px;
      justify-content: center;
      padding: 0.95rem 1.5rem;
      border: 1px solid rgba(217,189,122,0.82);
      border-radius: 0;
      background: rgba(8,8,8,0.18);
      color: #f4dfac;
      font-size: 0.75rem;
      letter-spacing: 0.18em;
      text-transform: uppercase;
    }
    .companions-view-all:hover {
      background: rgba(217,189,122,0.13);
      transform: none;
      box-shadow: 0 0 24px rgba(217,189,122,0.18);
    }

    /* ── PROFILE CARD ── */
    .profile-card {
      background: var(--white);
      border-radius: var(--radius-lg);
      overflow: hidden;
      box-shadow: var(--shadow-sm);
      cursor: pointer;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      position: relative;
      border: 1px solid var(--gray-100);
    }
    .profile-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 50px rgba(0,0,0,0.15);
      border-color: var(--gold);
    }
    .profile-card .cover {
      width: 100%;
      aspect-ratio: 3/4;
      object-fit: cover;
      display: block;
    }
    .profile-card .cover-placeholder {
      width: 100%;
      aspect-ratio: 3/4;
      background: linear-gradient(145deg, #1a1a1a, #000);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 3rem;
      font-weight: 800;
      color: var(--gold);
    }
    .profile-card .card-label {
      padding: 1rem 1.2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: var(--white);
    }
    .profile-card .name { font-weight: 700; font-size: 0.95rem; color: var(--black); }
    .profile-card .age  { font-size: 0.8rem; color: var(--gray-400); }
    .profile-card .avail-badge {
      position: absolute;
      top: 12px;
      right: 12px;
      background: rgba(212,175,55,0.9);
      color: var(--black);
      font-size: 0.7rem;
      font-weight: 800;
      padding: 4px 10px;
      border-radius: 999px;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      backdrop-filter: blur(4px);
    }

    /* ── PROFILE MODAL (FULLSCREEN GRID VIEW) ── */
    .profile-modal-overlay {
      position: fixed; inset: 0;
      background: rgba(85, 85, 85, 0.98); /* Matching the dark grey from screenshot */
      z-index: 9999;
      display: flex;
      flex-direction: column;
      overflow-y: auto;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.3s ease;
      padding: 3rem 2rem;
    }
    .profile-modal-overlay.open { opacity: 1; pointer-events: all; }
    
    .profile-modal {
      width: 100%;
      max-width: 1000px;
      margin: 0 auto;
      position: relative;
      color: #fff;
    }
    /* Companions modal (all profiles) */
    .companions-modal-overlay {
      position: fixed; inset: 0; display:flex; align-items:center; justify-content:center;
      background: rgba(6,6,6,0.88); z-index: 10000; opacity:0; pointer-events:none; transition:opacity .22s ease;
      padding: 2rem;
    }
    .companions-modal-overlay.open { opacity:1; pointer-events:all; }
    .companions-modal-inner {
      box-sizing: border-box;
      width: 100%; max-width: 1100px; max-height: 80vh; overflow-y:auto; overflow-x:hidden; -webkit-overflow-scrolling: touch; background: linear-gradient(180deg,#0f0f0f,#0b0b0b); padding:1.6rem; border:1px solid rgba(212,175,55,0.06); border-radius:12px;
    }
    .companions-modal-grid { display:grid; grid-template-columns: repeat(auto-fill,minmax(180px,1fr)); gap:14px; }
    .companion-item { background: rgba(255,255,255,0.02); padding:10px; border-radius:8px; display:flex; flex-direction:column; gap:8px; align-items:center; text-align:center; }
    .companion-item img { width:100%; height:190px; object-fit:cover; border-radius:6px; cursor: pointer; }
    .companion-item { cursor: default; }
    .companion-item button { cursor: pointer; }
    .companion-placeholder { width:100%; height:190px; display:flex; align-items:center; justify-content:center; font-size:3rem; background:#111; color:var(--gold); border-radius:6px; }
    .companion-name { color:var(--gold); font-weight:700; margin-top:6px; }
    .companion-age { color:rgba(255,255,255,0.7); font-size:0.9rem; }
    .companion-actions { display:flex; gap:8px; margin-top:6px; }

    /* Buttons removed from modal cards; hide any leftover action areas */
    .companions-modal-inner .companion-actions { display: none !important; }
    
    .modal-close-btn {
      position: fixed;
      top: 1.5rem;
      right: 2rem;
      background: none;
      color: #fff;
      border: 2px solid #fff;
      width: 44px;
      height: 44px;
      border-radius: 50%;
      font-size: 1.5rem;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 10000;
      transition: var(--transition);
    }
    .modal-close-btn:hover {
      background: #fff;
      color: #000;
    }

    /* When a modal is open, make background non-interactive */
    body.modal-open .site-header,
    body.modal-open .hero,
    body.modal-open .hero-features,
    body.modal-open .profiles-section,
    body.modal-open .site-footer,
    body.modal-open .chat-widget { pointer-events: none; user-select: none; filter: brightness(0.6) blur(1px); }

    .modal-top-info {
      margin-bottom: 3rem;
      font-size: 0.95rem;
      line-height: 1.6;
    }
    .modal-top-info h2 {
      font-size: 2.2rem;
      margin-bottom: 1rem;
      color: var(--gold);
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    .modal-stats {
      margin-bottom: 1.5rem;
    }
    .modal-stats div {
      margin-bottom: 0.25rem;
    }
    .modal-rates {
      margin-bottom: 1.5rem;
      font-weight: 600;
      color: var(--gold-light);
    }
    .modal-desc {
      max-width: 800px;
      margin-bottom: 2rem;
    }
    .modal-book-btn {
      padding: 0.8rem 2rem;
      background: var(--gold);
      color: var(--black);
      font-weight: 800;
      font-size: 1rem;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      transition: var(--transition);
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    .modal-book-btn:hover {
      background: var(--gold-light);
    }

    /* Gallery Grid */
    .modal-gallery-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 15px;
    }
    .modal-gallery-grid img {
      width: 100%;
      aspect-ratio: 3/4;
      object-fit: cover;
      display: block;
      border-radius: 4px;
      transition: transform 0.3s ease;
      cursor: pointer;
    }
    .modal-gallery-grid img:hover {
      transform: scale(1.03);
    }

    /* ── LIGHTBOX OVERLAY ── */
    #lightboxOverlay {
      position: fixed; inset: 0;
      background: rgba(15, 15, 15, 0.95);
      z-index: 100000;
      display: flex; align-items: center; justify-content: center;
      opacity: 0; pointer-events: none; transition: opacity 0.3s;
    }
    #lightboxOverlay.open { opacity: 1; pointer-events: all; }
    #lightboxImg { max-width: 90%; max-height: 90vh; object-fit: contain; border-radius: 4px; box-shadow: 0 10px 40px rgba(0,0,0,0.5); }
    .lb-close { position: absolute; top: 20px; right: 30px; background: none; border: none; color: #fff; font-size: 2.5rem; cursor: pointer; transition: 0.2s; }
    .lb-close:hover { color: var(--gold); }
    .lb-nav { position: absolute; top: 50%; transform: translateY(-50%); background: none; border: none; color: #fff; font-size: 3rem; cursor: pointer; transition: 0.2s; padding: 20px; }
    .lb-nav:hover { color: var(--gold); }
    .lb-prev { left: 20px; }
    .lb-next { right: 20px; }
    #lbCounter { position: absolute; top: 30px; left: 30px; color: #fff; font-size: 0.9rem; letter-spacing: 1px; }

    /* ── BOOKING MODAL ── */
    #bookingModal { position: fixed; inset: 0; background: rgba(0,0,0,0.88); backdrop-filter: blur(14px); z-index: 10000; display: flex; align-items: center; justify-content: center; padding: 1rem; opacity: 0; pointer-events: none; transition: opacity 0.3s; }
    #bookingModal.open { opacity: 1; pointer-events: all; }
    #bookingModal .bm-inner {
      background: linear-gradient(180deg, rgba(26,26,26,0.98), rgba(12,12,12,0.98));
      border-radius: 10px;
      padding: 2.35rem;
      width: 100%;
      max-width: 460px;
      position: relative;
      border: 1px solid rgba(193,154,82,0.58);
      box-shadow: 0 22px 70px rgba(0,0,0,0.72), 0 0 34px rgba(193,154,82,0.15);
      transform: scale(0.93);
      transition: transform 0.3s;
    }
    #bookingModal .bm-inner::before {
      content: '';
      position: absolute;
      left: 18px;
      right: 18px;
      top: 0;
      height: 2px;
      background: linear-gradient(90deg, transparent, var(--gold), transparent);
    }
    #bookingModal.open .bm-inner { transform: scale(1); }
    #bookingModal .bm-close {
      position: absolute;
      top: 0.9rem;
      right: 0.9rem;
      width: 36px;
      height: 36px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: rgba(255,255,255,0.04);
      border: 1px solid rgba(193,154,82,0.22);
      border-radius: 50%;
      color: rgba(255,255,255,0.72);
      font-size: 1.35rem;
      line-height: 1;
      cursor: pointer;
      transition: var(--transition);
    }
    #bookingModal .bm-close:hover {
      background: var(--gold);
      color: var(--black);
      border-color: var(--gold);
    }
    #bookingModal h3 {
      margin: 0 0 1.4rem;
      color: var(--gold);
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.8rem;
      font-weight: 700;
      letter-spacing: 0.02em;
    }
    #bookingForm { display: grid; gap: 1rem; }
    #bookingForm label {
      margin: 0 0 -0.55rem;
      color: rgba(255,255,255,0.72);
      font-size: 0.72rem;
      font-weight: 700;
      letter-spacing: 0.14em;
      text-transform: uppercase;
    }
    #bookingForm input,
    #bookingForm textarea {
      width: 100%;
      background: rgba(255,255,255,0.045);
      color: #f5f1e8;
      border: 1px solid rgba(193,154,82,0.32);
      border-radius: 7px;
      padding: 0.85rem 1rem;
      font-family: var(--font);
      font-size: 0.95rem;
      outline: none;
      transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }
    #bookingForm textarea { min-height: 98px; resize: vertical; }
    #bookingForm input::placeholder,
    #bookingForm textarea::placeholder { color: rgba(255,255,255,0.42); }
    #bookingForm input:focus,
    #bookingForm textarea:focus {
      background: rgba(255,255,255,0.07);
      border-color: var(--gold);
      box-shadow: 0 0 0 3px rgba(193,154,82,0.14);
    }
    #bookingForm button[type="submit"] {
      margin-top: 0.2rem;
      background: linear-gradient(135deg, #c19a52, #e0bd52);
      color: #050505;
      border: none;
      border-radius: 7px;
      padding: 0.95rem 1rem;
      font-weight: 800;
      font-size: 0.94rem;
      cursor: pointer;
      transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
    }
    #bookingForm button[type="submit"]:hover {
      transform: translateY(-1px);
      box-shadow: 0 10px 28px rgba(193,154,82,0.2);
      filter: brightness(1.04);
    }

    .vip-confirm-card {
      width: min(92vw, 420px);
      padding: 2.2rem;
      text-align: center;
      background: linear-gradient(180deg, rgba(24,24,24,0.98), rgba(8,8,8,0.98));
      border: 1px solid rgba(193,154,82,0.58);
      border-radius: 12px;
      box-shadow: 0 24px 80px rgba(0,0,0,0.76), 0 0 34px rgba(193,154,82,0.18);
      position: relative;
      overflow: hidden;
    }
    .vip-confirm-card::before {
      content: '';
      position: absolute;
      left: 22px;
      right: 22px;
      top: 0;
      height: 2px;
      background: linear-gradient(90deg, transparent, var(--gold), transparent);
    }
    .vip-confirm-mark {
      width: 54px;
      height: 54px;
      margin: 0 auto 1rem;
      border: 1px solid rgba(193,154,82,0.65);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--gold);
      font-size: 1.55rem;
      box-shadow: inset 0 0 18px rgba(193,154,82,0.14), 0 0 22px rgba(193,154,82,0.12);
    }
    .vip-confirm-card h2 {
      margin: 0 0 0.65rem;
      color: var(--gold);
      font-family: 'Cormorant Garamond', serif;
      font-size: 2rem;
      font-weight: 700;
      letter-spacing: 0.03em;
    }
    .vip-confirm-card p {
      margin: 0 auto 1.5rem;
      max-width: 310px;
      color: rgba(255,255,255,0.68);
      font-size: 0.95rem;
      line-height: 1.6;
    }
    .vip-confirm-actions {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 0.75rem;
    }
    .vip-confirm-actions button {
      border-radius: 7px;
      padding: 0.85rem 1rem;
      border: 1px solid rgba(193,154,82,0.35);
      font-weight: 800;
      cursor: pointer;
      transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }
    .vip-confirm-cancel {
      background: rgba(255,255,255,0.06);
      color: rgba(255,255,255,0.84);
    }
    .vip-confirm-exit {
      background: linear-gradient(135deg, #c19a52, #e0bd52);
      color: #050505;
    }
    .vip-confirm-actions button:hover {
      transform: translateY(-1px);
      box-shadow: 0 10px 26px rgba(193,154,82,0.16);
    }

    /* ══════════════════════════════════════════════
       CHAT WIDGET — Luxury Black/Gold Chatbot
       ══════════════════════════════════════════════ */
    .chat-widget {
      position: fixed;
      right: 24px;
      bottom: 24px;
      z-index: 2000;
    }
    /* Floating trigger button */
    .chat-trigger {
      min-width: 60px;
      height: 60px;
      padding: 0 12px;
      gap: 8px;
      border-radius: 999px;
      background: var(--gold);
      color: var(--black);
      border: none;
      font-size: 1rem;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 6px 24px rgba(212,175,55,0.4);
      transition: var(--transition);
      position: relative;
    }
    .chat-trigger:hover { transform: scale(1.08); box-shadow: 0 8px 32px rgba(212,175,55,0.5); }
    .chat-trigger .notif {
      position: absolute;
      top: -2px;
      right: -2px;
      width: 16px;
      height: 16px;
      background: #ef4444;
      border-radius: 50%;
      border: 2px solid var(--white);
      display: none;
    }
    .chat-label { font-weight: 800; color: #111; font-size: 0.95rem; line-height:1; }

    /* Chat window */
    .chat-window {
      position: absolute;
      bottom: 76px;
      right: 0;
      width: 370px;
      max-height: 520px;
      background: var(--white);
      border-radius: var(--radius-xl);
      box-shadow: 0 16px 60px rgba(0,0,0,0.25);
      display: none;
      flex-direction: column;
      overflow: hidden;
      border: 1px solid var(--gray-100);
      animation: chatSlideUp 0.3s ease;
    }
    .chat-window.open { display: flex; }

    @keyframes chatSlideUp {
      from { opacity: 0; transform: translateY(16px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    /* Chat header */
    .cw-header {
      background: var(--black);
      color: var(--white);
      padding: 1.1rem 1.25rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      border-bottom: 1px solid rgba(212,175,55,0.2);
    }
    .cw-avatar {
      width: 38px;
      height: 38px;
      border-radius: 50%;
      background: var(--gold);
      color: var(--black);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      font-size: 1rem;
      flex-shrink: 0;
    }
    .cw-info { flex: 1; }
    .cw-info .cw-name { font-weight: 700; font-size: 0.95rem; }
    .cw-info .cw-status { font-size: 0.75rem; color: var(--gold); display: flex; align-items: center; gap: 5px; }
    .cw-info .cw-status::before { content: ''; width: 7px; height: 7px; border-radius: 50%; background: #22c55e; display: inline-block; }
    .cw-close {
      background: none;
      border: none;
      color: #888;
      font-size: 1.3rem;
      cursor: pointer;
      padding: 0;
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      transition: var(--transition);
    }
    .cw-close:hover { background: rgba(255,255,255,0.1); color: var(--white); transform: none; box-shadow: none; }

    /* Messages area */
    .cw-messages {
      flex: 1;
      overflow-y: auto;
      padding: 1rem;
      background: #fafafa;
      min-height: 280px;
      max-height: 320px;
    }
    .cw-msg {
      max-width: 82%;
      padding: 0.7rem 1rem;
      border-radius: 16px;
      font-size: 0.88rem;
      line-height: 1.5;
      margin-bottom: 0.6rem;
      position: relative;
      animation: msgPop 0.25s ease;
    }
    @keyframes msgPop {
      from { opacity: 0; transform: translateY(6px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    .cw-msg.user-msg {
      background: var(--black);
      color: var(--white);
      margin-left: auto;
      border-bottom-right-radius: 4px;
    }
    .cw-msg.admin-msg {
      background: var(--white);
      color: var(--black);
      margin-right: auto;
      border-bottom-left-radius: 4px;
      border: 1px solid var(--gray-100);
    }
    .cw-msg.admin-msg .bot-badge {
      display: inline-block;
      font-size: 0.65rem;
      font-weight: 800;
      color: var(--gold);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 3px;
    }
    .cw-msg .msg-time {
      font-size: 0.65rem;
      color: #999;
      margin-top: 4px;
      display: block;
    }
    .cw-msg.user-msg .msg-time { color: rgba(255,255,255,0.5); text-align: right; }

    /* Typing indicator */
    .typing-indicator {
      display: none;
      padding: 0.6rem 1rem;
      margin-bottom: 0.6rem;
      max-width: 80px;
    }
    .typing-indicator.show { display: flex; }
    .typing-indicator span {
      width: 7px;
      height: 7px;
      border-radius: 50%;
      background: var(--gray-200);
      display: inline-block;
      margin-right: 4px;
      animation: typingBounce 1.2s infinite;
    }
    .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
    .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }
    @keyframes typingBounce {
      0%, 80%, 100% { transform: translateY(0); }
      40% { transform: translateY(-6px); }
    }

    /* Input bar */
    .cw-input-bar {
      display: flex;
      padding: 0.75rem 1rem;
      gap: 0.5rem;
      border-top: 1px solid var(--gray-100);
      background: var(--white);
    }
    .cw-input-bar input {
      flex: 1;
      padding: 0.65rem 1rem;
      border: 1.5px solid var(--gray-100);
      border-radius: 999px;
      outline: none;
      font-family: var(--font);
      font-size: 0.9rem;
      transition: border var(--transition);
    }
    .cw-input-bar input:focus { border-color: var(--gold); }
    .cw-input-bar button {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: var(--gold);
      color: var(--black);
      border: none;
      font-size: 1.1rem;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: var(--transition);
      padding: 0;
      flex-shrink: 0;
    }
    .cw-input-bar button:hover { background: var(--gold-light); box-shadow: var(--shadow-gold); transform: none; }

    /* ── FOOTER ── */
    .site-footer {
      background: var(--black);
      color: #666;
      text-align: center;
      padding: 2rem;
      font-size: 0.85rem;
      border-top: 1px solid rgba(212,175,55,0.1);
    }
    .site-footer span { color: var(--gold); }

    /* Logo and Header Adjustments */
    .header-left {
      display: flex;
      align-items: center;
      gap: 0.9rem;
    }
    .site-logo {
      width: 58px;
      height: 58px;
      padding: 7px;
      border: 1px solid rgba(193, 154, 82, 0.76);
      border-radius: 50%;
      object-fit: contain;
      margin-right: 0;
      background: radial-gradient(circle, rgba(193,154,82,0.18), rgba(0,0,0,0.1));
    }
    .header-titles {
      display: flex;
      flex-direction: column;
    }
    .header-titles h1 {
      margin: 0;
      line-height: 1;
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.55rem;
      font-weight: 600;
      color: #d9bd7a;
      text-transform: uppercase;
      letter-spacing: 0.22em;
    }
    .header-subtitle {
      font-size: 0.58rem;
      color: #c9a96f;
      letter-spacing: 0.26em;
      margin-top: 0.45rem;
      font-family: var(--font);
      font-weight: 600;
      text-transform: uppercase;
    }

    /* ── VIP BADGE ── */
    .vip-header-slot {
      display: flex;
      align-items: center;
      margin-left: 1rem;
      margin-right: 1.15rem;
      flex-shrink: 0;
    }
    .vip-mode-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: linear-gradient(135deg, #D4AF37, #f0d060, #b8962e);
      color: #000;
      font-size: 0.72rem;
      font-weight: 800;
      padding: 5px 14px;
      border-radius: 999px;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      box-shadow: 0 0 16px rgba(212,175,55,0.6);
      animation: vipPulse 2s ease-in-out infinite;
      cursor: pointer;
      border: none;
      font-family: var(--font-family);
      white-space: nowrap;
    }
    .vip-mode-badge:hover { opacity: 0.85; }
    @keyframes vipPulse {
      0%,100% { box-shadow: 0 0 12px rgba(212,175,55,0.5); }
      50%      { box-shadow: 0 0 28px rgba(212,175,55,0.9); }
    }

    @media (max-width: 768px) {
      .site-header {
        min-height: auto;
        padding: 0.85rem 1rem;
        align-items: flex-start;
        gap: 0.8rem;
      }
      .header-left { flex: 1; }
      .site-logo { width: 46px; height: 46px; padding: 5px; }
      .header-titles h1 { font-size: 1.05rem; letter-spacing: 0.14em; }
      .header-subtitle { font-size: 0.46rem; letter-spacing: 0.16em; display: block; }
      .main-nav { display: none; }
      .vip-header-slot { margin-left: auto; margin-right: 0; }
      .hero {
        min-height: auto;
        padding: 0;
        background-position: center center;
      }
      .hero-inner {
        width: calc(100% - 2.6rem);
        top: 53%;
        transform: translate(-50%, -40%);
      }
      .hero h2 { font-size: 2.8rem; letter-spacing: 0.11em; }
      .hero p { font-size: 0.95rem; max-width: 320px; }
      .hero-divider { width: 220px; }
      .hero-divider::before,
      .hero-divider::after { width: 94px; }
      .hero-divider::after { left: 126px; }
      .hero-divider span { left: 101px; }
      .hero-features { padding: 1.5rem 1rem; }
      .feature-grid { grid-template-columns: repeat(2, 1fr); }
      .feature-item { padding: 1rem 0.8rem; border-left: 0; border-top: 1px solid rgba(217,189,122,0.16); }
      .feature-item:nth-child(-n+2) { border-top: 0; }
      .feature-icon { font-size: 2rem; }
      .feature-item h3 { font-size: 0.9rem; }
      .feature-item p { font-size: 0.74rem; }
      .companions-section { padding: 2.6rem 1rem 3rem; }
      .companions-kicker { font-size: 0.68rem; letter-spacing: 0.22em; }
      .companions-section h3 { font-size: 1.55rem; letter-spacing: 0.12em; line-height: 1.25; }
      .companions-frame { padding: 0 1.35rem; }
      .companions-section .profiles-list {
        grid-auto-columns: minmax(135px, 46%);
        gap: 10px;
      }
      .companions-section .profile-card { min-width: 135px; }
      .companions-nav { width: 24px; font-size: 2.1rem; }
      .profiles-list { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 1rem; }
      .companions-view-all { min-width: 190px; font-size: 0.68rem; }
      .modal-gallery-grid { grid-template-columns: repeat(2, 1fr); }
      .chat-window { width: 320px; right: -12px; }
      .modal-close-btn { top: 10px; right: 10px; width: 36px; height: 36px; font-size: 1.2rem; }
      .profile-modal-overlay { padding: 3rem 1rem 1rem; }
      .lb-nav { font-size: 2rem; padding: 10px; }
      .lb-close { top: 10px; right: 15px; }
    }
  </style>
  <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
</head>
<body>
  <header class="site-header">
    <div class="header-left">
      <img src="uploads/logo-removebg-preview.png" alt="Logo" class="site-logo">
      <div class="header-titles">
        <h1>The Orchid Circle</h1>
        <div class="header-subtitle">Discreet. Exclusive. Exceptional.</div>
      </div>
    </div>
    <nav class="main-nav" aria-label="Main navigation">
      <a href="#" class="active">Home</a>
      <a href="#about">About</a>
      <a href="#companions">Companions</a>
      <a href="#membership">Membership</a>
      <a href="#faq">FAQ</a>
      <a href="#contact">Contact</a>
    </nav>
    <?php if ($isVIP): ?>
    <div class="vip-header-slot">
      <button class="vip-mode-badge" onclick="exitVIP()" title="Click to exit VIP mode">&#9733; VIP Mode &#9733;</button>
    </div>
    <?php endif; ?>
  </header>

  <!-- HERO -->
  <section class="hero">
    <img class="hero-bg" src="uploads/mainpic%20%282%29.jpg" alt="">
    <div class="hero-inner">
      <h2>Exclusivity<br><span>In Full Bloom</span></h2>
      <div class="hero-divider"><span>&#10022;</span></div>
      <p>The Orchid Circle is an exclusive introduction to exceptional companions for discerning gentlemen in Manila and beyond.</p>
      <div class="hero-actions">
        <button class="hero-cta" onclick="unlockVIP()">Request Access</button>
        <div class="confidential-note">&#9906; Discreet &amp; Confidential</div>
      </div>
    </div>
  </section>

  <section class="hero-features" id="about">
    <div class="feature-grid">
      <div class="feature-item">
        <div class="feature-icon">&#9819;</div>
        <h3>Exclusive</h3>
        <p>By invitation only. We ensure privacy and the highest standards of discretion.</p>
      </div>
      <div class="feature-item">
        <div class="feature-icon">&#9884;</div>
        <h3>Curated</h3>
        <p>Carefully selected companions who embody elegance, intelligence and charm.</p>
      </div>
      <div class="feature-item">
        <div class="feature-icon">&#9826;</div>
        <h3>Professional</h3>
        <p>Impeccable service with the utmost respect for your time and privacy.</p>
      </div>
      <div class="feature-item">
        <div class="feature-icon">&#9671;</div>
        <h3>Memorable</h3>
        <p>Extraordinary experiences, tailored to your preferences and lifestyle.</p>
      </div>
    </div>
  </section>

  <!-- VIP EXCLUSIVE GRID -->
  <?php if ($isVIP && !empty($vipProfiles)): ?>
  <section class="profiles-section" id="membership" style="background:var(--black); padding-top:4rem; padding-bottom:4rem; margin-bottom:2rem; border-top: 1px solid rgba(212,175,55,0.2); border-bottom: 1px solid rgba(212,175,55,0.2);">
    <h3 style="color:var(--gold); font-family:'Great Vibes', cursive; font-size:3.5rem; text-transform:none; margin-bottom:0;">VIP Exclusives</h3>
    <p class="subtitle" style="color:var(--gray-200); margin-bottom: 2rem;">Elite companions available only to our VIP members.</p>
    <div class="profiles-list">
      <?php foreach ($vipProfiles as $p): ?>
        <div class="profile-card" onclick="openProfile(<?php echo $p['id']; ?>)" style="border: 1px solid var(--gold); background: #1a1a1a;">
          <span class="avail-badge" style="background:var(--gold); color:var(--black);">VIP</span>
          <?php if (!empty($p['image'])): ?>
            <img class="cover" src="uploads/<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
          <?php else: ?>
            <div class="cover-placeholder" style="background:#111; color:var(--gold); border-bottom:1px solid var(--gold);"><?php echo strtoupper(substr($p['name'],0,1)); ?></div>
          <?php endif; ?>
          <div class="card-label">
            <span class="name" style="color:var(--gold);"><?php echo htmlspecialchars($p['name']); ?></span>
            <?php if(!empty($p['age'])): ?><span class="age" style="color:#ccc;"><?php echo $p['age']; ?> yrs</span><?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <!-- PROFILES GRID -->
  <section class="profiles-section companions-section" id="companions">
    <div class="companions-heading">
      <div class="companions-kicker">Our Companions</div>
      <h3>Elegance. Beauty. Sophistication.</h3>
      <div class="companions-divider">&#10022;</div>
    </div>
    <div class="companions-frame">
      <button class="companions-nav prev" type="button" data-companion-scroll="-1" aria-label="Previous companions">&#8249;</button>
      <div class="profiles-list" id="companionsList">
        <?php foreach ($profiles as $p): ?>
          <div class="profile-card" onclick="openProfile(<?php echo $p['id']; ?>)">
            <span class="avail-badge">Available</span>
            <?php if (!empty($p['image'])): ?>
              <img class="cover" src="uploads/<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
            <?php else: ?>
              <div class="cover-placeholder"><?php echo strtoupper(substr($p['name'],0,1)); ?></div>
            <?php endif; ?>
            <div class="card-label">
              <span class="name"><?php echo htmlspecialchars($p['name']); ?></span>
              <?php if(!empty($p['age'])): ?><span class="age"><?php echo $p['age']; ?> yrs</span><?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
        <?php if (empty($profiles)): ?>
          <p style="color: rgba(255,255,255,0.62);">No profiles available yet.</p>
        <?php endif; ?>
      </div>
      <button class="companions-nav next" type="button" data-companion-scroll="1" aria-label="Next companions">&#8250;</button>
    </div>
    <div class="companions-actions">
      <button class="companions-view-all" type="button" onclick="openCompanionsModal()">View All Companions</button>
    </div>
  </section>

  <!-- COMPANIONS MODAL (ALL PROFILES) -->
  <div class="companions-modal-overlay" id="companionsModal">
    <button class="modal-close-btn" id="companionsModalClose">&times;</button>
    <div class="companions-modal-inner">
      <h3 style="color:var(--gold); font-family:'Cormorant Garamond', serif; margin-bottom:0.5rem;">All Available Companions</h3>
      <p style="color:var(--gray-300); margin-bottom:1.2rem;">Browse and book any available companion.</p>
      <div class="companions-modal-grid">
        <?php foreach ($profiles as $p): ?>
          <div class="companion-item" data-id="<?php echo $p['id']; ?>">
            <?php if (!empty($p['image'])): ?>
              <img class="companion-cover" src="uploads/<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" onclick="openProfile(<?php echo $p['id']; ?>)" />
            <?php else: ?>
              <div class="companion-placeholder" onclick="openProfile(<?php echo $p['id']; ?>)"><?php echo strtoupper(substr($p['name'],0,1)); ?></div>
            <?php endif; ?>
            <div class="companion-info">
              <div class="companion-name"><?php echo htmlspecialchars($p['name']); ?></div>
              <?php if(!empty($p['age'])): ?><div class="companion-age"><?php echo $p['age']; ?> yrs</div><?php endif; ?>
            </div>
            <!-- Buttons removed: cover opens profile on single tap -->
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- PROFILE MODAL (FULLSCREEN GRID VIEW) -->
  <div class="profile-modal-overlay" id="profileModalOverlay">
    <button class="modal-close-btn" id="modalCloseBtn">&times;</button>
    <div class="profile-modal" id="profileModal">
      
      <div class="modal-top-info">
        <h2 id="dName">Loading...</h2>
        <div class="modal-stats" id="dStats">
          <!-- Stats go here -->
        </div>
        <div class="modal-rates" id="dRate"></div>
        <div class="modal-desc" id="dDesc"></div>
        <button class="modal-book-btn" id="dBookBtn">Book Now</button>
      </div>

      <div class="modal-gallery-grid" id="modalGalleryGrid">
        <!-- Gallery images go here -->
      </div>

    </div>
  </div>

  <!-- LIGHTBOX OVERLAY -->
  <div id="lightboxOverlay">
    <div id="lbCounter"></div>
    <button class="lb-close" id="lbClose">&times;</button>
    <button class="lb-nav lb-prev" id="lbPrev">&#10094;</button>
    <img id="lightboxImg" src="" alt="">
    <button class="lb-nav lb-next" id="lbNext">&#10095;</button>
  </div>

  <!-- BOOKING MODAL -->
  <div id="bookingModal">
    <div class="bm-inner">
      <button class="bm-close" onclick="closeBooking()" aria-label="Close booking form">&times;</button>
      <h3>Complete Your Booking</h3>
      <form id="bookingForm">
        <input type="hidden" name="profile_id" id="bProfileId">
        <label>Full Name</label>
        <input name="customer_name" required placeholder="Your full name">
        <label>Date</label>
        <input name="booking_date" type="date" required>
        <label>Hours</label>
        <input name="hours" type="number" min="1" value="1">
        <label>Message / Special Request</label>
        <textarea name="message" rows="3" placeholder="Any special requests?"></textarea>
        <button type="submit">Submit Booking Request</button>
      </form>
    </div>
  </div>

  <!-- ══════════════════════════════════════════
       CHAT WIDGET — LUXURY CHATBOT
       ══════════════════════════════════════════ -->
  <div class="chat-widget" id="chatWidget">
    <!-- Floating button -->
    <button class="chat-trigger" id="chatTrigger" title="Chat with us" aria-label="Chat with Orchidcircle">
      <svg class="chat-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <path d="M21 6.5A2.5 2.5 0 0 0 18.5 4h-13A2.5 2.5 0 0 0 3 6.5v6A2.5 2.5 0 0 0 5.5 15H8v3l3-3h7.5A2.5 2.5 0 0 0 21 12.5v-6z" fill="#ffffff"/>
      </svg>
      <span class="chat-label">Chat</span>
      <span class="notif" id="chatNotif"></span>
    </button>

    <!-- Chat window -->
    <div class="chat-window" id="chatWindow">
      <div class="cw-header">
        <div class="cw-avatar">OC</div>
        <div class="cw-info">
          <div class="cw-name">Orchidcircle</div>
          <div class="cw-status">Online — We reply fast</div>
        </div>
        <button class="cw-close" id="chatClose">&times;</button>
      </div>
      <div class="cw-messages" id="cwMessages">
        <!-- Welcome message -->
        <div class="cw-msg admin-msg">
          <span class="bot-badge">Orchidcircle Bot</span>
          <div>Hi there! 👋 Welcome to Orchidcircle. How can I help you today?</div>
          <span class="msg-time">Just now</span>
        </div>
      </div>
      <div class="typing-indicator" id="typingIndicator">
        <span></span><span></span><span></span>
      </div>
      <div class="cw-input-bar">
        <input type="text" id="cwInput" placeholder="Type your message..." autocomplete="off">
        <button id="cwSend" title="Send">➤</button>
      </div>
    </div>
  </div>

  <!-- FAQ SECTION -->
  <section class="site-section faq-section" id="faq" style="background: linear-gradient(180deg, #0a0a0a, #0f0f0f); padding: 4rem 2rem; border-top: 1px solid rgba(212,175,55,0.1);">
    <div style="max-width: 900px; margin: 0 auto;">
      <h2 style="color: var(--gold); font-family: 'Cormorant Garamond', serif; font-size: 3rem; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 1px;">Frequently Asked Questions</h2>
      <p style="color: rgba(255,255,255,0.6); margin-bottom: 2.5rem; font-size: 0.95rem;">Everything you need to know about The Orchid Circle.</p>
      
      <div style="display: grid; gap: 1.5rem;">
        <div style="background: rgba(255,255,255,0.03); padding: 1.5rem; border-left: 3px solid var(--gold); border-radius: 4px;">
          <h3 style="color: var(--gold); font-size: 1.1rem; margin-bottom: 0.5rem;">How do I request access?</h3>
          <p style="color: rgba(255,255,255,0.72); line-height: 1.7;">Click the "Request Access" button on the hero section. Submit your details and we will review your request within 24-48 hours. Upon approval, you will receive VIP credentials via email.</p>
        </div>
        
        <div style="background: rgba(255,255,255,0.03); padding: 1.5rem; border-left: 3px solid var(--gold); border-radius: 4px;">
          <h3 style="color: var(--gold); font-size: 1.1rem; margin-bottom: 0.5rem;">What is VIP membership?</h3>
          <p style="color: rgba(255,255,255,0.72); line-height: 1.7;">VIP members enjoy exclusive access to our elite companion roster, priority booking, discounted rates, and 24/7 concierge support. Premium membership ensures the utmost discretion and personalized service.</p>
        </div>
        
        <div style="background: rgba(255,255,255,0.03); padding: 1.5rem; border-left: 3px solid var(--gold); border-radius: 4px;">
          <h3 style="color: var(--gold); font-size: 1.1rem; margin-bottom: 0.5rem;">How are bookings arranged?</h3>
          <p style="color: rgba(255,255,255,0.72); line-height: 1.7;">Simply select your preferred companion, choose your date and time, and submit your booking request. Our team will confirm availability and send you booking details. All arrangements are kept strictly confidential.</p>
        </div>
        
        <div style="background: rgba(255,255,255,0.03); padding: 1.5rem; border-left: 3px solid var(--gold); border-radius: 4px;">
          <h3 style="color: var(--gold); font-size: 1.1rem; margin-bottom: 0.5rem;">Is my privacy guaranteed?</h3>
          <p style="color: rgba(255,255,255,0.72); line-height: 1.7;">Absolute discretion is our cornerstone. We maintain the highest standards of confidentiality. All personal information is encrypted and secure. Your privacy is our top priority.</p>
        </div>
        
        <div style="background: rgba(255,255,255,0.03); padding: 1.5rem; border-left: 3px solid var(--gold); border-radius: 4px;">
          <h3 style="color: var(--gold); font-size: 1.1rem; margin-bottom: 0.5rem;">What payment methods do you accept?</h3>
          <p style="color: rgba(255,255,255,0.72); line-height: 1.7;">We accept bank transfers, credit cards (Visa/Mastercard), and digital wallets. All payments are processed securely with discreet billing descriptions for your privacy.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- FOOTER / CONTACT -->
  <footer class="site-footer" id="contact" style="background: var(--black); padding: 3rem 2rem; border-top: 1px solid rgba(212,175,55,0.2);">
    <div style="max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-bottom: 2rem; color: rgba(255,255,255,0.7);">
      <div>
        <h4 style="color: var(--gold); font-weight: 700; margin-bottom: 1rem; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Contact Us</h4>
        <p style="margin-bottom: 0.5rem;"><strong>Email:</strong> <a href="mailto:hello@orchidcircle.com" style="color: var(--gold); text-decoration: none;">hello@orchidcircle.com</a></p>
        <p style="margin-bottom: 0.5rem;"><strong>Phone:</strong> <a href="tel:+639171234567" style="color: var(--gold); text-decoration: none;">+63 917 123 4567</a></p>
        <p style="margin-bottom: 0.5rem;"><strong>WhatsApp:</strong> <a href="https://wa.me/639171234567" target="_blank" style="color: var(--gold); text-decoration: none;">+63 917 123 4567</a></p>
        <p><strong>Hours:</strong> 24/7 Support Available</p>
      </div>
      
      <div>
        <h4 style="color: var(--gold); font-weight: 700; margin-bottom: 1rem; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Location</h4>
        <p style="margin-bottom: 0.5rem;"><strong>Office:</strong></p>
        <p style="line-height: 1.6;">The Orchid Circle<br>BGC, Taguig City<br>Metro Manila, Philippines</p>
      </div>
      
      <div>
        <h4 style="color: var(--gold); font-weight: 700; margin-bottom: 1rem; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Quick Links</h4>
        <ul style="list-style: none; padding: 0; margin: 0;">
          <li style="margin-bottom: 0.5rem;"><a href="#companions" style="color: rgba(255,255,255,0.7); text-decoration: none;">Browse Companions</a></li>
          <li style="margin-bottom: 0.5rem;"><a href="#membership" style="color: rgba(255,255,255,0.7); text-decoration: none;">Membership</a></li>
          <li style="margin-bottom: 0.5rem;"><a href="#about" style="color: rgba(255,255,255,0.7); text-decoration: none;">About Us</a></li>
          <li><a href="#faq" style="color: rgba(255,255,255,0.7); text-decoration: none;">FAQ</a></li>
        </ul>
      </div>
    </div>
    
    <div style="border-top: 1px solid rgba(212,175,55,0.1); padding-top: 2rem; text-align: center; color: rgba(255,255,255,0.5); font-size: 0.85rem;">
      <p>&copy; <?php echo date('Y'); ?> <span style="color: var(--gold);">Orchidcircle</span>. All rights reserved.<br>
      <span style="font-size: 0.8rem;">Privacy Policy &nbsp;|&nbsp; Terms of Service &nbsp;|&nbsp; Discreet &amp; Confidential</span></p>
    </div>
  </footer>

  <script>
  // ─────────────────────────────────────────────
  // PROFILE MODAL (GRID VIEW)
  // ─────────────────────────────────────────────
  let galleryImages = [];
  let currentLbIndex = 0;

  async function openProfile(id) {
    // If companions modal is open, close it before opening profile modal
    const companionsModal = document.getElementById('companionsModal');
    if (companionsModal && companionsModal.classList.contains('open')) {
      companionsModal.classList.remove('open');
      document.body.style.overflow = '';
    }
    const overlay = document.getElementById('profileModalOverlay');
    const res = await fetch('api/profile.php?id=' + id);
    const p = await res.json();

    document.getElementById('dName').textContent = p.name;
    document.getElementById('dRate').textContent = '₱' + parseFloat(p.rate_per_hour).toLocaleString() + ' / hour';
    
    let desc = p.description || '';
    // Format description with line breaks if any
    document.getElementById('dDesc').innerHTML = desc.replace(/\n/g, '<br>');

    // Build stats similar to screenshot
    let statsHTML = '';
    if (p.age) statsHTML += `<div><strong>Age:</strong> ${p.age}</div>`;
    if (p.height) statsHTML += `<div><strong>Height:</strong> ${p.height}</div>`;
    if (p.waist || p.cup_size) {
      let m = [];
      if (p.cup_size) m.push(p.cup_size);
      if (p.waist) m.push(p.waist);
      statsHTML += `<div><strong>Measurements:</strong> ${m.join(' - ')}</div>`;
    }
    document.getElementById('dStats').innerHTML = statsHTML;

    document.getElementById('dBookBtn').onclick = () => openBooking(p.id);

    galleryImages = (p.gallery && p.gallery.length > 0)
      ? p.gallery.map(img => 'uploads/' + img)
      : (p.image ? ['uploads/' + p.image] : []);
    
    // Always include cover image at the beginning if not present
    if (p.image && galleryImages[0] !== 'uploads/' + p.image) {
      galleryImages.unshift('uploads/' + p.image);
    }

    renderGalleryGrid();
    overlay.classList.add('open');
    document.body.style.overflow = 'hidden';
    document.documentElement.style.overflow = 'hidden';
    document.body.classList.add('modal-open');
    
    // Scroll to top of modal when opening
    overlay.scrollTop = 0;
  }

  function renderGalleryGrid() {
    const grid = document.getElementById('modalGalleryGrid');
    if (galleryImages.length === 0) {
      grid.innerHTML = ''; 
      return;
    }
    
    grid.innerHTML = galleryImages.map((img, idx) => `<img src="${img}" alt="Gallery Image" onclick="openLightbox(${idx})">`).join('');
  }

  // ─────────────────────────────────────────────
  // COMPANIONS (ALL) MODAL
  // ─────────────────────────────────────────────
  function openCompanionsModal() {
    document.getElementById('companionsModal').classList.add('open');
    document.body.style.overflow = 'hidden';
    document.documentElement.style.overflow = 'hidden';
    document.body.classList.add('modal-open');
    // focus first interactive element for accessibility
    const firstBtn = document.querySelector('#companionsModal .companion-cover');
    if (firstBtn) firstBtn.focus();
  }
  function closeCompanionsModal() {
    document.getElementById('companionsModal').classList.remove('open');
    document.body.style.overflow = '';
    document.documentElement.style.overflow = '';
    document.body.classList.remove('modal-open');
  }
  document.getElementById('companionsModalClose').onclick = closeCompanionsModal;
  document.getElementById('companionsModal').addEventListener('click', e => { if (e.target === document.getElementById('companionsModal')) closeCompanionsModal(); });
  // close with Escape
  document.addEventListener('keydown', e => { if (e.key === 'Escape') {
    if (document.getElementById('companionsModal').classList.contains('open')) closeCompanionsModal();
  }});

  // Simplified modal click handling: covers already have onclick handlers in HTML
  // Block clicks on other card areas (name, age labels)
  document.addEventListener('click', function(e){
    const item = e.target.closest('.companion-item');
    if (!item) return;
    const modal = item.closest('#companionsModal');
    if (!modal || !modal.classList.contains('open')) return;
    // Allow: cover images and placeholders (they have onclick="openProfile()")
    if (e.target.closest('.companion-cover') || e.target.closest('.companion-placeholder')) {
      return;
    }
    // Block other clicks on the card body (name/age text)
    const isCardBody = e.target.closest('.companion-info') || e.target.closest('.companion-name') || e.target.closest('.companion-age');
    if (isCardBody) {
      e.stopPropagation();
    }
  }, false);

  // ─────────────────────────────────────────────
  // LIGHTBOX LOGIC
  // ─────────────────────────────────────────────
  function openLightbox(idx) {
    currentLbIndex = idx;
    updateLightboxImg();
    document.getElementById('lightboxOverlay').classList.add('open');
  }
  function updateLightboxImg() {
    document.getElementById('lightboxImg').src = galleryImages[currentLbIndex];
    document.getElementById('lbCounter').textContent = (currentLbIndex + 1) + ' / ' + galleryImages.length;
  }
  function nextLightbox() {
    currentLbIndex = (currentLbIndex + 1) % galleryImages.length;
    updateLightboxImg();
  }
  function prevLightbox() {
    currentLbIndex = (currentLbIndex - 1 + galleryImages.length) % galleryImages.length;
    updateLightboxImg();
  }
  function closeLightbox() {
    document.getElementById('lightboxOverlay').classList.remove('open');
  }

  document.getElementById('lbNext').onclick = (e) => { e.stopPropagation(); nextLightbox(); };
  document.getElementById('lbPrev').onclick = (e) => { e.stopPropagation(); prevLightbox(); };
  document.getElementById('lbClose').onclick = closeLightbox;
  document.getElementById('lightboxOverlay').onclick = (e) => {
    if (e.target === document.getElementById('lightboxOverlay') || e.target === document.getElementById('lightboxImg')) {
      closeLightbox();
    }
  };

  function closeProfileModal() { 
    document.getElementById('profileModalOverlay').classList.remove('open'); 
    document.body.style.overflow = ''; 
    document.documentElement.style.overflow = '';
    document.body.classList.remove('modal-open');
  }
  
  document.getElementById('modalCloseBtn').onclick = closeProfileModal;
  
  document.getElementById('profileModalOverlay').addEventListener('click', e => { 
    if(e.target === document.getElementById('profileModalOverlay')) closeProfileModal(); 
  });
  
  document.addEventListener('keydown', e => {
    const lbOpen = document.getElementById('lightboxOverlay').classList.contains('open');
    const profileOpen = document.getElementById('profileModalOverlay').classList.contains('open');

    if (lbOpen) {
      if (e.key === 'Escape') closeLightbox();
      if (e.key === 'ArrowRight') nextLightbox();
      if (e.key === 'ArrowLeft') prevLightbox();
    } else if (profileOpen) {
      if (e.key === 'Escape') closeProfileModal();
    }
  });

  // ─────────────────────────────────────────────
  // BOOKING MODAL
  // ─────────────────────────────────────────────
  function openBooking(pid) { document.getElementById('bProfileId').value=pid; document.getElementById('bookingModal').classList.add('open'); }
  function closeBooking() { document.getElementById('bookingModal').classList.remove('open'); }
  document.getElementById('bookingModal').addEventListener('click', e => { if(e.target===document.getElementById('bookingModal')) closeBooking(); });
  document.getElementById('bookingForm').addEventListener('submit', async e => {
    e.preventDefault();
    const form = new FormData(e.target);
    try {
      const res = await fetch('api/book.php',{method:'POST',body:form});
      const data = await res.json();
      alert(data.message||'Booking submitted!');
      closeBooking(); closeProfileModal(); e.target.reset();
    } catch(err) { alert('Error submitting booking.'); }
  });

  document.querySelectorAll('[data-companion-scroll]').forEach(btn => {
    btn.addEventListener('click', () => {
      const list = document.getElementById('companionsList');
      if (!list) return;
      const direction = Number(btn.dataset.companionScroll || 1);
      list.scrollBy({ left: direction * Math.max(260, list.clientWidth * 0.7), behavior: 'smooth' });
    });
  });

  // ─────────────────────────────────────────────
  // VIP UNLOCK
  // ─────────────────────────────────────────────
  window.unlockVIP = function() {
    document.getElementById('vipCustomModal').classList.remove('hidden');
    document.getElementById('vipCustomCode').focus();
  };

  window.exitVIP = function() {
    document.getElementById('vipExitModal').classList.remove('hidden');
  };

  window.closeVipExitModal = function() {
    document.getElementById('vipExitModal').classList.add('hidden');
  };

  window.confirmExitVip = function() {
    document.cookie = "vip_unlocked=; path=/; max-age=0";
    window.location.reload();
  };

  window.closeCustomVip = function() {
    document.getElementById('vipCustomModal').classList.add('hidden');
    document.getElementById('vipCustomCode').value = '';
    document.getElementById('vipCustomError').textContent = '';
  };

  window.submitCustomVip = async function() {
    const code = document.getElementById('vipCustomCode').value.trim();
    if(!code) return;

    // Secret master code for VIP Gallery
    if (code.toLowerCase() === "orchidcirclephilippines1") {
      document.cookie = "vip_unlocked=1; path=/; max-age=86400"; // 24 hours
      window.location.reload();
      return;
    }

    // Normal VIP Gallery Code check
    const res = await fetch('api/vip_check.php?code='+encodeURIComponent(code));
    const data = await res.json();
    if(data.valid){ 
       document.cookie = "vip_unlocked=1; path=/; max-age=86400"; // 24 hours
       window.location.reload(); 
    } else { 
       document.getElementById('vipCustomError').textContent = '❌ Invalid code! Please try again.'; 
    }
  };

  // ─────────────────────────────────────────────
  // CHATBOT WIDGET
  // ─────────────────────────────────────────────
  const chatTrigger = document.getElementById('chatTrigger');
  const chatWindow = document.getElementById('chatWindow');
  const chatClose = document.getElementById('chatClose');
  const cwMessages = document.getElementById('cwMessages');
  const cwInput = document.getElementById('cwInput');
  const cwSend = document.getElementById('cwSend');
  const typingIndicator = document.getElementById('typingIndicator');
  const chatNotif = document.getElementById('chatNotif');

  let chatOpen = false;
  let lastFetchTime = null;
  let chatUserName = 'Guest';

  function getChatSessionId() {
    let sid = sessionStorage.getItem('chat_session_id');
    if (!sid) {
      sid = 'session_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now();
      sessionStorage.setItem('chat_session_id', sid);
    }
    return sid;
  }
  const chatSessionId = getChatSessionId();

  chatTrigger.addEventListener('click', () => {
    chatOpen = !chatOpen;
    chatWindow.classList.toggle('open', chatOpen);
    if (chatOpen) { chatNotif.style.display = 'none'; cwInput.focus(); loadChatHistory(); }
  });
  chatClose.addEventListener('click', () => {
    chatOpen = false;
    chatWindow.classList.remove('open');
  });

  function formatTime(dateStr) {
    const d = new Date(dateStr);
    const h = d.getHours(); const m = d.getMinutes();
    const ampm = h >= 12 ? 'PM' : 'AM';
    return ((h%12)||12) + ':' + String(m).padStart(2,'0') + ' ' + ampm;
  }

  function renderMessage(msg) {
    const isAdmin = msg.sender_type === 'admin';
    const div = document.createElement('div');
    div.className = `cw-msg ${isAdmin ? 'admin-msg' : 'user-msg'}`;
    div.innerHTML = `
      ${isAdmin ? '<span class="bot-badge">' + (msg.sender_name || 'Orchidcircle') + '</span>' : ''}
      <div>${msg.message}</div>
      <span class="msg-time">${formatTime(msg.created_at)}</span>
    `;
    cwMessages.appendChild(div);
  }

  async function loadChatHistory() {
    try {
      const res = await fetch('api/chat.php?action=fetch&session_id=' + encodeURIComponent(chatSessionId));
      const data = await res.json();
      // Keep the welcome message, clear the rest
      cwMessages.innerHTML = `
        <div class="cw-msg admin-msg">
          <span class="bot-badge">Orchidcircle Bot</span>
          <div>Hi there! 👋 Welcome to Orchidcircle. How can I help you today?</div>
          <span class="msg-time">—</span>
        </div>
      `;
      data.forEach(m => renderMessage(m));
      cwMessages.scrollTop = cwMessages.scrollHeight;
      if (data.length > 0) lastFetchTime = data[data.length-1].created_at;
    } catch(e) {}
  }

  async function sendMessage() {
    const text = cwInput.value.trim();
    if (!text) return;
    cwInput.value = '';

    // Immediately render user bubble
    const now = new Date();
    const userMsg = {
      sender_type: 'user',
      sender_name: chatUserName,
      message: text,
      created_at: now.toISOString()
    };
    renderMessage(userMsg);
    cwMessages.scrollTop = cwMessages.scrollHeight;

    // Show typing indicator
    typingIndicator.classList.add('show');
    cwMessages.scrollTop = cwMessages.scrollHeight;

    try {
      const res = await fetch('api/chat.php?action=send', {
        method: 'POST',
        body: new URLSearchParams({ session_id: chatSessionId, sender_type:'user', sender_name: chatUserName, message: text })
      });
      const data = await res.json();

      // Simulate typing delay for auto-reply
      setTimeout(() => {
        typingIndicator.classList.remove('show');
        if (data.auto_reply) {
          const botMsg = {
            sender_type: 'admin',
            sender_name: 'Orchidcircle Bot',
            message: data.auto_reply,
            created_at: new Date().toISOString()
          };
          renderMessage(botMsg);
          cwMessages.scrollTop = cwMessages.scrollHeight;
        }
      }, 800 + Math.random() * 700); // 800ms–1500ms delay for realism
    } catch(err) {
      typingIndicator.classList.remove('show');
    }
  }

  cwSend.addEventListener('click', sendMessage);
  cwInput.addEventListener('keydown', e => { if(e.key==='Enter') sendMessage(); });

  // Poll for new admin replies every 5 seconds
  setInterval(async () => {
    if (!chatOpen) return;
    try {
      const since = lastFetchTime || '1970-01-01 00:00:00';
      const res = await fetch('api/chat.php?action=fetch&session_id=' + encodeURIComponent(chatSessionId) + '&since=' + encodeURIComponent(since));
      const data = await res.json();
      let newAdmin = false;
      data.forEach(m => {
        // Only render if it's a NEW admin message (not auto-replies we already rendered)
        if (m.sender_type === 'admin' && m.sender_name !== 'Orchidcircle Bot') {
          renderMessage(m);
          newAdmin = true;
        }
      });
      if (data.length > 0) lastFetchTime = data[data.length-1].created_at;
      if (newAdmin) cwMessages.scrollTop = cwMessages.scrollHeight;
    } catch(e) {}
  }, 5000);

  // Notify when chat is closed and new messages arrive
  setInterval(async () => {
    if (chatOpen) return;
    try {
      const since = lastFetchTime || '1970-01-01 00:00:00';
      const res = await fetch('api/chat.php?action=fetch&session_id=' + encodeURIComponent(chatSessionId) + '&since=' + encodeURIComponent(since));
      const data = await res.json();
      if (data.some(m => m.sender_type === 'admin' && m.sender_name !== 'Orchidcircle Bot')) {
        chatNotif.style.display = 'block';
      }
      if (data.length > 0) lastFetchTime = data[data.length-1].created_at;
    } catch(e) {}
  }, 8000);
  </script>

  <!-- VIP EXIT CONFIRM MODAL -->
  <div id="vipExitModal" class="modal hidden" onclick="if(event.target === this) closeVipExitModal();">
    <div class="vip-confirm-card">
      <div class="vip-confirm-mark">&#9733;</div>
      <h2>Exit VIP Mode?</h2>
      <p>Your private gallery access will be hidden until you enter a VIP code again.</p>
      <div class="vip-confirm-actions">
        <button type="button" class="vip-confirm-cancel" onclick="closeVipExitModal()">Stay VIP</button>
        <button type="button" class="vip-confirm-exit" onclick="confirmExitVip()">Exit Mode</button>
      </div>
    </div>
  </div>

  <!-- CUSTOM VIP MODAL -->
  <div id="vipCustomModal" class="modal hidden">
    <div class="modal-content" style="max-width:400px; width:90%; text-align:center; padding:2.5rem; background:var(--black); border: 2px solid var(--gold); border-radius:15px; box-shadow: 0 0 30px rgba(212,175,55,0.4);">
      <h2 style="color:var(--gold); font-family:'Great Vibes', cursive; font-size:3.5rem; margin-bottom:0.5rem; line-height:1; font-weight:normal;">VIP Access</h2>
      <p style="color:var(--gray-200); margin-bottom:1.5rem; font-size:0.95rem;">Enter your exclusive code to unlock the private gallery.</p>
      
      <input type="password" id="vipCustomCode" placeholder="Enter VIP Code..." style="width:100%; padding:1rem; border-radius:8px; border:1px solid var(--gold); background:#1a1a1a; color:var(--gold); text-align:center; font-size:1.2rem; margin-bottom:0.5rem; outline:none; font-weight:bold; letter-spacing:2px;">
      <div id="vipCustomError" style="color:#ef4444; font-size:0.85rem; margin-bottom:1.5rem; min-height:20px;"></div>
      
      <div style="display:flex; gap:10px; justify-content:center;">
        <button onclick="submitCustomVip()" class="btn-gold" style="flex:1; padding:0.8rem; font-size:1.05rem;">Unlock</button>
        <button onclick="closeCustomVip()" style="flex:1; padding:0.8rem; background:#333; color:#fff; border:none; border-radius:8px; cursor:pointer; font-weight:600;">Cancel</button>
      </div>
    </div>
  </div>

</body>
</html>
