// Builds the recurring diagrams before reveal.js initialises.
(function () {
  const FLOW = [
    ['📱 / 🎮', 'Telefoon of remote', 'WebSocket of ESP-NOW'],
    ['ESP32-C3', 'Ontvangen', 'valideren · mixen'],
    ['PWM', 'Motorsignaal', '−255 … 255 → x% aan'],
    ['DRV8833', 'H-brug', 'stroom + richting'],
    ['N20 + chassis', 'Wielen draaien', '150 g, 3D-print'],
  ];

  document.querySelectorAll('.flow').forEach((el) => {
    const on = (el.dataset.hl || '').split(',').filter(Boolean).map(Number);
    if (on.length) el.classList.add('dim');
    el.innerHTML = FLOW.map(([title, sub, detail], i) =>
      `<div class="node${on.includes(i + 1) ? ' on' : ''}"><span class="n">${i + 1}</span><b>${title}</b>${sub}<small>${detail}</small></div>`
    ).join('<div class="arrow">→</div>');
  });

  document.querySelectorAll('.pwm-wave').forEach((el) => {
    const duty = Number(el.dataset.duty);
    const period = 100, high = 8, low = 48;
    let d = `M0,${low}`;
    for (let p = 0; p < 6; p++) {
      const x = p * period, on = x + period * duty / 100;
      if (duty > 0) d += ` L${x},${high} L${on},${high} L${on},${low}`;
      d += ` L${x + period},${low}`;
    }
    el.innerHTML = `<svg viewBox="0 0 600 56" preserveAspectRatio="none"><path d="${d}" fill="none" stroke="#f97316" stroke-width="4"/></svg>`;
  });

  // H-bridge: four switches around the motor; `closed` = which switches conduct.
  document.querySelectorAll('.hbridge').forEach((el) => {
    const closed = (el.dataset.closed || '').split(',');
    const c = (k) => (closed.includes(k) ? '#f97316' : '#475569');
    const sw = (k, x, y1, y2) => closed.includes(k)
      ? `<line x1="${x}" y1="${y1}" x2="${x}" y2="${y2}" stroke="${c(k)}" stroke-width="6"/>`
      : `<line x1="${x}" y1="${y1}" x2="${x + 22}" y2="${y2 - 12}" stroke="${c(k)}" stroke-width="6"/>`;
    const label = (k, x, y) => `<text x="${x}" y="${y}" fill="${c(k)}" font-size="20" font-weight="800">${k}</text>`;
    const dir = el.dataset.dir || '';
    el.innerHTML = `<svg viewBox="0 0 330 250">
      <line x1="60" y1="20" x2="270" y2="20" stroke="#ef4444" stroke-width="4"/><text x="282" y="27" fill="#ef4444" font-size="22" font-weight="800">+</text>
      <line x1="60" y1="230" x2="270" y2="230" stroke="#94a3b8" stroke-width="4"/><text x="276" y="237" fill="#94a3b8" font-size="18">GND</text>
      <line x1="80" y1="20" x2="80" y2="60" stroke="#475569" stroke-width="4"/><line x1="250" y1="20" x2="250" y2="60" stroke="#475569" stroke-width="4"/>
      ${sw('A', 80, 60, 110)}${sw('B', 250, 60, 110)}
      <line x1="80" y1="110" x2="80" y2="140" stroke="#475569" stroke-width="4"/><line x1="250" y1="110" x2="250" y2="140" stroke="#475569" stroke-width="4"/>
      ${sw('C', 80, 140, 190)}${sw('D', 250, 140, 190)}
      <line x1="80" y1="190" x2="80" y2="230" stroke="#475569" stroke-width="4"/><line x1="250" y1="190" x2="250" y2="230" stroke="#475569" stroke-width="4"/>
      <line x1="80" y1="125" x2="135" y2="125" stroke="#475569" stroke-width="4"/><line x1="195" y1="125" x2="250" y2="125" stroke="#475569" stroke-width="4"/>
      <circle cx="165" cy="125" r="30" fill="#1e293b" stroke="#e2e8f0" stroke-width="4"/><text x="152" y="133" fill="#e2e8f0" font-size="24" font-weight="800">M</text>
      ${label('A', 50, 92)}${label('B', 268, 92)}${label('C', 50, 172)}${label('D', 268, 172)}
      <text x="165" y="80" fill="#f97316" font-size="30" text-anchor="middle" font-weight="900">${dir}</text>
    </svg>`;
  });
})();

// Animated PCB slide: the step = number of visible .pf-step fragments.
window.roboPcbFlow = function (Reveal) {
  // Cumulative step classes (s1…s5) so earlier highlights stay visible.
  const render = (slide) => {
    const step = slide.querySelectorAll('.pf-step.visible').length;
    slide.dataset.step = step;
    for (let i = 1; i <= 5; i++) slide.classList.toggle(`s${i}`, step >= i);
  };
  const update = () => {
    const slide = Reveal.getCurrentSlide();
    if (slide && slide.classList.contains('pcbflow-slide')) render(slide);
  };
  ['ready', 'slidechanged', 'fragmentshown', 'fragmenthidden'].forEach((e) => Reveal.on(e, update));
  document.querySelectorAll('.pcbflow-slide').forEach(render);
};

// Reusable PCB v4 animation. Host: <div class="pcbm" data-states='[...]'>.
// State index = number of fragments shown on the slide (incl. code-highlight steps).
// State: { k: "esp drv ...", L: -255..255, R: -255..255, t: { slot: "text" }, c: "host classes" }
window.roboPcbMini = function (Reveal) {
  const tpl = document.getElementById('pcb-tpl');
  if (!tpl) return;
  const hosts = [...document.querySelectorAll('.pcbm')];
  hosts.forEach((h) => h.prepend(tpl.content.cloneNode(true)));

  const spin = (el, v) => {
    el.style.animation = v ? `pfSpin ${(110 / Math.abs(v)).toFixed(2)}s linear infinite${v < 0 ? ' reverse' : ''}` : 'none';
    el.classList.toggle('on', !!v);
  };
  const flow = (g, v) => g.querySelectorAll('path').forEach((p) => {
    p.style.animationDuration = v ? `${(120 / Math.abs(v)).toFixed(2)}s` : '';
    p.style.animationDirection = v < 0 ? 'reverse' : 'normal';
  });

  const apply = (host, st) => {
    st = st || {};
    const L = st.L || 0, R = st.R || 0;
    const keys = new Set((st.k || '').split(/\s+/).filter(Boolean));
    if (L > 0) keys.add('g6'); if (L < 0) keys.add('g7');
    if (R > 0) keys.add('g10'); if (R < 0) keys.add('g9');
    if (L) keys.add('m2'); if (R) keys.add('m1');
    if (L || R) keys.add('pwr');
    host.className = 'pcbm ' + (host.dataset.base || '') + ' ' + (st.c || '');
    host.querySelectorAll('[data-k]').forEach((el) => el.classList.toggle('on', keys.has(el.dataset.k)));
    flow(host.querySelector('[data-k="m2"]'), L);
    flow(host.querySelector('[data-k="m1"]'), R);
    spin(host.querySelector('.wh-l'), L);
    spin(host.querySelector('.wh-r'), R);
    const t = st.t || {};
    host.querySelectorAll('[data-tag]').forEach((g) => {
      let txt = t[g.dataset.tag] || '';
      const red = txt.startsWith('!');
      if (red) txt = txt.slice(1);
      g.classList.toggle('on', !!txt);
      g.classList.toggle('red', red);
      g.querySelector('text').textContent = txt;
      const w = [...txt].length * 19.5 + 48;
      const r = g.querySelector('rect');
      r.setAttribute('x', -w / 2);
      r.setAttribute('width', w);
    });
  };

  const states = (host) => { try { return JSON.parse(host.dataset.states || '[]'); } catch (e) { console.error(e); return []; } };
  const update = () => {
    const slide = Reveal.getCurrentSlide();
    if (!slide) return;
    const host = slide.querySelector('.pcbm');
    if (!host) return;
    const list = states(host);
    const f = Reveal.getIndices().f;
    const i = Math.min(f === undefined || f < 0 ? 0 : f + 1, list.length - 1);
    apply(host, list[i]);
  };
  hosts.forEach((h) => apply(h, states(h)[0]));
  ['ready', 'slidechanged', 'fragmentshown', 'fragmenthidden'].forEach((e) => Reveal.on(e, update));
};
