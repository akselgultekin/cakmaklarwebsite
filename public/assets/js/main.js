    const $ = (selector, scope = document) => scope.querySelector(selector);
    const $$ = (selector, scope = document) => Array.from(scope.querySelectorAll(selector));

    const header     = $("#header");
    const mobilePanel = $("#mobilePanel");
    const hamburger   = $("#hamburger");
    const toast       = $("#toast");
    const parallaxCard = $("#parallaxCard");

    // ── Toast ────────────────────────────────────────────────────────────
    function showToast(message) {
      if (!toast) return;
      toast.textContent = message;
      toast.classList.add("show");
      clearTimeout(showToast.timer);
      showToast.timer = setTimeout(() => toast.classList.remove("show"), 2600);
    }

    // ── Header scroll / parallax ─────────────────────────────────────────
    window.addEventListener("scroll", () => {
      if (header) header.classList.toggle("scrolled", window.scrollY > 24);
      if (parallaxCard) {
        const rect = parallaxCard.closest(".parallax-band").getBoundingClientRect();
        const offset = Math.max(-44, Math.min(44, rect.top * -0.08));
        parallaxCard.style.setProperty("--parallax-offset", `${offset}px`);
      }
    });

    // ── Hamburger ────────────────────────────────────────────────────────
    if (hamburger && mobilePanel) {
      hamburger.addEventListener("click", () => {
        mobilePanel.classList.toggle("open");
        document.body.classList.toggle("menu-open", mobilePanel.classList.contains("open"));
      });
      $$("#mobilePanel a").forEach(link => {
        link.addEventListener("click", () => {
          mobilePanel.classList.remove("open");
          document.body.classList.remove("menu-open");
        });
      });
    }

    // ── Custom selects ───────────────────────────────────────────────────
    $$(".field select").forEach(select => {
      const wrapper = document.createElement("div");
      wrapper.className = "custom-select";
      const trigger = document.createElement("button");
      trigger.type = "button";
      trigger.className = "custom-select-trigger";
      trigger.setAttribute("aria-haspopup", "listbox");
      trigger.innerHTML = `<span>${select.options[select.selectedIndex].text}</span><i class="fa-solid fa-chevron-down"></i>`;
      const options = document.createElement("div");
      options.className = "custom-options";
      options.setAttribute("role", "listbox");

      Array.from(select.options).forEach((option, index) => {
        const item = document.createElement("button");
        item.type = "button";
        item.className = `custom-option${index === select.selectedIndex ? " selected" : ""}`;
        item.textContent = option.text;
        item.setAttribute("role", "option");
        item.addEventListener("click", () => {
          select.selectedIndex = index;
          select.dispatchEvent(new Event("change", { bubbles: true }));
          trigger.querySelector("span").textContent = option.text;
          $$(".custom-option", options).forEach(node => node.classList.toggle("selected", node === item));
          wrapper.classList.remove("open");
          trigger.setAttribute("aria-expanded", "false");
        });
        options.appendChild(item);
      });

      trigger.addEventListener("click", event => {
        event.stopPropagation();
        const willOpen = !wrapper.classList.contains("open");
        $$(".custom-select.open").forEach(node => node.classList.remove("open"));
        wrapper.classList.toggle("open", willOpen);
        trigger.setAttribute("aria-expanded", String(willOpen));
      });
      select.insertAdjacentElement("afterend", wrapper);
      wrapper.append(trigger, options);
    });
    document.addEventListener("click", () => $$(".custom-select.open").forEach(node => node.classList.remove("open")));

    // ── Stat counter animation ───────────────────────────────────────────
    const statValues = $$(".stat-card strong[data-count]");
    if (statValues.length > 0) {
      const animateStat = stat => {
        const target = Number(stat.dataset.count);
        const start = performance.now();
        const duration = 1250;
        const tick = now => {
          const progress = Math.min((now - start) / duration, 1);
          const eased = 1 - Math.pow(1 - progress, 3);
          stat.textContent = `${Math.round(target * eased)}${progress === 1 ? stat.dataset.suffix : ""}`;
          if (progress < 1) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
      };
      const statsObserver = new IntersectionObserver(entries => {
        entries.forEach(entry => {
          if (!entry.isIntersecting) return;
          statValues.forEach(animateStat);
          statsObserver.disconnect();
        });
      }, { threshold: .45 });
      const statsSection = $(".stats");
      if (statsSection) statsObserver.observe(statsSection);
    }

    // ── Hero slider (sadece ana sayfa) ───────────────────────────────────
    const slides   = $$(".slide");
    const dotsWrap = $("#dots");
    const prevBtn  = $("#prevSlide");
    const nextBtn  = $("#nextSlide");

    if (slides.length > 0 && dotsWrap && prevBtn && nextBtn) {
      let slideIndex = 0;
      let slideTimer;

      slides.forEach((_, index) => {
        const dot = document.createElement("button");
        dot.className = index === 0 ? "dot active" : "dot";
        dot.setAttribute("aria-label", `${index + 1}. slayta geç`);
        dot.addEventListener("click", () => setSlide(index));
        dotsWrap.appendChild(dot);
      });

      function setSlide(index) {
        slideIndex = (index + slides.length) % slides.length;
        slides.forEach((slide, i) => slide.classList.toggle("active", i === slideIndex));
        $$(".dot", dotsWrap).forEach((dot, i) => dot.classList.toggle("active", i === slideIndex));
        clearInterval(slideTimer);
        slideTimer = setInterval(() => setSlide(slideIndex + 1), 11000);
      }

      prevBtn.addEventListener("click", () => setSlide(slideIndex - 1));
      nextBtn.addEventListener("click", () => setSlide(slideIndex + 1));
      slideTimer = setInterval(() => setSlide(slideIndex + 1), 11000);
    }

    // ── Search tabs (hızlı arama) ─────────────────────────────────────────
    const quickSearch = $("#quickSearch");
    $$(".search-tabs .tab-btn").forEach(btn => {
      btn.addEventListener("click", () => {
        $$(".search-tabs .tab-btn").forEach(item => item.classList.remove("active"));
        btn.classList.add("active");
        const tab = btn.dataset.searchTab; // satilik | kiralik | projeler
        if (!quickSearch) return;
        if (tab === "projeler") {
          quickSearch.action = quickSearch.dataset.baseUrl
            ? quickSearch.dataset.baseUrl.replace("__TAB__", "projeler")
            : window.location.origin + "/projeler";
          // Projeler için tip dropdown gizle
          const tipField = quickSearch.querySelector('[name="tip"]')?.closest(".field");
          const odaField = quickSearch.querySelector('[name="oda"]')?.closest(".field");
          if (tipField) tipField.style.display = "none";
          if (odaField) odaField.style.display = "none";
        } else {
          const base = window.location.origin;
          quickSearch.action = base + "/" + tab;
          const tipField = quickSearch.querySelector('[name="tip"]')?.closest(".field");
          const odaField = quickSearch.querySelector('[name="oda"]')?.closest(".field");
          if (tipField) tipField.style.display = "";
          if (odaField) odaField.style.display = "";
        }
      });
    });

    // ── 3D Tur — oda verisi ───────────────────────────────────────────────
    const roomData = {
      salon:  ["Panorama Salon",      "Geniş oturma alanı, doğal ışık ve şehir manzarası.",          "https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1400&q=80"],
      mutfak: ["Modern Mutfak",       "Ada tezgah, ankastre alanlar ve ferah depolama çözümleri.",    "https://images.unsplash.com/photo-1600566752355-35792bedcfea?auto=format&fit=crop&w=1400&q=80"],
      yatak:  ["Ebeveyn Yatak Odası", "Sakin renk paleti, giyinme alanı ve konforlu planlama.",       "https://images.unsplash.com/photo-1616594039964-ae9021a400a0?auto=format&fit=crop&w=1400&q=80"],
      banyo:  ["Premium Banyo",       "Büyük seramik yüzeyler, modern armatürler ve spa hissi.",      "https://images.unsplash.com/photo-1620626011761-996317b8d101?auto=format&fit=crop&w=1400&q=80"]
    };

    function switchRoom(roomKey) {
      const room  = roomData[roomKey];
      if (!room) return;
      const img   = $("#roomImage");
      const title = $("#roomTitle");
      const text  = $("#roomText");
      if (img)   { img.src = room[2]; img.alt = room[0]; }
      if (title) title.textContent = room[0];
      if (text)  text.textContent  = room[1];
    }

    // Ana sayfadaki .room-btn'ler (home.php — tour bölümü)
    $$("#roomTabs .room-btn").forEach(btn => {
      btn.addEventListener("click", () => {
        $$("#roomTabs .room-btn").forEach(item => item.classList.remove("active"));
        btn.classList.add("active");
        switchRoom(btn.dataset.room);
        showToast(`${roomData[btn.dataset.room]?.[0] ?? ""} görüntüleniyor.`);
      });
    });

    // 3D sunum sayfasındaki .room-tab'lar (tour-3d.php)
    $$(".room-tab[data-room]").forEach(btn => {
      btn.addEventListener("click", () => {
        $$(".room-tab").forEach(item => item.classList.remove("active"));
        btn.classList.add("active");
        switchRoom(btn.dataset.room);
      });
    });

    // ── Mega menü görsel güncelleyici ─────────────────────────────────────
    $$("[data-mega-image]").forEach(link => {
      const setMegaVisual = () => {
        const image = link.closest(".mega-panel")?.querySelector(".mega-visual img");
        if (image) image.src = link.dataset.megaImage;
      };
      link.addEventListener("mouseenter", setMegaVisual);
      link.addEventListener("focus", setMegaVisual);
    });

    // ── AI Chat Widget (frontend-only) ───────────────────────────────────
    (function () {
      const trigger   = document.getElementById('aiTriggerBtn');
      const panel     = document.getElementById('aiPanel');
      const closeBtn  = document.getElementById('aiPanelClose');
      const input     = document.getElementById('aiInput');
      const sendBtn   = document.getElementById('aiSendBtn');
      const msgBox    = document.getElementById('aiMessages');
      const chipsWrap = document.getElementById('aiChips');
      const notify    = document.getElementById('aiNotify');
      const notifyClose = document.getElementById('aiNotifyClose');
      if (!trigger || !panel) return;

      // ── Bilgi tabanı ──────────────────────────────────────────────────
      const KB = [
        {
          match: ['satılık ev', 'satılık konut', 'satılık daire', 'satılık villa', 'satın almak', 'ev almak', 'konut almak', 'satılık'],
          reply: 'Satılık konut portföyümüzde daire, villa ve arsa seçenekleri mevcut. Güncel ilanlarımızı inceleyebilirsiniz.',
          links: [{ text: '🏠 Satılık İlanları Gör', url: '/satilik' }, { text: '📞 Fiyat Bilgisi Al', url: 'tel:+905336227493' }]
        },
        {
          match: ['kiralık ev', 'kiralık daire', 'kiralık dükkan', 'kiralık ofis', 'kiralık konut', 'kiralamak', 'kira', 'kiracı', 'kiralık'],
          reply: 'Kiralık konut ve işyeri seçeneklerimiz için portföyümüzü incelemenizi öneririz.',
          links: [{ text: '🏡 Kiralık İlanları Gör', url: '/kiralik' }, { text: '🏢 Ticari İlanlar', url: '/ticari' }]
        },
        {
          match: ['3d tur', 'sanal tur', '3d ev', '360', 'sanal gezi', 'ev gez', 'virtual', '3d'],
          reply: 'Projelerimizi evinizden 360° sanal tur ile keşfedebilirsiniz! Gerçekçi deneyim için tıklayın.',
          links: [{ text: '🎯 3D Sanal Turu Başlat', url: '/3d-ev-gez' }]
        },
        {
          match: ['araç', 'araba', 'vasıta', 'otomobil', 'araç ilanı', 'araç al', 'sıfır araç', 'ikinci el'],
          reply: 'Araç portföyümüzde çeşitli seçenekler bulunmaktadır.',
          links: [{ text: '🚗 Araç İlanlarını Gör', url: '/arac-ilanlari' }]
        },
        {
          match: ['proje', 'yeni proje', 'yapım aşamasında', 'inşaat projesi', 'projeler'],
          reply: 'Devam eden ve tamamlanan konut projelerimizi inceleyin. Kat planları ve 3D turlarla detaylı bilgi alabilirsiniz.',
          links: [{ text: '🏗️ Projelerimizi Gör', url: '/projeler' }, { text: '📐 Kat Planları', url: '/kat-planlari' }]
        },
        {
          match: ['fiyat', 'ne kadar', 'kaç para', 'fiyatlar', 'ücret', 'değer', 'değerleme', 'metrekare fiyatı'],
          reply: 'Fiyatlar konuma, büyüklüğe ve projeye göre değişmektedir. Güncel fiyat bilgisi için sizi arayalım.',
          links: [{ text: '📞 +90 533 622 74 93', url: 'tel:+905336227493' }, { text: '✉️ İletişim Formu', url: '/iletisim' }]
        },
        {
          match: ['iletişim', 'telefon', 'ara', 'mail', 'adres', 'nerede', 'ofis', 'konum', 'ulaşmak'],
          reply: 'Bize ulaşmak için:\n📍 Tabaklar Mh. Haznedarlar Sk. No: 63/2, Bolu\n⏰ Hafta içi 09:00 – 18:30',
          links: [{ text: '📞 Hemen Ara', url: 'tel:+905336227493' }, { text: '✉️ Mesaj Gönder', url: '/iletisim' }]
        },
        {
          match: ['hakkında', 'kimsiniz', 'siz kimsiniz', 'firma', 'şirket', 'çakmaklar', 'hakkımızda', 'tarihçe', 'deneyim'],
          reply: 'Çakmaklar Grup İnşaat; Bolu\'da yılların deneyimiyle konut projeleri, satılık ve kiralık gayrimenkul ile araç portföyü sunan köklü bir firmadır.',
          links: [{ text: '👥 Biz Kimiz', url: '/biz-kimiz' }]
        },
        {
          match: ['haberler', 'blog', 'duyuru', 'etkinlik', 'yenilik'],
          reply: 'Güncel haberlerimiz ve duyurularımız için haber sayfamızı ziyaret edin.',
          links: [{ text: '📰 Haberler', url: '/haberler' }]
        },
      ];

      const DEFAULT_REPLY = 'Anladım! Size daha iyi yardımcı olmak için lütfen ofisimizi arayın veya iletişim formumuzu doldurun.';
      const DEFAULT_LINKS = [{ text: '📞 Bizi Arayın', url: 'tel:+905336227493' }, { text: '✉️ İletişim', url: '/iletisim' }];

      function findAnswer(text) {
        const t = text.toLowerCase().trim();
        for (const item of KB) {
          if (item.match.some(kw => t.includes(kw))) return item;
        }
        return null;
      }

      // ── UI yardımcıları ───────────────────────────────────────────────
      function scrollBottom() { msgBox.scrollTop = msgBox.scrollHeight; }

      function addMsg(role, text, links) {
        const row = document.createElement('div');
        row.className = 'ai-msg-row ' + role;
        const bub = document.createElement('div');
        bub.className = 'ai-bubble';
        bub.innerHTML = text.replace(/\n/g, '<br>');
        row.appendChild(bub);
        if (links && links.length) {
          const lw = document.createElement('div');
          lw.className = 'ai-link-btns';
          links.forEach(l => {
            const a = document.createElement('a');
            a.className = 'ai-link-btn';
            a.href = l.url;
            a.textContent = l.text;
            if (l.url.startsWith('/')) a.setAttribute('data-internal', '1');
            else a.target = '_blank';
            lw.appendChild(a);
          });
          bub.appendChild(lw);
        }
        msgBox.appendChild(row);
        scrollBottom();
        return row;
      }

      function addTyping() {
        const row = document.createElement('div');
        row.className = 'ai-msg-row bot';
        row.innerHTML = '<div class="ai-bubble"><div class="ai-typing-dots"><span></span><span></span><span></span></div></div>';
        msgBox.appendChild(row);
        scrollBottom();
        return row;
      }

      function respond(userText) {
        addMsg('user', userText);
        const typing = addTyping();
        setTimeout(() => {
          typing.remove();
          const ans = findAnswer(userText);
          if (ans) {
            addMsg('bot', ans.reply, ans.links);
          } else {
            addMsg('bot', DEFAULT_REPLY, DEFAULT_LINKS);
          }
          // chip'leri gizle yazışmaya başlayınca
          if (chipsWrap) chipsWrap.style.display = 'none';
        }, 650);
      }

      // ── Chip tıklamaları ──────────────────────────────────────────────
      document.querySelectorAll('.ai-chip').forEach(chip => {
        chip.addEventListener('click', () => {
          const q = chip.dataset.query || chip.textContent;
          respond(q);
        });
      });

      // ── Input gönder ──────────────────────────────────────────────────
      function send() {
        const txt = (input.value || '').trim();
        if (!txt) return;
        input.value = '';
        respond(txt);
      }
      sendBtn.addEventListener('click', send);
      input.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); send(); } });

      // ── Panel aç/kapa ─────────────────────────────────────────────────
      function openPanel() {
        panel.classList.add('open');
        trigger.classList.add('open');
        panel.setAttribute('aria-hidden', 'false');
        if (notify) notify.classList.add('hidden');
        setTimeout(() => input && input.focus(), 320);
      }
      function closePanel() {
        panel.classList.remove('open');
        trigger.classList.remove('open');
        panel.setAttribute('aria-hidden', 'true');
      }
      trigger.addEventListener('click', () => panel.classList.contains('open') ? closePanel() : openPanel());
      if (closeBtn) closeBtn.addEventListener('click', closePanel);

      // ── Bildirim balonu ───────────────────────────────────────────────
      if (notify) {
        if (notifyClose) {
          notifyClose.addEventListener('click', e => {
            e.stopPropagation();
            notify.classList.add('hidden');
          });
        }
        notify.addEventListener('click', e => {
          if (!e.target.closest('#aiNotifyClose')) openPanel();
        });
        setTimeout(() => notify.classList.add('hidden'), 7000);
      }
    })();
