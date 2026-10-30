if ('scrollRestoration' in history) {
  history.scrollRestoration = 'manual';
}
window.scrollTo(0, 0);
document.documentElement.scrollTop = 0;
document.body.scrollTop = 0;

const fullpage = document.querySelector("#fullpage");

let isDetailFlowActive = false;
let currentDetailPane = "intro";

let panels = [];

function refreshPanels() {
  const detailPanels = document.querySelectorAll(".panel-detail-flow");
  detailPanels.forEach((p) => {
    if (isDetailFlowActive) {
      p.classList.remove("is-hidden");
    } else {
      p.classList.add("is-hidden");
    }
  });

  if (window.innerWidth <= 760) {
    panels = Array.from(document.querySelectorAll(".panel:not(#connect):not(.is-hidden)"));
  } else {
    panels = Array.from(document.querySelectorAll(".panel:not(.is-hidden)"));
  }
}

refreshPanels();

// Dọn dẹp query param ?service=... trên URL trang chủ để link luôn ngắn gọn
if (window.location.search && window.location.search.includes("service=")) {
  const cleanPath = window.location.pathname.replace(/\/+$/, '') || '/';
  window.history.replaceState(null, "", `${cleanPath}${window.location.hash || ''}`);
}
const downButton = document.querySelector(".scroll-button");
const sectionLinks = document.querySelectorAll('a[href^="#"]:not([data-menu-section])');
const sectionNavLinks = document.querySelectorAll(".service-mini-nav a[href^='#']");
const workflowNavButtons = document.querySelectorAll(".global-workflow-nav button");
const menuTrigger = document.querySelector(".menu-trigger");
const menuOverlay = document.querySelector("#menuOverlay");
const menuClose = document.querySelector(".menu-close");
const menuLinks = document.querySelectorAll("[data-menu-section]");
const serviceGallery = document.querySelector(".service-gallery");
const serviceCards = Array.from(document.querySelectorAll(".service-card"));
const servicePrev = document.querySelector(".gallery-arrow-prev");
const serviceNext = document.querySelector(".gallery-arrow-next");
const whyStatement = document.querySelector(".why-statement");
const whyTitle = document.querySelector(".why-slide-title");
const whyBullets = document.querySelector(".why-bullets");
const whyProgress = document.querySelector(".why-progress span");
const whyPrev = document.querySelector(".why-prev");
const whyNext = document.querySelector(".why-next");
const partnerSliders = Array.from(document.querySelectorAll(".partner-slider"));
const connectForms = document.querySelectorAll(".connect-form");
const projectPanels = Array.from(document.querySelectorAll(".panel-project"));
const expertPanels = Array.from(document.querySelectorAll(".panel-expert"));

let initialHash = window.location.hash;
if (initialHash === "#coverflowProject" || initialHash === "#coverflowService") {
  initialHash = "#coverflow";
}
let currentIndex = Math.max(
  0,
  panels.findIndex((panel) => `#${panel.id}` === initialHash),
);
let locked = false;
let touchStartX = 0;
let touchStartY = 0;
let touchStartedInGallery = false;
let activeServiceIndex = 0;
let serviceScrollFrame = 0;
let serviceDragStartX = 0;
let serviceDragStartY = 0;
let serviceDragStartIndex = 0;
let serviceDragActivated = false;
let serviceDragActive = false;
let serviceDragTimer = 0;
let isProgrammaticServiceScroll = false;
let programmaticServiceScrollTimer = 0;
let activeWhyIndex = 0;

const fallbackProductWorkflows = [
  { what: "about", project: "design-project", expert: "design-expert", connect: "about-connect" },
  { what: "build", project: "build-project", expert: "build-expert", connect: "build-connect" },
  { what: "furniture-design", project: "furniture-project", expert: "furniture-expert", connect: "furniture-design-connect" },
  { what: "maintain", project: "maintain-project", expert: "maintain-expert", connect: "maintain-connect" },
];

const workflowStepKeys = ["what", "project", "expert", "connect"];
const productWorkflows = discoverProductWorkflows();



const activeProjectIndexes = new Map();
const activeExpertIndexes = new Map();
const activeProjectGalleryIndexes = new Map();

const fallbackWhySlides = [
  {
    title: "QUALITY",
    bullets: [
      "Design and engineering team directly involved in production & construction.",
      "Skilled workers are trained from the WorkSpace center.",
      "Key personnel directly involved in the quality control process.",
    ],
  },
  {
    title: "PROGRESS",
    bullets: [
      "Workers have trained to multitask from the centers of WS.",
      "Cooperation in accomplishing goals with strategic partners is the core value.",
      "Flexible culture is highly valued by the WorkSpace management team.",
    ],
  },
  {
    title: "COST EFFECTIVE",
    bullets: [
      "The production and construction process minimizes intermediate costs.",
      "Key material and performance are managed for stronger efficiency.",
      "WorkSpace brings practical solutions before the production and construction stage.",
    ],
  },
  {
    title: "PROBLEM-SOLVING",
    bullets: [
      "Answer to handling design objectives for customers within agreed budgets.",
      "The system keeps technical and site control in one working flow.",
      "Real solutions focus on fast delivery improvement.",
    ],
  },
];

function getSeededWhySlides() {
  const source = document.querySelector("#workspace-why-slides");

  if (!source) {
    return [];
  }

  try {
    const parsed = JSON.parse(source.textContent || "[]");
    return Array.isArray(parsed)
      ? parsed.filter((slide) => slide?.title && Array.isArray(slide.bullets))
      : [];
  } catch {
    return [];
  }
}

const seededWhySlides = getSeededWhySlides();
const whySlides = seededWhySlides.length ? seededWhySlides : fallbackWhySlides;

function clampIndex(index) {
  return Math.max(0, Math.min(index, panels.length - 1));
}

function getPanelOffset(index = currentIndex) {
  return panels[clampIndex(index)]?.offsetTop || 0;
}

function applyFullpageTransform() {
  fullpage.style.transform = `translate3d(0, -${getPanelOffset()}px, 0)`;
}

function getPanelIndexFromLink(link, datasetKey = "section") {
  let hash = link.getAttribute("href");
  if (hash === "#coverflowProject") {
    hash = "#coverflowService";
  }
  let hashIndex = hash ? panels.findIndex((panel) => `#${panel.id}` === hash) : -1;

  if (hashIndex === -1 && hash === "#connect" && window.innerWidth <= 760) {
    hashIndex = panels.findIndex((panel) => panel.id === "why-workspace");
  }

  if (hashIndex >= 0) {
    return hashIndex;
  }

  return Number(link.dataset[datasetKey]);
}

function discoverProductWorkflows() {
  const groups = new Map();

  panels.forEach((panel) => {
    const group = panel.dataset.workflowGroup;
    const step = panel.dataset.workflowStep;

    if (!group || !workflowStepKeys.includes(step)) {
      return;
    }

    const workflow = groups.get(group) || {};
    workflow[step] = panel.id;
    groups.set(group, workflow);
  });

  const discovered = Array.from(groups.values()).filter(
    (workflow) => workflow.what && workflow.project && workflow.expert,
  );

  return discovered.length ? discovered : fallbackProductWorkflows;
}

function getProductWorkflow(panel) {
  return productWorkflows.find((workflow) =>
    workflowStepKeys.some((key) => workflow[key] === panel?.id),
  );
}

function getProductStep(panel) {
  const workflow = getProductWorkflow(panel);

  if (!workflow) {
    return -1;
  }

  return workflowStepKeys.map((key) => workflow[key]).indexOf(panel.id);
}

function getWorkflowById(id) {
  return productWorkflows.find((workflow) =>
    workflowStepKeys.some((key) => workflow[key] === id),
  );
}

function humanizeId(value) {
  return String(value || "")
    .replace(/-project$|-expert$/g, "")
    .replace(/-/g, " ")
    .replace(/\b\w/g, (char) => char.toUpperCase());
}

function getLeadContext(panel = panels[currentIndex]) {
  const activePanel = panel || panels[currentIndex];
  let workflow = getProductWorkflow(activePanel);

  if (!workflow && activePanel?.id === "connect") {
    const hashId = (window.location.hash || "").replace("#", "");
    workflow = getWorkflowById(hashId);
  }

  if (!workflow) {
    const previousWorkflowPanel = panels
      .slice(0, currentIndex + 1)
      .reverse()
      .find((item) => getProductWorkflow(item));

    workflow = getProductWorkflow(previousWorkflowPanel);
  }

  if (!workflow) {
    return {
      service: "",
      project: "",
      step: activePanel?.id || "",
      section: activePanel?.id || "",
    };
  }

  const workflowPanel = workflowStepKeys.some((key) => workflow[key] === activePanel?.id)
    ? activePanel
    : panels.find((item) => item.id === workflow.project) || panels.find((item) => item.id === workflow.what);
  const stepIndex = getProductStep(workflowPanel);
  const projectPanel = panels.find((item) => item.id === workflow.project);
  const projectSlide = projectPanel ? getActiveProjectSlide(projectPanel) : null;

  return {
    service: humanizeId(workflow.what),
    project: projectSlide?.dataset.label || humanizeId(workflow.project),
    step: ["What we do", "Projects", "About the expert", "Connect Workspace"][stepIndex] || humanizeId(workflowPanel?.id),
    section: workflowPanel?.id || activePanel?.id || "",
  };
}

function syncLeadContext(targetForm = null) {
  const forms = targetForm ? [targetForm] : Array.from(connectForms);

  forms.forEach((form) => {
    const formPanel = form.closest(".panel");
    const context = formPanel?.dataset.workflowGroup
      ? getLeadContext(formPanel)
      : getLeadContext();
    const fields = {
      lead_service: form.dataset.leadService || context.service,
      lead_project: context.project,
      lead_step: form.dataset.leadStep || context.step,
      lead_section: form.dataset.leadSection || context.section,
    };

    Object.entries(fields).forEach(([name, value]) => {
      let input = form.querySelector(`input[name="${name}"]`);

      if (!input) {
        input = document.createElement("input");
        input.type = "hidden";
        input.name = name;
        form.prepend(input);
      }

      input.value = value || "";
    });
  });
}

function getActiveMenuHash(panel) {
  if (!panel) {
    return "";
  }

  if (panel.id === "service" || getProductWorkflow(panel)) {
    return "#service";
  }

  if (panel.id === "contact") {
    return "#contact";
  }

  if (panel.id === "why-workspace") {
    return "#why-workspace";
  }

  return `#${panel.id}`;
}

function getActiveSectionNavHash(panel) {
  if (!panel) {
    return "";
  }

  if (panel.id === "core-values") {
    return "#core-values";
  }

  if (panel.id === "service") {
    return "#service";
  }

  if (panel.id === "why-workspace") {
    return "#why-workspace";
  }

  return "";
}

function getActiveProjectSlide(panel) {
  const slides = Array.from(panel.querySelectorAll(".project-slide"));
  return slides[activeProjectIndexes.get(panel.id) || 0] || slides[0] || null;
}

function parseProjectGallery(slide) {
  if (!slide) {
    return [];
  }

  try {
    const gallery = JSON.parse(slide.dataset.gallery || "[]");
    return Array.isArray(gallery) ? gallery.filter(Boolean) : [];
  } catch {
    return slide.dataset.image ? [slide.dataset.image] : [];
  }
}

function getProjectGallery(panel) {
  const slide = getActiveProjectSlide(panel);
  const gallery = parseProjectGallery(slide);

  if (gallery.length) {
    return gallery;
  }

  return slide?.dataset.image ? [slide.dataset.image] : [];
}

function renderProjectLightbox(panel, index) {
  const gallery = getProjectGallery(panel);
  const lightbox = panel.querySelector(".project-lightbox");

  if (!lightbox || !gallery.length) {
    return;
  }

  const activeIndex = (index + gallery.length) % gallery.length;
  activeProjectGalleryIndexes.set(panel.id, activeIndex);

  const image = lightbox.querySelector(".project-lightbox-image");
  const count = lightbox.querySelector(".project-lightbox-count");
  const arrows = lightbox.querySelectorAll(".project-lightbox-prev, .project-lightbox-next");

  if (image) {
    image.src = gallery[activeIndex];
  }

  if (count) {
    count.textContent = `${activeIndex + 1} / ${gallery.length}`;
  }

  arrows.forEach((arrow) => {
    arrow.disabled = gallery.length <= 1;
  });
}

function getOpenProjectLightboxPanel() {
  const openLightbox = document.querySelector(".project-lightbox.is-open");
  return openLightbox ? openLightbox.closest(".panel") : null;
}

function openProjectLightbox(panel) {
  const gallery = getProjectGallery(panel);
  const lightbox = panel.querySelector(".project-lightbox");

  if (!lightbox || !gallery.length) {
    return;
  }

  activeProjectGalleryIndexes.set(panel.id, 0);
  renderProjectLightbox(panel, 0);
  lightbox.classList.add("is-open");
  lightbox.setAttribute("aria-hidden", "false");
  document.body.classList.add("is-project-lightbox-open");
}

function closeProjectLightbox(panel) {
  const lightbox = panel?.querySelector(".project-lightbox");

  if (!lightbox) {
    return;
  }

  lightbox.classList.remove("is-open");
  lightbox.setAttribute("aria-hidden", "true");
  document.body.classList.remove("is-project-lightbox-open");
}



function renderProject(panel, index, direction = 1, animate = false) {
  const slides = Array.from(panel.querySelectorAll(".project-slide"));

  if (!slides.length) {
    return;
  }

  const activeIndex = (index + slides.length) % slides.length;
  const slide = slides[activeIndex];
  const surface = panel.querySelector(".project-surface");

  activeProjectIndexes.set(panel.id, activeIndex);

  const applyProject = () => {
    // Only apply manual display toggling if NOT using Swiper
    if (typeof Swiper === 'undefined') {
      slides.forEach((s, i) => {
        s.style.display = (i === activeIndex) ? "grid" : "none";
      });
    }

    const image = panel.querySelector(".project-bg");
    const label = panel.querySelector(".project-count strong");
    const progress = panel.querySelector(".project-count span");
    const pill = panel.querySelector(".project-pill");

    if (image && slide.dataset.image) {
      if (image.src && !image.src.endsWith(slide.dataset.image) && image.src !== slide.dataset.image) {
        const clone = image.cloneNode();
        image.parentNode.insertBefore(clone, image);
        clone.animate([
          { transform: 'translateX(0)', opacity: 1 },
          { transform: `translateX(${direction > 0 ? '-30px' : '30px'})`, opacity: 0 }
        ], { duration: 500, easing: 'ease-out' }).onfinish = () => clone.remove();

        image.src = slide.dataset.image;
        image.animate([
          { transform: `translateX(${direction > 0 ? '30px' : '-30px'})`, opacity: 0 },
          { transform: 'translateX(0)', opacity: 1 }
        ], { duration: 500, easing: 'ease-out' });
      } else {
        image.src = slide.dataset.image;
      }
    }

    if (label && slide.dataset.label) {
      label.textContent = slide.dataset.label;
    }

    if (progress) {
      progress.style.setProperty("--project-progress", `${((activeIndex + 1) / slides.length) * 100}%`);
    }

    if (pill) {
      pill.disabled = getProjectGallery(panel).length === 0;
    }
  };

  const controls = panel.querySelectorAll(".project-controls .project-prev, .project-controls .project-next");
  if (typeof Swiper === 'undefined') {
    controls.forEach((control) => {
      control.disabled = slides.length <= 1;
    });
  }

  if (!animate || !surface || typeof Swiper !== 'undefined') {
    applyProject();
    syncLeadContext();
    return;
  }

  surface.style.setProperty("--project-slide-offset", `${direction > 0 ? -18 : 18}px`);
  surface.classList.add("is-project-changing");

  window.setTimeout(() => {
    applyProject();
    surface.style.setProperty("--project-slide-offset", `${direction > 0 ? 18 : -18}px`);
    window.requestAnimationFrame(() => {
      surface.classList.remove("is-project-changing");
      syncLeadContext();
    });
  }, 170);
}

function renderExpert(panel, index, direction = 1, animate = false) {
  const slides = Array.from(panel.querySelectorAll(".expert-slide"));

  if (!slides.length) {
    return;
  }

  const activeIndex = (index + slides.length) % slides.length;
  const slide = slides[activeIndex];
  const surface = panel.querySelector(".expert-surface");

  activeExpertIndexes.set(panel.id, activeIndex);

  const applyExpert = () => {
    slides.forEach((s, i) => {
      const isActive = i === activeIndex;
      s.style.display = isActive ? "grid" : "none";
      s.setAttribute("aria-hidden", isActive ? "false" : "true");
    });

    const image = panel.querySelector(".expert-image");
    if (image && slide.dataset.image) {
      image.src = slide.dataset.image;
    }
  };

  panel.querySelectorAll(".expert-prev, .expert-next").forEach((control) => {
    control.disabled = slides.length <= 1;
  });

  if (!animate || !surface) {
    applyExpert();
    return;
  }

  surface.style.setProperty("--expert-slide-offset", `${direction > 0 ? -18 : 18}px`);
  surface.classList.add("is-expert-changing");

  window.setTimeout(() => {
    applyExpert();
    surface.style.setProperty("--expert-slide-offset", `${direction > 0 ? 18 : -18}px`);
    window.requestAnimationFrame(() => {
      surface.classList.remove("is-expert-changing");
    });
  }, 170);
}

function updateControls() {
  const isLast = currentIndex === panels.length - 1;
  const currentPanel = panels[currentIndex];
  const currentProductStep = getProductStep(currentPanel);

  // Manage is-active class for Animate.css and smooth replay
  panels.forEach(panel => {
    if (panel !== currentPanel) {
      panel.classList.remove('is-active');
      // Remove animation class from inactive panels so they can fade out smoothly
      const animatedElements = panel.querySelectorAll('.animate__animated');
      animatedElements.forEach(el => el.classList.remove('animate__animated'));
    }
  });

  if (currentPanel && !currentPanel.classList.contains('is-active')) {
    currentPanel.classList.add('is-active');
    // Add animate__animated asynchronously with a slight delay to avoid scroll stuttering and initial flash
    const animateElements = currentPanel.querySelectorAll('[class*="animate__"]');
    setTimeout(() => {
      window.requestAnimationFrame(() => {
        animateElements.forEach(el => {
          // Prevent adding to the body or panel itself if it happens to have the class name
          if (el !== currentPanel) {
              // Force browser reflow to reset the animation completely
              el.style.animation = 'none';
              el.style.animationName = 'none';
              void el.offsetWidth;
              el.style.animation = '';
              el.style.animationName = '';
              el.classList.add('animate__animated');
          }
        });
      });
    }, 400);
  }

  // Ẩn PDF PROFILE ở màn chi tiết dịch vụ (trùng vị trí "Back to Services")
  document.body.classList.toggle("is-service-detail", currentPanel?.id === "coverflowService");

  const inWorkflow = currentProductStep !== -1;
  document.body.classList.toggle("in-workflow-flow", inWorkflow);
  document.body.setAttribute("data-current-step", currentProductStep);
  document.body.classList.toggle("is-scrolled-past-home", currentIndex > 0);

  const isSectionFlow = ["core-values", "service", "why-workspace"].includes(currentPanel?.id);
  document.body.classList.toggle("in-section-flow", isSectionFlow);

  downButton.classList.toggle("is-last", isLast);
  document.body.classList.toggle("is-workflow-section", currentPanel?.dataset.ui === "dark");
  document.querySelectorAll(".global-workflow-nav button").forEach((button, buttonIndex) => {
      const isCurrent = buttonIndex === currentProductStep;
      button.classList.toggle("is-current", isCurrent);
      button.setAttribute("aria-current", isCurrent ? "page" : "false");
  });
  const activeSectionNavHash = getActiveSectionNavHash(currentPanel);

  sectionNavLinks.forEach((link) => {
    link.classList.remove("is-current");
    link.setAttribute("aria-current", "false");
  });

  sectionNavLinks.forEach((link) => {
    if (link.getAttribute("href") !== activeSectionNavHash) {
      return;
    }

    link.classList.add("is-current");
    link.setAttribute("aria-current", "page");
  });
  const activeMenuHash = getActiveMenuHash(currentPanel);

  menuLinks.forEach((link) => {
    link.classList.remove("is-current");
    link.setAttribute("aria-current", "false");
  });

  menuLinks.forEach((link) => {
    if (link.getAttribute("href") !== activeMenuHash) {
      return;
    }

    link.classList.add("is-current");
    link.setAttribute("aria-current", "page");
  });
  downButton.setAttribute(
    "aria-label",
    isLast ? "Tro ve section dau tien" : "Chuyen xuong section tiep theo",
  );
}

function openMenu() {
  document.body.classList.add("is-menu-open");
  menuTrigger.classList.add("is-active");
  menuTrigger.setAttribute("aria-expanded", "true");
  menuTrigger.setAttribute("aria-label", "Dong menu");
  menuOverlay.classList.add("is-open");
  menuOverlay.setAttribute("aria-hidden", "false");
}

function closeMenu() {
  document.body.classList.remove("is-menu-open");
  menuTrigger.classList.remove("is-active");
  menuTrigger.setAttribute("aria-expanded", "false");
  menuTrigger.setAttribute("aria-label", "Mo menu");
  menuOverlay.classList.remove("is-open");
  menuOverlay.setAttribute("aria-hidden", "true");
}

function toggleMenu() {
  if (menuOverlay.classList.contains("is-open")) {
    closeMenu();
    return;
  }

  openMenu();
}

function serviceGalleryCanScroll() {
  return serviceGallery && serviceGallery.scrollWidth > serviceGallery.clientWidth + 2;
}

function getNearestServiceCardIndex() {
  if (!serviceGallery || !serviceCards.length) {
    return activeServiceIndex;
  }

  const galleryRect = serviceGallery.getBoundingClientRect();
  const galleryCenter = galleryRect.left + galleryRect.width / 2;
  let nearestIndex = activeServiceIndex;
  let nearestDistance = Number.POSITIVE_INFINITY;

  serviceCards.forEach((card, cardIndex) => {
    const cardRect = card.getBoundingClientRect();
    const visibleWidth = Math.min(cardRect.right, galleryRect.right) - Math.max(cardRect.left, galleryRect.left);

    if (visibleWidth <= 0) {
      return;
    }

    const cardCenter = cardRect.left + cardRect.width / 2;
    const distance = Math.abs(cardCenter - galleryCenter);

    if (distance < nearestDistance) {
      nearestDistance = distance;
      nearestIndex = cardIndex;
    }
  });

  return nearestIndex;
}

function updateActiveServiceFromScroll() {
  serviceScrollFrame = 0;

  const nearestIndex = getNearestServiceCardIndex();

  if (nearestIndex !== activeServiceIndex) {
    setActiveService(nearestIndex, { syncScroll: false });
  }
}

function requestServiceScrollUpdate() {
  if (serviceScrollFrame || serviceDragActive || !serviceGalleryCanScroll() || isProgrammaticServiceScroll) {
    return;
  }

  serviceScrollFrame = window.requestAnimationFrame(updateActiveServiceFromScroll);
}

function getServiceCardIndexFromTarget(target) {
  const card = target?.closest?.(".service-card");

  if (!card) {
    return activeServiceIndex;
  }

  const cardIndex = serviceCards.indexOf(card);
  return cardIndex === -1 ? activeServiceIndex : cardIndex;
}

function startServiceDrag(clientX, clientY, target) {
  if (!serviceGalleryCanScroll()) {
    return;
  }

  window.clearTimeout(serviceDragTimer);
  serviceDragStartX = clientX;
  serviceDragStartY = clientY;
  serviceDragStartIndex = getServiceCardIndexFromTarget(target);
  serviceDragActivated = false;
  serviceDragActive = true;
}

function moveServiceDrag(clientX, clientY) {
  if (!serviceDragActive || serviceDragActivated) {
    return;
  }

  const deltaX = clientX - serviceDragStartX;
  const deltaY = clientY - serviceDragStartY;

  if (Math.abs(deltaX) < 18 || Math.abs(deltaX) <= Math.abs(deltaY)) {
    return;
  }

  serviceDragActivated = true;
  setActiveService(serviceDragStartIndex + (deltaX < 0 ? 1 : -1), { syncScroll: false });
}

function endServiceDrag() {
  serviceDragTimer = window.setTimeout(() => {
    serviceDragActive = false;
  }, 260);
}

function setActiveService(index, options = {}) {
  if (!serviceCards.length) {
    return;
  }

  const { syncScroll = false } = options;
  activeServiceIndex = (index + serviceCards.length) % serviceCards.length;

  serviceCards.forEach((card, cardIndex) => {
    const isActive = cardIndex === activeServiceIndex;

    card.classList.toggle("service-card-active", isActive);
    card.setAttribute("aria-current", isActive ? "true" : "false");
  });

  if (syncScroll && serviceGalleryCanScroll()) {
    serviceCards[activeServiceIndex].scrollIntoView({
      behavior: "smooth",
      block: "nearest",
      inline: "center",
    });
  }
}

function renderWhySlide(index) {
  if (!whyTitle || !whyBullets || !whySlides.length) {
    return;
  }

  activeWhyIndex = (index + whySlides.length) % whySlides.length;
  const slide = whySlides[activeWhyIndex];

  if (whyProgress) {
    whyProgress.style.width = `${((activeWhyIndex + 1) / whySlides.length) * 100}%`;
  }

  whyStatement?.classList.add("is-changing");
  whyBullets.classList.add("is-changing");

  window.setTimeout(() => {
    const whyPrefix = whyStatement?.querySelector("p");
    if (whyPrefix && slide.prefix) whyPrefix.textContent = slide.prefix;
    whyTitle.textContent = slide.title;
    whyBullets.innerHTML = slide.bullets.map((item) => `<li>${item}</li>`).join("");

    whyStatement?.classList.remove("is-changing");
    whyBullets.classList.remove("is-changing");
  }, 180);
}

function createPartnerSlider(slider) {
  const viewport = slider.querySelector(".partner-viewport");
  const track = slider.querySelector(".partner-track");
  const cards = Array.from(track?.querySelectorAll(".partner-card:not([data-partner-clone])") || []);
  let timer;
  let dragStart = 0;
  let dragScrollStart = 0;
  let isDragging = false;

  function getStep() {
    const firstCard = track?.querySelector(".partner-card");

    if (!track || !firstCard) {
      return 0;
    }

    const trackStyle = getComputedStyle(track);
    const gap = Number.parseFloat(trackStyle.columnGap || trackStyle.gap) || 0;

    return firstCard.getBoundingClientRect().width + gap;
  }

  function cleanRuntimeState() {
    track?.querySelectorAll("[data-partner-clone]").forEach((clone) => clone.remove());
    track?.removeAttribute("data-loop-ready");
    viewport.scrollLeft = 0;
  }

  function nextLogo() {
    if (!viewport || !track || isDragging) {
      return;
    }

    const step = getStep();
    const maxScroll = viewport.scrollWidth - viewport.clientWidth;

    if (step <= 0 || maxScroll <= 1) {
      return;
    }

    viewport.scrollTo({
      left: viewport.scrollLeft + step >= maxScroll - 2 ? 0 : viewport.scrollLeft + step,
      behavior: "smooth",
    });
  }

  function startAutoplay() {
    window.clearInterval(timer);
    timer = window.setInterval(nextLogo, 2000);
  }

  function handleDragStart(event) {
    if (!viewport || event.pointerType === "touch") {
      return;
    }

    isDragging = true;
    dragStart = event.clientX;
    dragScrollStart = viewport.scrollLeft;
    viewport.classList.add("is-dragging");
    viewport.setPointerCapture?.(event.pointerId);
    window.clearInterval(timer);
  }

  function handleDragMove(event) {
    if (!isDragging || !viewport) {
      return;
    }

    event.preventDefault();
    viewport.scrollLeft = dragScrollStart - (event.clientX - dragStart);
  }

  function handleDragEnd(event) {
    if (!isDragging || !viewport) {
      return;
    }

    isDragging = false;
    viewport.classList.remove("is-dragging");
    viewport.releasePointerCapture?.(event.pointerId);
    startAutoplay();
  }

  function init() {
    if (!viewport || !track || !cards.length) {
      return;
    }

    cleanRuntimeState();
    cards.forEach((card) => {
      const clone = card.cloneNode(true);
      clone.setAttribute("data-partner-clone", "true");
      clone.setAttribute("aria-hidden", "true");
      track.appendChild(clone);
    });
    track.setAttribute("data-loop-ready", "true");
    viewport.addEventListener("pointerdown", handleDragStart);
    window.addEventListener("pointermove", handleDragMove);
    window.addEventListener("pointerup", handleDragEnd);
    window.addEventListener("pointercancel", handleDragEnd);
    startAutoplay();
  }

  return { init };
}

// Quản lý chuyển tab ngang giữa Service Introductions & Projects
window.setDetailPane = function(pane, updateHistory = true) {
  currentDetailPane = pane === "project" ? "project" : "intro";
  const servicePanel = document.getElementById("coverflowService");
  if (!servicePanel) return;

  const isProject = currentDetailPane === "project";
  servicePanel.classList.toggle("is-projects-active", isProject);

  if (isProject) {
    if (typeof window.expandProjectWing === "function") {
      window.expandProjectWing();
    }
  } else {
    if (typeof window.collapseProjectWing === "function") {
      window.collapseProjectWing();
    }
  }

  const introTabs = servicePanel.querySelectorAll('.coverflowService-nav-tab[href="#coverflowService"], .coverflowProject-nav-tab[href="#coverflowService"], [data-pane="intro"]');
  const projectTabs = servicePanel.querySelectorAll('.coverflowService-nav-tab[href="#coverflowProject"], .coverflowProject-nav-tab[href="#coverflowProject"], [data-pane="project"]');

  introTabs.forEach(t => t.classList.toggle("is-active", !isProject));
  projectTabs.forEach(t => t.classList.toggle("is-active", isProject));

  if (updateHistory) {
    const cleanPath = window.location.pathname.replace(/\/+$/, '') || '/';
    const hash = isProject ? "#coverflowProject" : "#coverflowService";
    window.history.replaceState(null, "", `${cleanPath}${hash}`);
  }
};

// Delegated click cho các tab điều hướng Coverflow Detail
document.addEventListener("click", function(event) {
  const tab = event.target.closest('.coverflowService-nav-tab, .coverflowProject-nav-tab');
  if (tab) {
    event.preventDefault();
    event.stopPropagation();
    const href = tab.getAttribute("href");
    const pane = tab.dataset.pane || (href === "#coverflowProject" ? "project" : "intro");

    const srvIdx = panels.findIndex(p => p.id === "coverflowService");
    if (currentIndex !== srvIdx) {
      window.openServiceDetail(null, pane);
      return;
    }

    window.setDetailPane(pane, true);
  }
});

let detailTransitionTimer = 0;

function resetDetailSurfaceStyles() {
  const coverflowPanel = document.getElementById("coverflow");
  const servicePanel = document.getElementById("coverflowService");
  const coverflowSurface = coverflowPanel?.querySelector(".coverflow-surface");
  const serviceSurface = servicePanel?.querySelector(".coverflowService-surface");

  if (coverflowPanel) {
    coverflowPanel.classList.remove("is-sliding-out-left");
    coverflowPanel.style.transform = "";
  }
  if (coverflowSurface) {
    coverflowSurface.style.transition = "";
    coverflowSurface.style.transform = "";
    coverflowSurface.style.opacity = "";
  }
  if (serviceSurface) {
    serviceSurface.style.transition = "";
    serviceSurface.style.transform = "";
    serviceSurface.style.opacity = "";
  }
}

window.openServiceDetail = function(slug, targetPane = "intro") {
  if (locked) return;

  const coverflowPanel = document.getElementById("coverflow");
  const servicePanel = document.getElementById("coverflowService");

  if (!isDetailFlowActive) {
    isDetailFlowActive = true;
    refreshPanels();
  }

  const srvIdx = panels.findIndex((p) => p.id === "coverflowService");
  if (srvIdx === -1 || !servicePanel) {
    return;
  }

  window.setDetailPane(targetPane, true);

  if (currentIndex === srvIdx) {
    return;
  }

  locked = true;
  window.clearTimeout(detailTransitionTimer);
  resetDetailSurfaceStyles();

  const wasAtCoverflow = Boolean(coverflowPanel && panels[currentIndex]?.id === "coverflow");
  currentIndex = srvIdx;

  // Nhảy thẳng tới vị trí của coverflowService (không cuộn dọc xuống)
  fullpage.style.transition = "none";
  applyFullpageTransform();
  void fullpage.offsetHeight;
  fullpage.style.transition = "";

  const cleanPath = window.location.pathname.replace(/\/+$/, '') || '/';
  const targetHash = currentDetailPane === "project" ? "#coverflowProject" : "#coverflowService";
  window.history.replaceState(null, "", `${cleanPath}${targetHash}`);

  window.scrollTo(0, 0);
  document.documentElement.scrollTop = 0;
  document.body.scrollTop = 0;
  if (fullpage) {
    fullpage.scrollTop = 0;
    fullpage.scrollLeft = 0;
  }

  updateControls();
  syncLeadContext();

  const coverflowSurface = coverflowPanel?.querySelector(".coverflow-surface");
  const serviceSurface = servicePanel.querySelector(".coverflowService-surface");
  const easing = "750ms cubic-bezier(0.22, 1, 0.36, 1)";

  if (wasAtCoverflow && coverflowPanel && coverflowSurface && serviceSurface) {
    const deltaY = servicePanel.offsetTop - coverflowPanel.offsetTop;

    coverflowPanel.classList.add("is-sliding-out-left");
    coverflowPanel.style.transform = `translate3d(0, ${deltaY}px, 0)`;

    coverflowSurface.style.transition = "none";
    coverflowSurface.style.transform = "translate3d(0, 0, 0)";
    coverflowSurface.style.opacity = "1";

    serviceSurface.style.transition = "none";
    serviceSurface.style.transform = "translate3d(35%, 0, 0)";
    serviceSurface.style.opacity = "0";

    void serviceSurface.offsetWidth;

    coverflowSurface.style.transition = `transform ${easing}, opacity ${easing}`;
    serviceSurface.style.transition = `transform ${easing}, opacity ${easing}`;

    coverflowSurface.style.transform = "translate3d(-35%, 0, 0)";
    coverflowSurface.style.opacity = "0";

    serviceSurface.style.transform = "translate3d(0, 0, 0)";
    serviceSurface.style.opacity = "1";

    detailTransitionTimer = window.setTimeout(() => {
      resetDetailSurfaceStyles();
      locked = false;
    }, 760);
  } else if (serviceSurface) {
    serviceSurface.style.transition = "none";
    serviceSurface.style.transform = "translate3d(35%, 0, 0)";
    serviceSurface.style.opacity = "0";

    void serviceSurface.offsetWidth;

    serviceSurface.style.transition = `transform ${easing}, opacity ${easing}`;
    serviceSurface.style.transform = "translate3d(0, 0, 0)";
    serviceSurface.style.opacity = "1";

    detailTransitionTimer = window.setTimeout(() => {
      resetDetailSurfaceStyles();
      locked = false;
    }, 760);
  } else {
    locked = false;
  }
};

window.closeServiceDetail = function() {
  if (!isDetailFlowActive || locked) return;

  const coverflowPanel = document.getElementById("coverflow");
  const servicePanel = document.getElementById("coverflowService");
  const coverflowSurface = coverflowPanel?.querySelector(".coverflow-surface");
  const serviceSurface = servicePanel?.querySelector(".coverflowService-surface");

  const isCurrentlyAtService = Boolean(servicePanel && panels[currentIndex]?.id === "coverflowService");

  if (isCurrentlyAtService && coverflowPanel && coverflowSurface && serviceSurface) {
    locked = true;
    window.clearTimeout(detailTransitionTimer);
    resetDetailSurfaceStyles();

    const deltaY = servicePanel.offsetTop - coverflowPanel.offsetTop;
    const easing = "750ms cubic-bezier(0.22, 1, 0.36, 1)";

    coverflowPanel.classList.add("is-sliding-out-left");
    coverflowPanel.style.transform = `translate3d(0, ${deltaY}px, 0)`;

    coverflowSurface.style.transition = "none";
    coverflowSurface.style.transform = "translate3d(-35%, 0, 0)";
    coverflowSurface.style.opacity = "0";

    serviceSurface.style.transition = "none";
    serviceSurface.style.transform = "translate3d(0, 0, 0)";
    serviceSurface.style.opacity = "1";

    void serviceSurface.offsetWidth;

    coverflowSurface.style.transition = `transform ${easing}, opacity ${easing}`;
    serviceSurface.style.transition = `transform ${easing}, opacity ${easing}`;

    coverflowSurface.style.transform = "translate3d(0, 0, 0)";
    coverflowSurface.style.opacity = "1";

    serviceSurface.style.transform = "translate3d(35%, 0, 0)";
    serviceSurface.style.opacity = "0";

    const cleanPath = window.location.pathname.replace(/\/+$/, '') || '/';
    window.history.replaceState(null, "", `${cleanPath}#coverflow`);

    detailTransitionTimer = window.setTimeout(() => {
      resetDetailSurfaceStyles();
      isDetailFlowActive = false;
      refreshPanels();
      currentIndex = Math.max(0, panels.findIndex((p) => p.id === "coverflow"));
      fullpage.style.transition = "none";
      applyFullpageTransform();
      void fullpage.offsetHeight;
      fullpage.style.transition = "";
      updateControls();
      window.setDetailPane("intro", false);
      locked = false;
    }, 760);
    return;
  }

  resetDetailSurfaceStyles();
  isDetailFlowActive = false;
  refreshPanels();
  currentIndex = Math.max(0, panels.findIndex((p) => p.id === "coverflow"));
  fullpage.style.transition = "none";
  applyFullpageTransform();
  void fullpage.offsetHeight;
  fullpage.style.transition = "";
  updateControls();
  window.setDetailPane("intro", false);
};

window.goToSectionById = function(id) {
  if (id === "coverflowService" || id === "coverflowProject") {
    window.openServiceDetail(null, id === "coverflowProject" ? "project" : "intro");
    return;
  }
  const targetIdx = panels.findIndex((p) => p.id === id);
  if (targetIdx !== -1) {
    goToSection(targetIdx);
  }
};

function getNextSectionIndex(direction) {
  return clampIndex(currentIndex + direction);
}

function goToSection(index) {
  const nextIndex = clampIndex(index);

  if (!Number.isFinite(nextIndex)) {
    return;
  }

  if (nextIndex === currentIndex || locked) {
    return;
  }

  const targetPanel = panels[nextIndex];
  currentIndex = nextIndex;
  locked = true;
  applyFullpageTransform();
  const panelId = panels[currentIndex]?.id;

  if (panelId) {
    const cleanPath = window.location.pathname.replace(/\/+$/, '') || '/';
    let targetHash = `#${panelId}`;
    if (panelId === "coverflowService" && currentDetailPane === "project") {
      targetHash = "#coverflowProject";
    }
    window.history.replaceState(null, "", `${cleanPath}${targetHash}`);
  }

  window.scrollTo(0, 0);
  document.documentElement.scrollTop = 0;
  document.body.scrollTop = 0;
  if (fullpage) {
    fullpage.scrollTop = 0;
    fullpage.scrollLeft = 0;
  }

  updateControls();
  syncLeadContext();

  // Khi về lại Coverflow hoặc ra khỏi subflow (sang Why/Connect), tự động reset subflow về trạng thái mặc định
  if (targetPanel && targetPanel.id === "coverflow" && isDetailFlowActive) {
    window.setTimeout(() => {
      if (panels[currentIndex]?.id === "coverflow" && isDetailFlowActive) {
        isDetailFlowActive = false;
        refreshPanels();
        window.setDetailPane("intro", false);
        currentIndex = panels.findIndex((p) => p.id === "coverflow");
        fullpage.style.transition = "none";
        applyFullpageTransform();
        void fullpage.offsetHeight;
        fullpage.style.transition = "";
      }
    }, 900);
  } else if (targetPanel && targetPanel.id !== "coverflowService" && isDetailFlowActive) {
    window.setTimeout(() => {
      if (panels[currentIndex]?.id !== "coverflowService" && isDetailFlowActive) {
        const curId = panels[currentIndex]?.id;
        isDetailFlowActive = false;
        refreshPanels();
        window.setDetailPane("intro", false);
        const newIdx = panels.findIndex((p) => p.id === curId);
        if (newIdx !== -1) {
          currentIndex = newIdx;
          fullpage.style.transition = "none";
          applyFullpageTransform();
          void fullpage.offsetHeight;
          fullpage.style.transition = "";
        }
      }
    }, 900);
  }

  window.setTimeout(() => {
    locked = false;
  }, 950);
}

function isComparisonModalOpen() {
  const m = document.getElementById("comparisonModal");
  return Boolean(m && m.classList.contains("is-open"));
}

function handleWheel(event) {
  event.preventDefault();

  if (getOpenProjectLightboxPanel() || isComparisonModalOpen()) {
    return;
  }

  if (menuOverlay.classList.contains("is-open")) {
    return;
  }

  if (locked || Math.abs(event.deltaY) < 12) {
    return;
  }

  const currentPanel = panels[currentIndex];

  // MERGED DETAIL FLOW: Khi active, thay vì cuộn dọc (scroll down) giữa Introductions và Projects thì trượt ngang (scroll left)!
  if (currentPanel && currentPanel.id === "coverflowService") {
    if (event.deltaY > 0) {
      if (currentDetailPane === "intro") {
        window.setDetailPane("project", true);
        locked = true;
        window.setTimeout(() => { locked = false; }, 650);
        return;
      } else {
        goToSection(currentIndex + 1);
        return;
      }
    } else {
      if (currentDetailPane === "project") {
        window.setDetailPane("intro", true);
        locked = true;
        window.setTimeout(() => { locked = false; }, 650);
        return;
      } else {
        window.closeServiceDetail();
        return;
      }
    }
  }

  goToSection(currentIndex + (event.deltaY > 0 ? 1 : -1));
}

function handleKeydown(event) {
  if (isComparisonModalOpen()) {
    if (event.key === "Escape") {
      event.preventDefault();
      if (typeof window.closeComparisonModal === "function") {
        window.closeComparisonModal();
      }
    }
    return;
  }

  const openProjectPanel = getOpenProjectLightboxPanel();

  if (openProjectPanel) {
    if (event.key === "Escape") {
      event.preventDefault();
      closeProjectLightbox(openProjectPanel);
      return;
    }

    if (event.key === "ArrowLeft") {
      event.preventDefault();
      renderProjectLightbox(openProjectPanel, (activeProjectGalleryIndexes.get(openProjectPanel.id) || 0) - 1);
      return;
    }

    if (event.key === "ArrowRight") {
      event.preventDefault();
      renderProjectLightbox(openProjectPanel, (activeProjectGalleryIndexes.get(openProjectPanel.id) || 0) + 1);
      return;
    }
  }

  if (event.key === "Escape" && menuOverlay.classList.contains("is-open")) {
    closeMenu();
    return;
  }

  if (menuOverlay.classList.contains("is-open")) {
    return;
  }

  const currentPanel = panels[currentIndex];
  if (currentPanel && currentPanel.id === "coverflowService") {
    const nextKeys = ["ArrowDown", "PageDown", "ArrowRight", " "];
    const prevKeys = ["ArrowUp", "PageUp", "ArrowLeft"];

    if (nextKeys.includes(event.key)) {
      event.preventDefault();
      if (currentDetailPane === "intro") {
        window.setDetailPane("project", true);
        locked = true;
        window.setTimeout(() => { locked = false; }, 650);
        return;
      } else {
        goToSection(currentIndex + 1);
        return;
      }
    }

    if (prevKeys.includes(event.key)) {
      event.preventDefault();
      if (currentDetailPane === "project") {
        window.setDetailPane("intro", true);
        locked = true;
        window.setTimeout(() => { locked = false; }, 650);
        return;
      } else {
        window.closeServiceDetail();
        return;
      }
    }
  }

  const nextKeys = ["ArrowDown", "PageDown", " "];
  const prevKeys = ["ArrowUp", "PageUp"];

  if (nextKeys.includes(event.key)) {
    event.preventDefault();
    goToSection(currentIndex + 1);
  }

  if (prevKeys.includes(event.key)) {
    event.preventDefault();
    goToSection(currentIndex - 1);
  }

  if (event.key === "Home") {
    event.preventDefault();
    goToSection(0);
  }

  if (event.key === "End") {
    event.preventDefault();
    goToSection(panels.length - 1);
  }
}

downButton.addEventListener("click", () => {
  if (panels[currentIndex]?.id === "coverflowService" && currentDetailPane === "intro") {
    window.setDetailPane("project", true);
    return;
  }
  goToSection(currentIndex === panels.length - 1 ? 0 : currentIndex + 1);
});

menuTrigger.addEventListener("click", toggleMenu);
menuClose.addEventListener("click", closeMenu);

menuOverlay.addEventListener("click", (event) => {
  if (event.target === menuOverlay) {
    closeMenu();
  }
});

sectionLinks.forEach((link) => {
  link.addEventListener("click", (event) => {
    const hash = link.getAttribute("href");
    if (hash === "#coverflowProject" || hash === "#coverflowService") {
      event.preventDefault();
      if (panels[currentIndex]?.id === "coverflowService") {
        window.setDetailPane(hash === "#coverflowProject" ? "project" : "intro", true);
        return;
      }
      window.goToSectionById(hash.replace("#", ""));
      return;
    }

    event.preventDefault();
    goToSection(getPanelIndexFromLink(link));
  });
});

menuLinks.forEach((link) => {
  link.addEventListener("click", (event) => {
    event.preventDefault();
    const nextIndex = getPanelIndexFromLink(link, "menuSection");

    closeMenu();
    window.setTimeout(() => {
      goToSection(nextIndex);
    }, 260);
  });
});

document.querySelectorAll(".global-workflow-nav button").forEach((button, buttonIndex) => {
  button.addEventListener("click", () => {
    const currentPanel = panels[currentIndex];
    const workflow = getProductWorkflow(currentPanel);
    const targetId = workflow && [workflow.what, workflow.project, workflow.expert][buttonIndex];

    if (targetId) {
      const targetIndex = panels.findIndex((panel) => panel.id === targetId);
      goToSection(targetIndex);
    }
  });
});

projectPanels.forEach((panel) => {
  const previous = panel.querySelector(".project-controls .project-prev");
  const next = panel.querySelector(".project-controls .project-next");
  const pill = panel.querySelector(".project-pill");
  const lightbox = panel.querySelector(".project-lightbox");
  const close = panel.querySelector(".project-lightbox-close");
  const galleryPrevious = panel.querySelector(".project-lightbox-prev");
  const galleryNext = panel.querySelector(".project-lightbox-next");
  const swiperEl = panel.querySelector(".project-swiper");

  if (swiperEl && typeof Swiper !== 'undefined') {
    new Swiper(swiperEl, {
      navigation: {
        nextEl: next,
        prevEl: previous,
      },
      speed: 600,
      spaceBetween: 30,
      grabCursor: true,
      on: {
        init: function () {
          renderProject(panel, this.activeIndex, 1, false);
        },
        slideChange: function () {
          const direction = this.activeIndex > this.previousIndex ? 1 : -1;
          renderProject(panel, this.activeIndex, direction, false);
        }
      }
    });
  } else {
    renderProject(panel, 0);

    previous?.addEventListener("click", () => {
      renderProject(panel, (activeProjectIndexes.get(panel.id) || 0) - 1, -1, true);
    });

    next?.addEventListener("click", () => {
      renderProject(panel, (activeProjectIndexes.get(panel.id) || 0) + 1, 1, true);
    });
  }

  pill?.addEventListener("click", () => {
    openProjectLightbox(panel);
  });

  close?.addEventListener("click", () => {
    closeProjectLightbox(panel);
  });

  lightbox?.addEventListener("click", (event) => {
    if (event.target === lightbox) {
      closeProjectLightbox(panel);
    }
  });

  galleryPrevious?.addEventListener("click", () => {
    renderProjectLightbox(panel, (activeProjectGalleryIndexes.get(panel.id) || 0) - 1);
  });

  galleryNext?.addEventListener("click", () => {
    renderProjectLightbox(panel, (activeProjectGalleryIndexes.get(panel.id) || 0) + 1);
  });
});

expertPanels.forEach((panel) => {
  const previous = panel.querySelector(".expert-prev");
  const next = panel.querySelector(".expert-next");

  renderExpert(panel, 0);

  previous?.addEventListener("click", () => {
    renderExpert(panel, (activeExpertIndexes.get(panel.id) || 0) - 1, -1, true);
  });

  next?.addEventListener("click", () => {
    renderExpert(panel, (activeExpertIndexes.get(panel.id) || 0) + 1, 1, true);
  });
});

serviceCards.forEach((card, cardIndex) => {
  card.addEventListener("pointerdown", () => {
    setActiveService(cardIndex, { syncScroll: false });
  });

  // card.addEventListener("mouseenter", () => {
  //   setActiveService(cardIndex, { syncScroll: false });
  // });

  card.addEventListener("focusin", () => {
    setActiveService(cardIndex, { syncScroll: true });
  });
});

serviceGallery?.addEventListener("scroll", requestServiceScrollUpdate, { passive: true });

serviceGallery?.addEventListener("pointerdown", (event) => {
  startServiceDrag(event.clientX, event.clientY, event.target);
});

serviceGallery?.addEventListener("pointermove", (event) => {
  moveServiceDrag(event.clientX, event.clientY);
});

serviceGallery?.addEventListener("pointerup", endServiceDrag);
serviceGallery?.addEventListener("pointercancel", endServiceDrag);
serviceGallery?.addEventListener("mouseleave", endServiceDrag);

serviceGallery?.addEventListener(
  "touchstart",
  (event) => {
    const touch = event.touches[0];
    startServiceDrag(touch.clientX, touch.clientY, event.target);
  },
  { passive: true },
);

serviceGallery?.addEventListener(
  "touchmove",
  (event) => {
    const touch = event.touches[0];
    moveServiceDrag(touch.clientX, touch.clientY);
  },
  { passive: true },
);

serviceGallery?.addEventListener("touchend", endServiceDrag, { passive: true });
serviceGallery?.addEventListener("touchcancel", endServiceDrag, { passive: true });

servicePrev?.addEventListener("click", () => {
  isProgrammaticServiceScroll = true;
  clearTimeout(programmaticServiceScrollTimer);
  programmaticServiceScrollTimer = setTimeout(() => { isProgrammaticServiceScroll = false; }, 800);
  setActiveService(activeServiceIndex - 1, { syncScroll: true });
});

serviceNext?.addEventListener("click", () => {
  isProgrammaticServiceScroll = true;
  clearTimeout(programmaticServiceScrollTimer);
  programmaticServiceScrollTimer = setTimeout(() => { isProgrammaticServiceScroll = false; }, 800);
  setActiveService(activeServiceIndex + 1, { syncScroll: true });
});

whyPrev?.addEventListener("click", () => {
  renderWhySlide(activeWhyIndex - 1);
});

whyNext?.addEventListener("click", () => {
  renderWhySlide(activeWhyIndex + 1);
});

connectForms.forEach((form) => {
  form.addEventListener("submit", async (event) => {
    event.preventDefault();
    syncLeadContext();

    const button = form.querySelector("button[type='submit']");
    const status = form.querySelector(".connect-status");
    const startedAt = form.querySelector("input[name='_form_start_time']");
    const originalLabel = button?.textContent || "CONNECT WITH US";

    if (status) {
      status.textContent = "";
      status.classList.remove("is-error", "is-success");
    }

    if (button) {
      button.disabled = true;
      button.textContent = "SENDING...";
    }

    try {
      const response = await fetch(form.action || "/connect", {
        method: form.method || "POST",
        headers: {
          Accept: "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
        body: new FormData(form),
      });
      const data = await response.json().catch(() => ({}));
      const message = data.message || (response.ok ? "Thank you. We will contact you soon." : "Unable to send your request right now.");

      if (!response.ok || data.success === false) {
        throw new Error(message);
      }

      form.reset();
      if (startedAt) {
        startedAt.value = Math.floor(Date.now() / 1000).toString();
      }

      if (status) {
        status.textContent = message;
        status.classList.add("is-success");
      }
    } catch (error) {
      if (status) {
        status.textContent = error.message || "Unable to send your request right now.";
        status.classList.add("is-error");
      }
    } finally {
      if (button) {
        button.disabled = false;
        button.textContent = originalLabel;
      }
    }
  });
});

window.addEventListener("wheel", handleWheel, { passive: false });
window.addEventListener("keydown", handleKeydown);

window.addEventListener(
  "touchstart",
  (event) => {
    touchStartX = event.touches[0].clientX;
    touchStartY = event.touches[0].clientY;
    touchStartedInGallery = Boolean(event.target.closest(".service-gallery, .partner-viewport, .project-lightbox"));
  },
  { passive: true },
);

window.addEventListener(
  "touchend",
  (event) => {
    const touchEndX = event.changedTouches[0].clientX;
    const touchEndY = event.changedTouches[0].clientY;
    const horizontalDistance = touchStartX - touchEndX;
    const distance = touchStartY - touchEndY;
    const openProjectPanel = getOpenProjectLightboxPanel();

    if (openProjectPanel) {
      if (Math.abs(horizontalDistance) > 42 && Math.abs(horizontalDistance) > Math.abs(distance)) {
        renderProjectLightbox(openProjectPanel, (activeProjectGalleryIndexes.get(openProjectPanel.id) || 0) + (horizontalDistance > 0 ? 1 : -1));
      }

      touchStartedInGallery = false;
      return;
    }

    if (touchStartedInGallery && Math.abs(horizontalDistance) > Math.abs(distance)) {
      touchStartedInGallery = false;
      return;
    }

    touchStartedInGallery = false;

    if (isComparisonModalOpen()) return;

    const currentPanel = panels[currentIndex];
    if (currentPanel && currentPanel.id === "coverflowService") {
      if (!locked && !menuOverlay.classList.contains("is-open")) {
        const isNext = distance > 42 || horizontalDistance > 42;
        const isPrev = distance < -42 || horizontalDistance < -42;

        if (isNext) {
          if (currentDetailPane === "intro") {
            window.setDetailPane("project", true);
            locked = true;
            window.setTimeout(() => { locked = false; }, 650);
            return;
          } else if (distance > 42) {
            goToSection(currentIndex + 1);
            return;
          }
        }

        if (isPrev) {
          if (currentDetailPane === "project") {
            window.setDetailPane("intro", true);
            locked = true;
            window.setTimeout(() => { locked = false; }, 650);
            return;
          } else {
            window.closeServiceDetail();
            return;
          }
        }
      }
      return;
    }

    if (!locked && !menuOverlay.classList.contains("is-open") && Math.abs(distance) > 42) {
      goToSection(currentIndex + (distance > 0 ? 1 : -1));
    }
  },
  { passive: true },
);

window.addEventListener("resize", () => {
  refreshPanels();
  currentIndex = clampIndex(currentIndex);
  applyFullpageTransform();
  requestServiceScrollUpdate();
});

applyFullpageTransform();
updateControls();
if (currentDetailPane === "project") {
  window.setDetailPane("project", false);
}
setActiveService(activeServiceIndex);
renderWhySlide(activeWhyIndex);
partnerSliders.forEach((slider) => {
  createPartnerSlider(slider).init();
});


document.querySelectorAll(".global-section-nav a").forEach((link) => {
  link.addEventListener("click", (event) => {
    event.preventDefault();
    goToSection(getPanelIndexFromLink(link));
  });
});
