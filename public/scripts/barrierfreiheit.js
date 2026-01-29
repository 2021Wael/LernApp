const slider = document.getElementById("fontSlider");
const resetBtn = document.getElementById("resetFont");
const menuBtn = document.getElementById("menuBtn");
const menu = document.getElementById("menu");
const contrastBtn = document.getElementById("contrastBtn");
const toggleBtn = document.getElementById("toggleCircleBtn");
const cursorCircle = document.getElementById("cursorCircle");

/* =====================
   LOAD SAVED SETTINGS
===================== */
const savedScale = localStorage.getItem("a11y_font_scale");
if (savedScale) {
    document.documentElement.style.fontSize = savedScale + "em";
    slider.value = savedScale * 100;
}

const savedContrast = localStorage.getItem("a11y_contrast");
if (savedContrast === "true") {
    document.body.classList.add("high-contrast");
}

let circleEnabled = localStorage.getItem("a11y_cursor_circle") === "true";
cursorCircle.style.display = circleEnabled ? "block" : "none";
if (circleEnabled) toggleBtn.classList.add("active");

/* ==========
   FONT SIZE
============= */
slider.addEventListener("input", () => {
    const scale = slider.value / 100;
    document.documentElement.style.fontSize = scale + "em";
    localStorage.setItem("a11y_font_scale", scale);
});

resetBtn.addEventListener("click", () => {
    slider.value = 100;
    document.documentElement.style.fontSize = "1em";
    localStorage.removeItem("a11y_font_scale");
});

/* ============
   MENU TOGGLE
=============== */
menuBtn.addEventListener("click", () => {
    const rect = menuBtn.getBoundingClientRect();
    menu.style.top = rect.bottom + "px";
    menu.style.left = rect.left + "px";
    menu.classList.toggle("hidden");
});

/* ==============
   HIGH CONTRAST
================= */
contrastBtn.addEventListener("click", () => {
    document.body.classList.toggle("high-contrast");
    localStorage.setItem(
        "a11y_contrast",
        document.body.classList.contains("high-contrast")
    );
});

/* =================
   CURSOR HIGHLIGHT
==================== */
toggleBtn.addEventListener("click", () => {
    circleEnabled = !circleEnabled;
    cursorCircle.style.display = circleEnabled ? "block" : "none";
    toggleBtn.classList.toggle("active");
    localStorage.setItem("a11y_cursor_circle", circleEnabled);
});

document.addEventListener("mousemove", (e) => {
    if (!circleEnabled) return;
    cursorCircle.style.left = e.clientX + "px";
    cursorCircle.style.top = e.clientY + "px";
});
