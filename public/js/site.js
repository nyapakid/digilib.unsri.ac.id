const toggle = document.querySelector(".menu-toggle");
const menu = document.querySelector("#mainMenu");

if (toggle && menu) {
  toggle.addEventListener("click", () => {
    const isOpen = menu.classList.toggle("open");
    toggle.setAttribute("aria-expanded", String(isOpen));
  });

  menu.addEventListener("click", (event) => {
    if (event.target.matches("a")) {
      menu.classList.remove("open");
      toggle.setAttribute("aria-expanded", "false");
    }
  });
}

const hero = document.querySelector(".hero-carousel");
const slides = Array.from(document.querySelectorAll(".hero-slide"));
const dots = Array.from(document.querySelectorAll("[data-hero-slide]"));

if (hero && slides.length > 1) {
  let activeIndex = slides.findIndex((slide) => slide.classList.contains("active"));
  let timerId = null;

  if (activeIndex < 0) {
    activeIndex = 0;
  }

  const showSlide = (index) => {
    activeIndex = (index + slides.length) % slides.length;

    slides.forEach((slide, slideIndex) => {
      const isActive = slideIndex === activeIndex;
      slide.classList.toggle("active", isActive);
      slide.setAttribute("aria-hidden", String(!isActive));
    });

    dots.forEach((dot, dotIndex) => {
      const isActive = dotIndex === activeIndex;
      dot.classList.toggle("active", isActive);
      dot.setAttribute("aria-current", isActive ? "true" : "false");
    });
  };

  const nextSlide = () => showSlide(activeIndex + 1);
  const start = () => {
    stop();
    timerId = window.setInterval(nextSlide, 6000);
  };
  const stop = () => {
    if (timerId) {
      window.clearInterval(timerId);
      timerId = null;
    }
  };

  dots.forEach((dot, index) => {
    dot.addEventListener("click", () => {
      showSlide(index);
      start();
    });
  });

  hero.addEventListener("mouseenter", stop);
  hero.addEventListener("mouseleave", start);
  hero.addEventListener("focusin", stop);
  hero.addEventListener("focusout", start);

  document.addEventListener("visibilitychange", () => {
    if (document.hidden) {
      stop();
      return;
    }

    start();
  });

  showSlide(activeIndex);
  start();
}

const matrixCanvases = hero ? Array.from(hero.querySelectorAll("[data-hero-matrix]")) : [];
const matrixMotionQuery = window.matchMedia("(prefers-reduced-motion: reduce)");

if (hero && matrixCanvases.length && !matrixMotionQuery.matches) {
  const matrixItems = matrixCanvases
    .map((canvas) => ({
      canvas,
      ctx: canvas.getContext("2d"),
      height: 0,
      offset: Math.random() * Math.PI * 2,
      width: 0
    }))
    .filter((item) => item.ctx);
  const gridSize = 30;
  const hoverRadius = 110;
  const mouse = { x: -1000, y: -1000 };
  let frameId = null;
  let lastTime = 0;

  const resizeMatrix = () => {
    matrixItems.forEach((item) => {
      const rect = item.canvas.getBoundingClientRect();
      const pixelRatio = Math.min(window.devicePixelRatio || 1, 2);

      item.width = Math.max(1, Math.round(rect.width));
      item.height = Math.max(1, Math.round(rect.height));
      item.canvas.width = Math.round(item.width * pixelRatio);
      item.canvas.height = Math.round(item.height * pixelRatio);
      item.ctx.setTransform(pixelRatio, 0, 0, pixelRatio, 0, 0);
      item.ctx.clearRect(0, 0, item.width, item.height);
    });
  };

  const updatePointer = (event) => {
    const rect = hero.getBoundingClientRect();

    mouse.x = event.clientX - rect.left;
    mouse.y = event.clientY - rect.top;
  };

  const resetPointer = () => {
    mouse.x = -1000;
    mouse.y = -1000;
  };

  const drawMatrix = (time = 0) => {
    const elapsed = Math.min(32, time - lastTime || 16);

    lastTime = time;

    matrixItems.forEach((item) => {
      const slide = item.canvas.closest(".hero-slide");

      if (!slide || !slide.classList.contains("active")) {
        return;
      }

      const { ctx, height, width } = item;

      if (!width || !height) {
        return;
      }

      item.offset += elapsed * 0.0012;
      ctx.globalCompositeOperation = "destination-out";
      ctx.fillStyle = "rgba(255, 255, 255, 0.19)";
      ctx.fillRect(0, 0, width, height);
      ctx.globalCompositeOperation = "source-over";

      for (let x = 0; x <= width + gridSize; x += gridSize) {
        for (let y = 0; y <= height + gridSize; y += gridSize) {
          const waveX = Math.sin(x * 0.01 + item.offset) * 20;
          const waveY = Math.sin(y * 0.012 + item.offset * 0.85) * 18;
          const baseX = x + waveX + waveY * 0.35;
          const baseY = y + waveY;
          const dx = mouse.x - baseX;
          const dy = mouse.y - baseY;
          const distance = Math.sqrt(dx * dx + dy * dy);
          const force = Math.max(0, (hoverRadius - distance) / hoverRadius);
          const drawX = baseX + dx * force * 0.45;
          const drawY = baseY + dy * force * 0.45;
          const pulse = Math.sin(item.offset + x * 0.012 + y * 0.009) * 0.1;
          const alpha = Math.min(0.26, 0.16 + pulse + force * 0.2);

          ctx.beginPath();
          ctx.arc(drawX, drawY, 1.45 + force * 3.1, 0, Math.PI * 2);
          ctx.fillStyle = `rgba(255, 255, 255, ${alpha})`;
          ctx.fill();
        }
      }

      ctx.globalCompositeOperation = "source-over";
    });

    frameId = window.requestAnimationFrame(drawMatrix);
  };

  const startMatrix = () => {
    if (!frameId) {
      lastTime = 0;
      frameId = window.requestAnimationFrame(drawMatrix);
    }
  };

  const stopMatrix = () => {
    if (frameId) {
      window.cancelAnimationFrame(frameId);
      frameId = null;
    }
  };

  resizeMatrix();
  window.addEventListener("resize", resizeMatrix);
  hero.addEventListener("pointermove", updatePointer);
  hero.addEventListener("pointerleave", resetPointer);
  document.addEventListener("visibilitychange", () => {
    if (document.hidden) {
      stopMatrix();
      return;
    }

    startMatrix();
  });
  startMatrix();
}

const partnerCarousels = Array.from(document.querySelectorAll("[data-partner-carousel]"));

partnerCarousels.forEach((carousel) => {
  const viewport = carousel.querySelector(".partner-viewport");
  const track = carousel.querySelector(".partner-track");
  const originalItems = track ? Array.from(track.querySelectorAll("[data-partner-item]")) : [];
  const partnerReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  if (!viewport || !track || originalItems.length <= 1) {
    return;
  }

  const visibleSlots = 5;
  const cloneRounds = Math.max(1, Math.ceil(visibleSlots / originalItems.length) + 1);
  const originalCount = originalItems.length;
  let currentIndex = originalCount * cloneRounds;
  let itemStep = 0;
  let autoTimerId = null;
  let dragStartX = 0;
  let dragOffset = 0;
  let isDragging = false;
  let resizeFrameId = null;
  let suppressClick = false;

  const prepareClone = (clone) => {
    clone.dataset.partnerClone = "true";
    clone.setAttribute("aria-hidden", "true");
    clone.tabIndex = -1;
    clone.querySelectorAll("a, button, input, select, textarea").forEach((element) => {
      element.tabIndex = -1;
    });
  };

  for (let round = 0; round < cloneRounds; round += 1) {
    originalItems.forEach((item) => {
      const clone = item.cloneNode(true);

      prepareClone(clone);
      track.appendChild(clone);
    });

    [...originalItems].reverse().forEach((item) => {
      const clone = item.cloneNode(true);

      prepareClone(clone);
      track.prepend(clone);
    });
  }

  const setTranslate = (offset = 0, animate = true) => {
    track.style.transition = animate && !partnerReducedMotion ? "transform 0.42s ease" : "none";
    track.style.transform = `translate3d(${offset - currentIndex * itemStep}px, 0, 0)`;
  };

  const measureCarousel = () => {
    const firstItem = track.querySelector(".partner");
    const trackStyle = window.getComputedStyle(track);
    const gap = parseFloat(trackStyle.columnGap || trackStyle.gap || "0") || 0;

    itemStep = firstItem ? firstItem.getBoundingClientRect().width + gap : 0;
    setTranslate(0, false);
  };

  const normalizePosition = () => {
    const lowerLimit = originalCount * cloneRounds;
    const upperLimit = originalCount * (cloneRounds + 1);
    let normalized = false;

    while (currentIndex >= upperLimit) {
      currentIndex -= originalCount;
      normalized = true;
    }

    while (currentIndex < lowerLimit) {
      currentIndex += originalCount;
      normalized = true;
    }

    if (normalized) {
      setTranslate(0, false);
    }
  };

  const moveBy = (amount) => {
    if (!itemStep) {
      measureCarousel();
    }

    currentIndex += amount;
    setTranslate(0, true);
  };

  const stopAutoSlide = () => {
    if (autoTimerId) {
      window.clearInterval(autoTimerId);
      autoTimerId = null;
    }
  };

  const startAutoSlide = () => {
    if (partnerReducedMotion || document.hidden) {
      return;
    }

    stopAutoSlide();
    autoTimerId = window.setInterval(() => moveBy(1), 3600);
  };

  const endDrag = (event) => {
    if (!isDragging) {
      return;
    }

    if (!itemStep) {
      measureCarousel();
    }

    if (!itemStep) {
      isDragging = false;
      dragOffset = 0;
      viewport.classList.remove("is-dragging");
      startAutoSlide();
      return;
    }

    const threshold = itemStep * 0.18;
    let steps = Math.round(-dragOffset / itemStep);

    if (Math.abs(dragOffset) > threshold && steps === 0) {
      steps = dragOffset < 0 ? 1 : -1;
    }

    isDragging = false;
    suppressClick = Math.abs(dragOffset) > 6;
    dragOffset = 0;
    viewport.classList.remove("is-dragging");
    if (event.pointerId !== undefined && viewport.hasPointerCapture?.(event.pointerId)) {
      viewport.releasePointerCapture(event.pointerId);
    }
    moveBy(steps);
    startAutoSlide();
  };

  viewport.addEventListener("pointerdown", (event) => {
    if (event.button !== undefined && event.button !== 0) {
      return;
    }

    isDragging = true;
    dragStartX = event.clientX;
    dragOffset = 0;
    stopAutoSlide();
    viewport.classList.add("is-dragging");
    viewport.setPointerCapture?.(event.pointerId);
    setTranslate(0, false);
  });

  viewport.addEventListener("pointermove", (event) => {
    if (!isDragging) {
      return;
    }

    dragOffset = event.clientX - dragStartX;

    if (Math.abs(dragOffset) > 3) {
      event.preventDefault();
    }

    setTranslate(dragOffset, false);
  });

  viewport.addEventListener("pointerup", endDrag);
  viewport.addEventListener("pointercancel", endDrag);
  viewport.addEventListener("dragstart", (event) => event.preventDefault());
  viewport.addEventListener("click", (event) => {
    if (!suppressClick) {
      return;
    }

    event.preventDefault();
    event.stopPropagation();
    suppressClick = false;
  }, true);

  track.addEventListener("transitionend", (event) => {
    if (event.target === track) {
      normalizePosition();
    }
  });

  carousel.addEventListener("mouseenter", stopAutoSlide);
  carousel.addEventListener("mouseleave", startAutoSlide);
  carousel.addEventListener("focusin", stopAutoSlide);
  carousel.addEventListener("focusout", startAutoSlide);
  carousel.addEventListener("keydown", (event) => {
    if (event.key !== "ArrowLeft" && event.key !== "ArrowRight") {
      return;
    }

    event.preventDefault();
    stopAutoSlide();
    moveBy(event.key === "ArrowRight" ? 1 : -1);
    startAutoSlide();
  });

  window.addEventListener("resize", () => {
    if (resizeFrameId) {
      window.cancelAnimationFrame(resizeFrameId);
    }

    resizeFrameId = window.requestAnimationFrame(measureCarousel);
  });

  document.addEventListener("visibilitychange", () => {
    if (document.hidden) {
      stopAutoSlide();
      return;
    }

    startAutoSlide();
  });

  measureCarousel();
  startAutoSlide();
});

const revealTargets = Array.from(document.querySelectorAll([
  ".page-hero .container",
  ".section-head",
  ".resource-card",
  ".service-card",
  ".facility-item",
  ".staff-card",
  ".panel",
  ".date-row",
  ".news-card",
  ".gallery-card",
  ".stat",
  ".partner",
  ".content-card",
  ".detail-image",
  ".detail-meta",
  ".detail-body"
].join(", ")));

if (revealTargets.length) {
  const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  revealTargets.forEach((element, index) => {
    element.classList.add("reveal-on-scroll");
    element.style.setProperty("--reveal-delay", `${(index % 6) * 55}ms`);
  });

  if (prefersReducedMotion || !("IntersectionObserver" in window)) {
    revealTargets.forEach((element) => element.classList.add("is-visible"));
  } else {
    const revealObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) {
          return;
        }

        entry.target.classList.add("is-visible");
        observer.unobserve(entry.target);
      });
    }, {
      rootMargin: "0px 0px -8% 0px",
      threshold: 0.12
    });

    revealTargets.forEach((element) => revealObserver.observe(element));
  }
}
