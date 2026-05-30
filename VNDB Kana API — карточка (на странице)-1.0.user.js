// ==UserScript==
// @name         VNDB Kana API — карточка (на странице)
// @namespace    https://github.com/yourname
// @version      1.0
// @description  Ссылки: белый список доменов + по одной ссылке на домен (последний релиз). Steam: системные требования и скриншоты.
// @author       Grok
// @match        https://vndb.org/v*
// @match        https://beta.vndb.org/v*
// @grant        GM_xmlhttpRequest
// @grant        GM_addStyle
// @connect      api.vndb.org
// @connect      beta.vndb.org
// @connect      translate.googleapis.com
// @connect      store.steampowered.com
// @connect      steamspy.com

// ==/UserScript==

(function () {
    'use strict';

    const API_BASE = location.hostname.includes('beta')
        ? 'https://beta.vndb.org/api/kana'
        : 'https://api.vndb.org/kana';

    const match = location.pathname.match(/^\/v(\d+)/);
    if (!match) return;
    const vnId = match[1];

    // ─── Утилиты ────────────────────────────────────────────────────────────────

    /** Заменяет экранированные \n на настоящие переносы строк */
    const fmt = s => s.replace(/\\n/g, '\n');

    /** Возвращает hostname из URL или null при ошибке парсинга */
    function getHostname(url) {
        try { return new URL(url).hostname; } catch { return null; }
    }

    /** Создаёт HTML-ссылку с заданным CSS-классом */
    const makeLink = (url, text = url, cls = 'vlink') =>
        `<a href="${url}" target="_blank" rel="noopener" class="${cls}">${text}</a>`;

    /** Строка карточки: [Метка] значение */
    const row = (label, val) =>
        `<div><strong class="vl">[${label}]</strong> ${val || '—'}</div>`;

    /** Секция с заголовком и контентом ниже; big=true → бо́льший отступ сверху */
    const sect = (label, content, big = false) =>
        `<div class="${big ? 'vsect2' : 'vsect'}"><strong class="vl">[${label}]</strong><br>${content}</div>`;

    /**
     * Парсит HTML минимальных системных требований Steam:
     * <br> → перенос строки, убираем все прочие теги,
     * «Minimum:» и звёздочку в «OS *:»
     */
    function parseSteamMinReqs(html) {
        if (!html) return null;
        return html
            .replace(/<br\s*\/?>/gi, '\n')
            .replace(/<strong>Minimum:<\/strong>/gi, '')
            .replace(/<[^>]+>/g, '')
            .replace(/OS \*:/g, 'OS:')
            .split('\n').map(l => l.trim()).filter(Boolean).join('\n');
    }

    // ─── API ────────────────────────────────────────────────────────────────────

    function apiPost(endpoint, payload) {
        return new Promise((resolve, reject) => {
            GM_xmlhttpRequest({
                method: 'POST',
                url: `${API_BASE}/${endpoint}`,
                headers: { 'Content-Type': 'application/json' },
                data: JSON.stringify(payload),
                onload: r => r.status === 200
                    ? resolve(JSON.parse(r.responseText))
                    : reject(new Error(`HTTP ${r.status}`)),
                onerror: () => reject(new Error('Ошибка сети'))
            });
        });
    }

    function fetchVNData(id) {
        return apiPost('vn', {
            filters: ['id', '=', `v${id}`],
            // olang — язык оригинальной озвучки
            fields: 'title, released, description, olang, image { url }, titles { lang, main, title }, tags { name, lie }, languages, developers { name }, platforms, screenshots { url }',
            results: 1
        }).then(r => r.results?.[0]);
    }

    function fetchReleases(id) {
        return apiPost('release', {
            filters: ['vn', '=', ['id', '=', `v${id}`]],
            // released — нужен для выбора ссылки с наибольшей датой среди одного домена
            fields: 'uncensored, official, patch, voiced, platforms, vns { id, rtype }, producers { name }, extlinks { url }, released',
            results: 100
        }).then(r => r.results || []).catch(() => []);
    }

    /** Запрашивает данные игры из Steam Store API (ключ не нужен) */
    function fetchSteamDetails(appId) {
        return new Promise(resolve => {
            GM_xmlhttpRequest({
                method: 'GET',
                url: `https://store.steampowered.com/api/appdetails?appids=${appId}&l=english`,
                timeout: 10000,
                onload: r => {
                    try {
                        const json = JSON.parse(r.responseText);
                        const entry = json?.[String(appId)];
                        resolve(entry?.success ? entry.data : null);
                    } catch { resolve(null); }
                },
                onerror: () => resolve(null)
            });
        });
    }

    /** Запрашивает пользовательские теги из SteamSpy API (ключ не нужен) */
    function fetchSteamSpy(appId) {
        return new Promise(resolve => {
            GM_xmlhttpRequest({
                method: 'GET',
                url: `https://steamspy.com/api.php?request=appdetails&appid=${appId}`,
                timeout: 10000,
                onload: r => {
                    try { resolve(JSON.parse(r.responseText)?.tags ?? null); }
                    catch { resolve(null); }
                },
                onerror: () => resolve(null)
            });
        });
    }

    function translateToRussian(text) {
        if (!text || /[а-яА-ЯёЁ]/.test(text)) return Promise.resolve(text);
        const url = `https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=ru&dt=t&q=${encodeURIComponent(text)}`;
        return new Promise(resolve => {
            GM_xmlhttpRequest({
                method: 'GET', url, timeout: 10000,
                onload: r => {
                    try { resolve(JSON.parse(r.responseText)[0].map(i => i[0]).join('')); }
                    catch { resolve(text); }
                },
                onerror: () => resolve(text)
            });
        });
    }

    // ─── Иконки магазинов ───────────────────────────────────────────────────────

    // Сначала — более специфичные субдомены, чтобы ci-en.dlsite.com не поглощался dlsite.com
    const STORE_ICONS = [
        ['ci-en.dlsite.com',       'ci-en.png'],
        ['dl.getchu.com',          'getchu.png'],
        ['dmm.co.jp',              'fanza.png'],
        ['store.steampowered.com', 'steam.png'],
        ['gog.com',                'gogcom.png'],
        ['itch.io',                'itch_io.png'],
        ['nutaku.net',             'nutaku.png'],
        ['jastusa.com',            'jast-usa.png'],
        ['jaststore.com',          'jast-usa.png'],
        ['saikeystudios.com',      'saikey.png'],
        ['dlsite.com',             'dlsite.png'],
        ['getchu.com',             'getchu.png'],
        ['dmm.com',                'dmm.png'],
        ['fantia.jp',              'fantia.png'],
        ['boosty.to',              'boosty.png'],
        ['patreon.com',            'patreon.png'],
        ['subscribestar.com',      'subscribestar.png'],
        ['x.com',                  'x.png'],
    ];
    const ICON_BASE = 'https://static.pornolab.net/icons_new/r/';

    // Белый список доменов для [Ссылки] (специфичные субдомены — первыми)
    const ALLOWED_DOMAINS_ORDERED = [
        'ci-en.dlsite.com',
        'dl.getchu.com',
        'store.steampowered.com',
        'gog.com',
        'itch.io',
        'nutaku.net',
        'jastusa.com',
        'jaststore.com',
        'saikeystudios.com',
        'dlsite.com',
        'getchu.com',
        'dmm.com',
        'fantia.jp',
        'dmm.co.jp',
        'boosty.to',
        'patreon.com',
        'subscribestar.com',
        'x.com',
    ];

    /**
     * Возвращает канонический домен из белого списка, которому принадлежит URL,
     * или null — если URL не входит ни в один разрешённый домен.
     */
    function getCanonicalDomain(url) {
        const hostname = getHostname(url);
        if (!hostname) return null;
        return ALLOWED_DOMAINS_ORDERED.find(d => hostname === d || hostname.endsWith('.' + d)) ?? null;
    }

    /** Оборачивает URL в кликабельную иконку магазина (или plain-ссылку) */
    function urlToIcon(url) {
        const hostname = getHostname(url);
        if (!hostname) return makeLink(url);
        for (const [domain, icon] of STORE_ICONS) {
            if (hostname === domain || hostname.endsWith('.' + domain)) {
                return `<a href="${url}" target="_blank" rel="noopener" title="${url}"><img src="${ICON_BASE}${icon}" style="height:28px;vertical-align:middle;border-radius:4px;"></a>`;
            }
        }
        return makeLink(url);
    }

    // ─── Карточка ───────────────────────────────────────────────────────────────

    async function showModal(data, releases) {
        document.getElementById('vndb-modal')?.remove();

        const vid    = `v${vnId}`;
        const titles = data.titles || [];

        // Названия
        const jaMain  = titles.find(t => t.lang === 'ja' && t.main);
        const enTitle = titles.find(t => t.lang === 'en');
        const ruTitle = titles.find(t => t.lang === 'ru');
        const titlesHTML = [
            jaMain  && row('Оригинальное название (иероглифы)', jaMain.title),
            row('Оригинальное название', enTitle?.title ?? data.title),
            ruTitle && row('Название на русском', ruTitle.title),
        ].filter(Boolean).join('');

        // Данные из релизов
        const hasUncensored = releases.some(r => r.uncensored === true);
        // fix: find()+?.rtype возвращало true для релизов, вообще не содержащих данный VN
        const hasNonTrial   = releases.some(r =>
            (r.vns || []).some(v => v.id === vid && v.rtype !== 'trial')
        );
        const platformsArr = [...new Set(releases.filter(r => r.official).flatMap(r => r.platforms || []))];
        const platforms    = platformsArr.join(', ') || (data.platforms || []).join(', ');
        const creators  = [...new Set([
            ...(data.developers || []).map(d => d.name),
            ...releases.filter(r => r.official).flatMap(r => (r.producers || []).map(p => p.name)),
        ])];
        const langs = (data.languages || []).join(', ');

        // [Озвучка]
        const hasVoiced = releases.some(r =>
            r.official && (r.vns || []).some(v => v.id === vid) && r.voiced >= 3
        );
        const voicedVal = hasVoiced ? (data.olang || '—') : 'нет';

        // [Ссылки] — официальные не-патч, белый список, один URL на домен (самый свежий).
        // Строковое сравнение ISO-дат (YYYY-MM-DD / YYYY-MM / YYYY) корректно.
        // Значения null / 'tba' / '' считаются самыми ранними.
        const domainBestLink = new Map(); // canonicalDomain → { url, date }
        releases
            .filter(r => r.official && !r.patch)
            .forEach(r => {
                const date = (r.released && r.released !== 'TBA') ? r.released : '';
                (r.extlinks || []).forEach(({ url }) => {
                    if (!url) return;
                    const domain = getCanonicalDomain(url);
                    if (!domain) return;
                    const cur = domainBestLink.get(domain);
                    if (!cur || date > cur.date) domainBestLink.set(domain, { url, date });
                });
            });

        const uniqueUrls = [...domainBestLink.values()].map(v => v.url);

        // Steam: если в ссылках есть store.steampowered.com — тянем данные по API
        const steamUrl   = uniqueUrls.find(u => getHostname(u) === 'store.steampowered.com');
        const steamAppId = steamUrl ? (steamUrl.match(/\/app\/(\d+)/)?.[1] ?? null) : null;
        const [steamData, steamSpyTags] = steamAppId
            ? await Promise.all([fetchSteamDetails(steamAppId), fetchSteamSpy(steamAppId)])
            : [null, null];

        // [Системные требования] из Steam (только Windows / pc_requirements)
        const minReqsText = parseSteamMinReqs(steamData?.pc_requirements?.minimum);

        // [Теги]: VNDB + платформы (win/lin/mac) + Steam platforms + Steam genres + SteamSpy
        const PLAT_WHITELIST = new Set(['win', 'lin', 'mac']);
        const STEAM_PLAT_MAP = { windows: 'win', mac: 'mac', linux: 'lin' };
        const seen    = new Set();
        const allTags = [];
        const addTag  = t => { t = t.trim(); if (!t) return; const k = t.toLowerCase(); if (!seen.has(k)) { seen.add(k); allTags.push(t); } };

        (data.tags || []).filter(t => !t.lie).forEach(t => addTag(t.name));
        platformsArr.filter(p => PLAT_WHITELIST.has(p)).forEach(addTag);
        Object.entries(steamData?.platforms || {}).filter(([, v]) => v)
            .forEach(([k]) => { const m = STEAM_PLAT_MAP[k]; if (m) addTag(m); });
        (steamData?.genres || []).forEach(g => g.description && addTag(g.description));
        Object.keys(steamSpyTags || {}).forEach(addTag);

        const tags = allTags.join(', ');

        const vndbIconHTML = `<a href="${location.href}" target="_blank" rel="noopener" title="${location.href}"><img src="${ICON_BASE}vndb.png" style="height:28px;vertical-align:middle;border-radius:4px;"></a>`;
        const linksHTML    = `<div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:6px;">${uniqueUrls.map(urlToIcon).join('')}${vndbIconHTML}</div>`;

        const desc  = (data.description || '—').replace(/\[(?:[^\[\]]|\[[^\[\]]*\])*\]\s*$/, '').trimEnd();
        const tDesc = await translateToRussian(desc);

        // [Постер] — ссылка вместо <img>
        const posterHTML = data.image?.url
            ? `<a href="${data.image.url}" target="_blank" rel="noopener">${data.image.url}</a>`
            : '—';

        // [Постер steam] — обложки по app_id, без запроса к API
        const steamPosterHTML = steamAppId
            ? [
                `capsule_616x353.jpg`,
                `library_600x900.jpg`,
                `library_600x900_2x.jpg`,
                `library_hero.jpg`,
                `library_hero_2x.jpg`,
              ].map(f => {
                  const url = `https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/${steamAppId}/${f}`;
                  return `<a href="${url}" target="_blank" rel="noopener" style="display:block;margin:2px 0;">${url}</a>`;
              }).join('')
            : null;

        // [Скриншоты/Примеры] — ссылки VNDB + ссылки из Steam (path_full)
        const steamScreenUrls = (steamData?.screenshots || []).map(s => s.path_full).filter(Boolean);
        const allScreenUrls   = [...(data.screenshots || []).map(s => s.url), ...steamScreenUrls];
        const screensHTML = allScreenUrls.length
            ? allScreenUrls.map(url => `<a href="${url}" target="_blank" rel="noopener" style="display:block;margin:2px 0;">${url}</a>`).join('')
            : '—';

        const modal = document.createElement('div');
        modal.id = 'vndb-modal';
        modal.style.cssText = 'position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:1000px;background:#1e1e1e;border-radius:12px;box-shadow:0 0 40px rgba(0,0,0,.9);z-index:99999;overflow:hidden;max-height:94vh;resize:both;min-width:720px;min-height:500px;';

        modal.innerHTML = `
            <div id="modal-header" style="position:sticky;top:0;background:#1e1e1e;padding:12px 20px;border-bottom:2px solid #444;display:flex;justify-content:space-between;align-items:center;cursor:move;z-index:10;">
                <h2 style="margin:0;color:#4caf9c;">${data.title}</h2>
                <button id="close-btn" style="font-size:28px;background:none;border:none;color:#ddd;cursor:pointer;line-height:1;">×</button>
            </div>
            <div style="padding:25px;overflow:auto;max-height:calc(94vh - 60px);">
                ${row('Постер',              posterHTML)}
                ${steamPosterHTML ? row('Постер steam', steamPosterHTML) : ''}
                ${titlesHTML}
                ${row('Год выпуска',          data.released?.split('-')[0])}
                ${row('Дата выпуска',         data.released?.replace(/-/g, '/'))}
                ${row('Теги',                tags)}
                ${row('Цензура',             hasUncensored ? 'Нет' : 'Есть')}
                ${row('Разработчик/Издатель', creators.join(' / '))}
                ${row('Платформа',           platforms)}
                ${row('Тип издания',         hasNonTrial ? 'Релиз' : releases.length ? 'Демо-версия' : null)}
                ${row('Язык игры',           langs)}
                ${row('Озвучка',             voicedVal)}
                ${minReqsText ? sect('Системные требования', `<pre class="vpre">${minReqsText}</pre>`) : ''}
                ${sect('Описание',           `<pre class="vpre">${fmt(desc)}</pre>`)}
                ${sect('Перевод на русский', `<pre class="vpre vborder">${fmt(tDesc)}</pre>`, true)}
                ${sect('Ссылки',             linksHTML, true)}
                ${sect('Скриншоты/Примеры', screensHTML, true)}
            </div>`;

        document.body.appendChild(modal);
        modal.querySelector('#close-btn').onclick = () => modal.remove();
        makeDraggable(modal);
    }

    // ─── Перетаскивание/resize модального окна ──────────────────────────────────

    function makeDraggable(el) {
        const header = el.querySelector('#modal-header');
        let drag = null;

        const save = () => localStorage.setItem('vndb_modal_pos', JSON.stringify({
            top: el.style.top, left: el.style.left,
            width: el.offsetWidth + 'px', height: el.offsetHeight + 'px'
        }));

        header.onmousedown = e => {
            if (e.target.tagName === 'BUTTON') return;
            e.preventDefault();
            drag = { x: e.clientX, y: e.clientY };
            document.onmousemove = e => {
                el.style.top       = (el.offsetTop  - (drag.y - e.clientY)) + 'px';
                el.style.left      = (el.offsetLeft - (drag.x - e.clientX)) + 'px';
                el.style.transform = 'none';
                drag = { x: e.clientX, y: e.clientY };
            };
            document.onmouseup = () => {
                document.onmousemove = document.onmouseup = null;
                save();
            };
        };

        new ResizeObserver(save).observe(el);

        try {
            const s = JSON.parse(localStorage.getItem('vndb_modal_pos') || '{}');
            // fix: в оригинале было два отдельных if (s.top), объединяем в один блок
            if (s.top)    { el.style.top = s.top; el.style.transform = 'none'; }
            if (s.left)   el.style.left   = s.left;
            if (s.width)  el.style.width  = s.width;
            if (s.height) el.style.height = s.height;
        } catch {}
    }

    // ─── Кнопка ─────────────────────────────────────────────────────────────────

    const btn = document.createElement('a');
    btn.href = '#';
    btn.textContent = '📋 Карточка VN';
    btn.style.cssText = 'margin-left:12px;padding:6px 12px;background:#2a8;color:white;border-radius:4px;text-decoration:none;font-size:14px;cursor:pointer;vertical-align:middle;';
    (document.querySelector('h1') ?? document.querySelector('.page-header h1'))?.appendChild(btn);

    btn.addEventListener('click', async e => {
        e.preventDefault();
        btn.textContent = '⏳ Загрузка...';
        try {
            const [vnData, releases] = await Promise.all([fetchVNData(vnId), fetchReleases(vnId)]);
            await showModal(vnData, releases);
        } catch (err) {
            alert('Ошибка API:\n' + (err.message || err));
        } finally {
            btn.textContent = '📋 Карточка VN';
        }
    });

    GM_addStyle(`
        .vl      { color: #8cf; }
        .vlink   { color: #4caf9c; word-break: break-all; }
        .vslink  { display: block; margin: 2px 0; }
        .vpre    { background: #252525; padding: 18px; border-radius: 8px; white-space: pre-wrap; margin: 6px 0 0; }
        .vborder { border-left: 5px solid #4caf9c; }
        .vsect   { margin-top: 8px; }
        .vsect2  { margin-top: 20px; }
    `);
})();
