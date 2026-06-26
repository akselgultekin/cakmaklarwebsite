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
