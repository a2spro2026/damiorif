<!DOCTYPE html>
<html lang="fr" translate="no">
<head>
    <meta charset="utf-8">
    <meta name="google" content="notranslate">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bon {{ $bon->numero_bon }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            background: #fff;
            color: #000;
            font-family: "Courier New", Courier, monospace;
            font-size: 12px;
            line-height: 1.35;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        body { padding: 8px; }
        .ticket {
            width: 72mm;
            max-width: 100%;
            margin: 0 auto;
        }
        .print-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
            justify-content: center;
            max-width: 420px;
            margin: 0 auto 14px;
            font-family: Arial, sans-serif;
        }
        .pb-btn {
            flex: 1 1 140px;
            padding: 12px 14px;
            border: 1px solid #111;
            border-radius: 10px;
            background: #fff;
            color: #111;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
        }
        .pb-btn.pb-main { background: #0f766e; border-color: #0f766e; color: #fff; }
        .pb-btn.pb-ghost { border-style: dashed; }
        .pb-btn:disabled { opacity: .55; cursor: wait; }
        .pb-width { font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 6px; }
        .pb-width select { padding: 6px 8px; border-radius: 8px; border: 1px solid #111; font-size: 14px; }
        .pb-status { flex: 1 1 100%; text-align: center; font-size: 13px; min-height: 18px; color: #333; }
        .pb-status.is-ok { color: #0f766e; font-weight: 700; }
        .pb-status.is-warn { color: #b45309; font-weight: 700; }
        .pb-status.is-err { color: #b91c1c; font-weight: 700; }
        .header {
            text-align: center;
            padding-bottom: 8px;
            border-bottom: 2px solid #000;
            margin-bottom: 8px;
        }
        .header img {
            display: block;
            width: 42mm;
            max-width: 160px;
            height: auto;
            margin: 0 auto 6px;
            object-fit: contain;
        }
        .brand {
            font-size: 18px;
            font-weight: 900;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }
        .doc-title {
            text-align: center;
            font-weight: 800;
            font-size: 13px;
            margin: 8px 0 6px;
            text-transform: uppercase;
        }
        .meta { margin-bottom: 8px; }
        .meta .row {
            display: flex;
            justify-content: space-between;
            gap: 6px;
            padding: 1px 0;
        }
        .meta .label { font-weight: 700; white-space: nowrap; }
        .meta .value { text-align: right; word-break: break-word; }
        .sep {
            border: 0;
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        .item {
            padding: 6px 0;
            border-bottom: 1px dashed #000;
        }
        .item:last-child,
        .item:has(+ .totals) { border-bottom: 0; }
        .item-ref .k { text-transform: none; }
        .item-ref {
            font-weight: 800;
            font-size: 12px;
            text-transform: uppercase;
        }
        .item-des {
            margin: 2px 0 4px;
            word-break: break-word;
        }
        .cols {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 4px;
        }
        .cols span:nth-child(2) { text-align: center; }
        .cols span:nth-child(3) { text-align: right; font-weight: 700; }
        .cols-head {
            font-weight: 800;
            padding-bottom: 4px;
            border-bottom: 1px solid #000;
        }
        .k { font-weight: 700; }
        .totals {
            margin-top: 8px;
            padding-top: 6px;
            border-top: 2px solid #000;
        }
        .totals .row {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            padding: 2px 0;
            font-weight: 700;
        }
        .totals .grand {
            font-size: 14px;
            font-weight: 900;
        }
        .footer {
            margin-top: 12px;
            padding-top: 8px;
            border-top: 2px solid #000;
            text-align: center;
            font-size: 11px;
            line-height: 1.45;
        }
        .footer .thanks {
            font-weight: 800;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .footer .line { word-break: break-word; }
        @page {
            size: 80mm auto;
            margin: 2mm;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
            .ticket { width: 76mm; }
            a[href]::after { content: none !important; }
        }
    </style>
</head>
<body>
@php
    $logoPath = public_path('images/logo.png');
    $logoSrc = is_file($logoPath)
        ? 'data:image/png;base64,'.base64_encode((string) file_get_contents($logoPath))
        : asset('images/logo.png');
    $companyName = (string) config('company.name', 'DAMIO-RIF');
    $companyAddress = trim((string) config('company.address', ''));
    $depotCity = \App\Support\Depots::city($bon->depot);
    $companyPhone = trim((string) config('company.phone', ''));
    $fmt = fn ($v) => number_format((float) $v, 2, ',', ' ');
    $ticket = [
        'company' => $companyName,
        'numero' => (string) $bon->numero_bon,
        'client' => (string) $bon->nom_client,
        'lignes' => $bon->lignes->map(fn ($l) => [
            'ref' => (string) ($l->ref ?: '—'),
            'designation' => (string) $l->designation,
            'qte' => $fmt($l->qte),
            'pu' => $fmt($l->prix_unitaire),
            'st' => $fmt($l->sous_total),
        ])->values()->all(),
        'total' => $fmt($bon->montant),
        'footer' => array_values(array_filter([
            $companyPhone !== '' ? 'Mobile : '.$companyPhone : null,
            $companyAddress !== '' ? 'Adresse : '.$companyAddress : null,
            'Dépôt : '.$depotCity,
        ])),
    ];
@endphp

    <div class="print-bar no-print">
        <button type="button" class="pb-btn pb-main" id="btPrintBtn">Imprimer Bluetooth</button>
        <button type="button" class="pb-btn" id="rawbtPrintBtn">Via RawBT</button>
        <button type="button" class="pb-btn pb-ghost" onclick="window.print()">Imprimer (PC)</button>
        <label class="pb-width">Papier
            <select id="paperWidth">
                <option value="384">58 mm</option>
                <option value="576">80 mm</option>
            </select>
        </label>
        <div class="pb-status" id="btStatus" role="status" aria-live="polite"></div>
    </div>

    <div class="ticket notranslate" translate="no">
        <header class="header">
            <img src="{{ $logoSrc }}" alt="{{ $companyName }}" width="160" height="80">
            <div class="brand">{{ $companyName }}</div>
        </header>

        <div class="meta">
            <div class="row"><span class="label">N°</span><span class="value">{{ $ticket['numero'] }}</span></div>
            <div class="row"><span class="label">Nom Client</span><span class="value">{{ $ticket['client'] }}</span></div>
        </div>

        <hr class="sep">

        <div class="cols cols-head">
            <span>Qte</span><span>P/U</span><span>S-total</span>
        </div>

        @forelse ($ticket['lignes'] as $ligne)
            <div class="item">
                <div class="item-ref"><span class="k">Réf :</span> {{ $ligne['ref'] }}</div>
                <div class="item-des" dir="auto">{{ $ligne['designation'] }}</div>
                <div class="cols">
                    <span>{{ $ligne['qte'] }}</span><span>{{ $ligne['pu'] }}</span><span>{{ $ligne['st'] }}</span>
                </div>
            </div>
        @empty
            <div class="item">Aucun article.</div>
        @endforelse

        <div class="totals">
            <div class="row grand"><span>TOTAL</span><span>{{ $ticket['total'] }}</span></div>
        </div>

        <footer class="footer">
            @foreach ($ticket['footer'] as $line)
                <div class="line">{{ $line }}</div>
            @endforeach
        </footer>
    </div>

    <script>
    (function () {
        var T = @json($ticket);
        var LOGO = @json($logoSrc);
        // Services BLE des imprimantes thermiques courantes (MTP-II, PT-210, Goojprt, Xprinter…).
        var SERVICES = [
            '000018f0-0000-1000-8000-00805f9b34fb',
            'e7810a71-73ae-499d-8c15-faa9aef0c3f2',
            '49535343-fe7d-4ae5-8fa9-9fafd205e455',
            '0000ff00-0000-1000-8000-00805f9b34fb',
            '0000ffe0-0000-1000-8000-00805f9b34fb',
            '0000fee7-0000-1000-8000-00805f9b34fb',
            '0000ae30-0000-1000-8000-00805f9b34fb',
            '0000af30-0000-1000-8000-00805f9b34fb'
        ];
        var RTL = /[\u0590-\u08FF\uFB1D-\uFDFF\uFE70-\uFEFF]/;
        var statusEl = document.getElementById('btStatus');
        var widthSel = document.getElementById('paperWidth');
        var btBtn = document.getElementById('btPrintBtn');
        var rawBtn = document.getElementById('rawbtPrintBtn');
        var bt = { device: null, ch: null };

        var savedWidth = localStorage.getItem('damio_paper_width');
        if (savedWidth) widthSel.value = savedWidth;
        widthSel.addEventListener('change', function () {
            localStorage.setItem('damio_paper_width', widthSel.value);
        });

        function setStatus(msg, kind) {
            statusEl.textContent = msg || '';
            statusEl.className = 'pb-status' + (kind ? ' is-' + kind : '');
        }

        function sleep(ms) { return new Promise(function (r) { setTimeout(r, ms); }); }

        function loadImage(src) {
            return new Promise(function (resolve) {
                var img = new Image();
                img.onload = function () { resolve(img); };
                img.onerror = function () { resolve(null); };
                img.src = src;
            });
        }

        function wrap(ctx, text, maxW) {
            var words = String(text).split(/\s+/).filter(Boolean);
            var lines = [];
            var cur = '';
            words.forEach(function (w) {
                var test = cur ? cur + ' ' + w : w;
                if (ctx.measureText(test).width <= maxW) { cur = test; return; }
                if (cur) lines.push(cur);
                cur = w;
                while (ctx.measureText(cur).width > maxW && cur.length > 1) {
                    var cut = cur.length - 1;
                    while (cut > 1 && ctx.measureText(cur.slice(0, cut)).width > maxW) cut--;
                    lines.push(cur.slice(0, cut));
                    cur = cur.slice(cut);
                }
            });
            if (cur) lines.push(cur);
            return lines.length ? lines : [''];
        }

        async function renderTicket(W) {
            var s = W / 384;
            var pad = Math.round(4 * s);
            var c = document.createElement('canvas');
            c.width = W;
            c.height = Math.round((1100 + T.lignes.length * 220) * s);
            var ctx = c.getContext('2d');
            ctx.fillStyle = '#fff';
            ctx.fillRect(0, 0, c.width, c.height);
            ctx.fillStyle = '#000';
            ctx.strokeStyle = '#000';
            ctx.textBaseline = 'top';
            var y = pad;

            function font(px, bold) {
                ctx.font = (bold ? 'bold ' : '') + Math.round(px * s) + 'px Arial, "Noto Naskh Arabic", sans-serif';
                return Math.round(px * s * 1.32);
            }
            function para(str, align, px, bold) {
                var lh = font(px, bold);
                var rtl = RTL.test(str);
                ctx.direction = rtl ? 'rtl' : 'ltr';
                wrap(ctx, str, W - 2 * pad).forEach(function (l) {
                    if (align === 'center') { ctx.textAlign = 'center'; ctx.fillText(l, W / 2, y); }
                    else if (rtl) { ctx.textAlign = 'right'; ctx.fillText(l, W - pad, y); }
                    else { ctx.textAlign = 'left'; ctx.fillText(l, pad, y); }
                    y += lh;
                });
            }
            function row(label, value, px, bold) {
                var lh = font(px, true);
                ctx.direction = 'ltr';
                ctx.textAlign = 'left';
                ctx.fillText(label, pad, y);
                var labelW = ctx.measureText(label).width + Math.round(10 * s);
                font(px, bold);
                ctx.direction = RTL.test(value) ? 'rtl' : 'ltr';
                ctx.textAlign = 'right';
                wrap(ctx, value, W - 2 * pad - labelW).forEach(function (l) {
                    ctx.fillText(l, W - pad, y);
                    y += lh;
                });
            }
            function rule(dashed, thick) {
                y += Math.round(5 * s);
                ctx.lineWidth = Math.max(1, Math.round((thick || 2) * s));
                ctx.setLineDash(dashed ? [Math.round(6 * s), Math.round(4 * s)] : []);
                ctx.beginPath();
                ctx.moveTo(pad, y);
                ctx.lineTo(W - pad, y);
                ctx.stroke();
                ctx.setLineDash([]);
                y += Math.round(7 * s);
            }

            var logo = await loadImage(LOGO);
            if (logo && logo.width) {
                var lw = Math.round(W * 0.55);
                var lhImg = Math.round(logo.height * lw / logo.width);
                ctx.drawImage(logo, Math.round((W - lw) / 2), y, lw, lhImg);
                y += lhImg + Math.round(6 * s);
            }
            function cols(a, b, c3, px, bold) {
                var lh = font(px, bold);
                ctx.direction = 'ltr';
                ctx.textAlign = 'left';
                ctx.fillText(a, pad, y);
                ctx.textAlign = 'center';
                ctx.fillText(b, W / 2, y);
                ctx.textAlign = 'right';
                ctx.fillText(c3, W - pad, y);
                y += lh;
            }

            para(T.company, 'center', 30, true);
            rule(false, 3);
            row('N°', T.numero, 21, false);
            row('Nom Client', T.client, 21, false);
            rule(false, 2);

            cols('Qte', 'P/U', 'S-total', 20, true);
            rule(false, 1);
            if (!T.lignes.length) para('Aucun article.', 'left', 20, false);
            T.lignes.forEach(function (l, i) {
                para('Réf : ' + l.ref, 'left', 20, true);
                para(l.designation, 'left', 20, false);
                cols(l.qte, l.pu, l.st, 20, true);
                if (i < T.lignes.length - 1) rule(true, 1);
            });

            rule(false, 3);
            row('TOTAL', T.total, 26, true);
            rule(false, 3);

            T.footer.forEach(function (line) { para(line, 'center', 19, false); });
            y += Math.round(10 * s);

            var out = document.createElement('canvas');
            out.width = W;
            out.height = Math.min(y, c.height);
            out.getContext('2d').drawImage(c, 0, 0);
            return out;
        }

        function toRaster(canvas) {
            var W = canvas.width, H = canvas.height;
            var px = canvas.getContext('2d').getImageData(0, 0, W, H).data;
            var g = new Float32Array(W * H);
            for (var i = 0; i < W * H; i++) {
                g[i] = 0.299 * px[i * 4] + 0.587 * px[i * 4 + 1] + 0.114 * px[i * 4 + 2];
            }
            var bw = Math.ceil(W / 8);
            var bits = new Uint8Array(bw * H);
            for (var yy = 0; yy < H; yy++) {
                for (var x = 0; x < W; x++) {
                    var idx = yy * W + x;
                    var old = g[idx];
                    var nv = old < 128 ? 0 : 255;
                    var err = old - nv;
                    if (nv === 0) bits[yy * bw + (x >> 3)] |= (0x80 >> (x & 7));
                    if (x + 1 < W) g[idx + 1] += err * 7 / 16;
                    if (yy + 1 < H) {
                        if (x > 0) g[idx + W - 1] += err * 3 / 16;
                        g[idx + W] += err * 5 / 16;
                        if (x + 1 < W) g[idx + W + 1] += err / 16;
                    }
                }
            }
            return { bits: bits, bw: bw, h: H };
        }

        function buildEscPos(r) {
            var parts = [new Uint8Array([0x1b, 0x40])];
            var band = 128;
            for (var y0 = 0; y0 < r.h; y0 += band) {
                var h = Math.min(band, r.h - y0);
                parts.push(new Uint8Array([0x1d, 0x76, 0x30, 0x00, r.bw & 255, r.bw >> 8, h & 255, h >> 8]));
                parts.push(r.bits.subarray(y0 * r.bw, (y0 + h) * r.bw));
            }
            parts.push(new Uint8Array([0x1b, 0x64, 0x04, 0x1d, 0x56, 0x42, 0x00]));
            var len = parts.reduce(function (a, p) { return a + p.length; }, 0);
            var out = new Uint8Array(len);
            var o = 0;
            parts.forEach(function (p) { out.set(p, o); o += p.length; });
            return out;
        }

        async function buildTicketBytes() {
            var canvas = await renderTicket(parseInt(widthSel.value, 10) || 384);
            return buildEscPos(toRaster(canvas));
        }

        function toBase64(bytes) {
            var bin = '';
            for (var i = 0; i < bytes.length; i += 0x8000) {
                bin += String.fromCharCode.apply(null, bytes.subarray(i, i + 0x8000));
            }
            return btoa(bin);
        }

        async function getCharacteristic() {
            if (bt.ch && bt.device && bt.device.gatt.connected) return bt.ch;
            if (!bt.device) {
                bt.device = await navigator.bluetooth.requestDevice({ acceptAllDevices: true, optionalServices: SERVICES });
            }
            setStatus('Connexion à ' + (bt.device.name || 'l’imprimante') + '…');
            var server = await bt.device.gatt.connect();
            var services = await server.getPrimaryServices();
            for (var i = 0; i < services.length; i++) {
                var chars = await services[i].getCharacteristics();
                for (var j = 0; j < chars.length; j++) {
                    if (chars[j].properties.writeWithoutResponse || chars[j].properties.write) {
                        bt.ch = chars[j];
                        return bt.ch;
                    }
                }
            }
            throw new Error('NO_WRITE');
        }

        async function sendBle(ch, data) {
            var noResp = ch.properties.writeWithoutResponse && typeof ch.writeValueWithoutResponse === 'function';
            var chunk = 100;
            for (var i = 0; i < data.length; i += chunk) {
                var part = data.slice(i, i + chunk);
                if (noResp) {
                    await ch.writeValueWithoutResponse(part);
                    await sleep(20);
                } else if (typeof ch.writeValueWithResponse === 'function') {
                    await ch.writeValueWithResponse(part);
                } else {
                    await ch.writeValue(part);
                }
                if (i % 2000 === 0) setStatus('Impression… ' + Math.min(100, Math.round(i * 100 / data.length)) + ' %');
            }
        }

        async function printRawBt() {
            setStatus('Préparation du ticket…');
            var b64 = toBase64(await buildTicketBytes());
            setStatus('Envoi vers RawBT…', 'ok');
            if (/Android/i.test(navigator.userAgent)) {
                window.location.href = 'intent:base64,' + b64 + '#Intent;scheme=rawbt;package=ru.a402d.rawbtprinter;end;';
            } else {
                window.location.href = 'rawbt:base64,' + b64;
            }
        }

        btBtn.addEventListener('click', async function () {
            if (!navigator.bluetooth) {
                setStatus('Bluetooth direct indisponible sur ce navigateur — envoi via RawBT.', 'warn');
                try { await printRawBt(); } catch (e) { setStatus('Échec : ' + e.message, 'err'); }
                return;
            }
            btBtn.disabled = true;
            try {
                setStatus('Choisissez l’imprimante…');
                var ch = await getCharacteristic();
                setStatus('Préparation du ticket…');
                var data = await buildTicketBytes();
                await sendBle(ch, data);
                setStatus('Ticket envoyé à ' + (bt.device.name || 'l’imprimante') + '.', 'ok');
            } catch (e) {
                bt.ch = null;
                if (e && e.name === 'NotFoundError') {
                    setStatus('Aucune imprimante choisie.', 'warn');
                } else if (e && e.message === 'NO_WRITE') {
                    bt.device = null;
                    setStatus('Imprimante non compatible en direct — utilisez « Via RawBT ».', 'err');
                } else {
                    setStatus('Échec Bluetooth (' + (e && e.message ? e.message : e) + ') — utilisez « Via RawBT ».', 'err');
                }
            } finally {
                btBtn.disabled = false;
            }
        });

        rawBtn.addEventListener('click', async function () {
            try { await printRawBt(); } catch (e) { setStatus('Échec : ' + e.message, 'err'); }
        });

        window.addEventListener('pagehide', function () {
            try { if (bt.device && bt.device.gatt.connected) bt.device.gatt.disconnect(); } catch (e) {}
        });
    })();
    </script>
</body>
</html>
