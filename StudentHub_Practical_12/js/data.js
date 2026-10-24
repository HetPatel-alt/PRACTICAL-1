async function fetchJson(path) {
  const response = await fetch(path, { headers: { Accept: "application/json" } });
  if (!response.ok) throw new Error(`Request failed (${response.status}) for ${path}`);
  const data = await response.json();
  if (!Array.isArray(data)) throw new Error(`Invalid JSON structure in ${path}: expected an array.`);
  return data;
}

function setStatus(element, message, kind = "") {
  if (!element) return;
  element.textContent = message;
  element.className = `data-status ${kind}`.trim();
}
function safeText(value) {
  return String(value ?? "").replace(/[&<>"']/g, (char) => ({
    "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;"
  }[char]));
}
function fillOptions(select, values, firstLabel) {
  if (!select) return;
  const existing = select.value;
  select.replaceChildren(new Option(firstLabel, ""));
  [...new Set(values)].sort((a, b) => String(a).localeCompare(String(b))).forEach(value => {
    select.add(new Option(value, value));
  });
  if ([...select.options].some(option => option.value === existing)) select.value = existing;
}
function renderCards(container, records, cardMarkup, emptyMessage) {
  if (!container) return;
  if (!records.length) {
    container.innerHTML = `<p class="empty-state">${safeText(emptyMessage)}</p>`;
    return;
  }
  container.innerHTML = records.map(cardMarkup).join("");
}
function setupPagination(prefix, getPageCount, onPageChange) {
  const prev = document.getElementById(`${prefix}-prev`);
  const next = document.getElementById(`${prefix}-next`);
  const label = document.getElementById(`${prefix}-page-label`);
  const state = { page: 1 };
  function update(totalItems, pageSize) {
    const pages = Math.max(1, Math.ceil(totalItems / pageSize));
    state.page = Math.min(state.page, pages);
    if (label) label.textContent = `Page ${state.page} of ${pages}`;
    if (prev) prev.disabled = state.page <= 1;
    if (next) next.disabled = state.page >= pages;
  }
  if (prev) prev.addEventListener("click", () => { state.page--; onPageChange(state.page); });
  if (next) next.addEventListener("click", () => { state.page++; onPageChange(state.page); });
  return { state, update, reset() { state.page = 1; } };
}

async function initEvents() {
  const list = document.getElementById("events-list");
  if (!list) return;
  const status = document.getElementById("events-status");
  const search = document.getElementById("event-search");
  const category = document.getElementById("event-category");
  const sort = document.getElementById("event-sort");
  const pageSize = document.getElementById("event-page-size");
  let records = [];
  const pager = setupPagination("events", () => 1, render);
  function render() {
    const query = (search.value || "").trim().toLowerCase();
    let filtered = records.filter(item =>
      [item.title, item.location, item.organizer, item.description, item.category]
        .some(value => String(value).toLowerCase().includes(query)) &&
      (!category.value || item.category === category.value)
    );
    filtered.sort((a, b) => {
      switch (sort.value) {
        case "date-desc": return b.date.localeCompare(a.date);
        case "title-asc": return a.title.localeCompare(b.title);
        case "title-desc": return b.title.localeCompare(a.title);
        default: return a.date.localeCompare(b.date);
      }
    });
    const size = Number(pageSize.value) || 5;
    const pageCount = Math.max(1, Math.ceil(filtered.length / size));
    pager.state.page = Math.min(pager.state.page, pageCount);
    const shown = filtered.slice((pager.state.page - 1) * size, pager.state.page * size);
    renderCards(list, shown, item => `<article class="data-card">
      <p class="data-meta">${safeText(item.category)} · ${safeText(new Date(`${item.date}T12:00:00`).toLocaleDateString(undefined, {year:"numeric",month:"short",day:"numeric"}))}</p>
      <h3>${safeText(item.title)}</h3><p>${safeText(item.description)}</p>
      <p><strong>Location:</strong> ${safeText(item.location)}</p><p><strong>Organizer:</strong> ${safeText(item.organizer)}</p>
    </article>`, "No events match your search and filters.");
    setStatus(status, `Showing ${shown.length ? (pager.state.page - 1) * size + 1 : 0}–${Math.min(pager.state.page * size, filtered.length)} of ${filtered.length} matching events.`);
    pager.update(filtered.length, size);
  }
  function resetAndRender() { pager.reset(); render(); }
  try {
    setStatus(status, "Loading events…");
    records = await fetchJson("data/events.json");
    fillOptions(category, records.map(item => item.category), "All categories");
    [search, category, sort, pageSize].forEach(control => control.addEventListener(control === search ? "input" : "change", resetAndRender));
    render();
  } catch (error) {
    setStatus(status, `Could not load events. ${error.message} Check data/events.json and run the site using a local HTTP server.`, "error");
    list.innerHTML = "";
    console.error("Events JSON loading error:", error);
  }
}

async function initStudents() {
  const list = document.getElementById("students-list");
  if (!list) return;
  const status = document.getElementById("students-status");
  const search = document.getElementById("student-search");
  const course = document.getElementById("student-course");
  const year = document.getElementById("student-year");
  const sort = document.getElementById("student-sort");
  const pageSize = document.getElementById("student-page-size");
  let records = [];
  const pager = setupPagination("students", () => 1, render);
  function render() {
    const query = (search.value || "").trim().toLowerCase();
    let filtered = records.filter(student =>
      [student.id, student.name, student.course, student.city, student.email]
        .some(value => String(value).toLowerCase().includes(query)) &&
      (!course.value || student.course === course.value) &&
      (!year.value || String(student.year) === year.value)
    );
    filtered.sort((a, b) => {
      if (sort.value === "name-desc") return b.name.localeCompare(a.name);
      if (sort.value === "year-asc") return a.year - b.year || a.name.localeCompare(b.name);
      if (sort.value === "year-desc") return b.year - a.year || a.name.localeCompare(b.name);
      return a.name.localeCompare(b.name);
    });
    const size = Number(pageSize.value) || 5;
    const pages = Math.max(1, Math.ceil(filtered.length / size));
    pager.state.page = Math.min(pager.state.page, pages);
    const shown = filtered.slice((pager.state.page - 1) * size, pager.state.page * size);
    renderCards(list, shown, student => `<article class="data-card">
      <p class="data-meta">${safeText(student.id)} · ${safeText(student.status)}</p>
      <h3>${safeText(student.name)}</h3><p>${safeText(student.course)}</p>
      <p><strong>Year:</strong> ${safeText(student.year)} · <strong>City:</strong> ${safeText(student.city)}</p>
      <p><a href="mailto:${encodeURIComponent(student.email)}">${safeText(student.email)}</a></p>
    </article>`, "No student profiles match your search and filters.");
    setStatus(status, `Showing ${shown.length ? (pager.state.page - 1) * size + 1 : 0}–${Math.min(pager.state.page * size, filtered.length)} of ${filtered.length} matching students.`);
    pager.update(filtered.length, size);
  }
  function resetAndRender() { pager.reset(); render(); }
  try {
    setStatus(status, "Loading student profiles…");
    records = await fetchJson("data/students.json");
    fillOptions(course, records.map(student => student.course), "All courses");
    [search, course, year, sort, pageSize].forEach(control => control.addEventListener(control === search ? "input" : "change", resetAndRender));
    render();
  } catch (error) {
    setStatus(status, `Could not load student profiles. ${error.message} Check data/students.json and run the site using a local HTTP server.`, "error");
    list.innerHTML = "";
    console.error("Students JSON loading error:", error);
  }
}

async function initFaqs() {
  const list = document.getElementById("faqs-list");
  if (!list) return;
  const status = document.getElementById("faqs-status");
  const search = document.getElementById("faq-search");
  const category = document.getElementById("faq-category");
  let records = [];
  function render() {
    const query = (search.value || "").trim().toLowerCase();
    const filtered = records.filter(item =>
      [item.question, item.answer, item.category].some(value => String(value).toLowerCase().includes(query)) &&
      (!category.value || item.category === category.value)
    );
    renderCards(list, filtered, item => `<article class="faq-item">
      <h3><button class="faq-question" type="button" aria-expanded="false" aria-controls="faq-answer-${item.id}">${safeText(item.question)}</button></h3>
      <div class="faq-answer" id="faq-answer-${item.id}" hidden><p>${safeText(item.answer)}</p></div>
    </article>`, "No FAQs match your search.");
    list.querySelectorAll(".faq-question").forEach(button => button.addEventListener("click", () => {
      const expanded = button.getAttribute("aria-expanded") === "true";
      button.setAttribute("aria-expanded", String(!expanded));
      const answer = document.getElementById(button.getAttribute("aria-controls"));
      if (answer) answer.hidden = expanded;
    }));
    setStatus(status, `Showing ${filtered.length} of ${records.length} FAQs.`);
  }
  try {
    setStatus(status, "Loading FAQs…");
    records = await fetchJson("data/faqs.json");
    fillOptions(category, records.map(item => item.category), "All categories");
    search.addEventListener("input", render);
    category.addEventListener("change", render);
    render();
  } catch (error) {
    setStatus(status, `Could not load FAQs. ${error.message} Check data/faqs.json and run the site using a local HTTP server.`, "error");
    list.innerHTML = "";
    console.error("FAQ JSON loading error:", error);
  }
}

async function initNotices() {
  const list = document.getElementById("notices-list");
  if (!list) return;
  const status = document.getElementById("notices-status");
  try {
    setStatus(status, "Loading notices…");
    const records = await fetchJson("data/notices.json");
    records.sort((a, b) => b.date.localeCompare(a.date) || b.id - a.id);
    renderCards(list, records.slice(0, 6), item => `<article class="data-card">
      <p class="data-meta">${safeText(item.type)} · ${safeText(item.date)}</p>
      <h3>${safeText(item.title)}</h3><p>${safeText(item.message)}</p>
    </article>`, "No notices are available.");
    setStatus(status, `Loaded ${records.length} notices from JSON. Showing the latest ${Math.min(records.length, 6)}.`);
  } catch (error) {
    setStatus(status, `Could not load notices. ${error.message} Check data/notices.json and run the site using a local HTTP server.`, "error");
    list.innerHTML = "";
    console.error("Notices JSON loading error:", error);
  }
}

document.addEventListener("DOMContentLoaded", () => {
  initEvents();
  initStudents();
  initFaqs();
  initNotices();
});
