<?php
function icon($n, $s = 20)
{
    static $p = [
        'grid' => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
        'users' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
        'db' => '<ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>',
        'file' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8"/>',
        'out' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5M21 12H9"/>',
        'home' => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22V12h6v10"/>',
        'shield' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>',
        'build' => '<rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4M8 6h.01M12 6h.01M16 6h.01M8 10h.01M12 10h.01M16 10h.01"/>',
        'pulse' => '<path d="M22 12h-4l-3 9L9 3l-3 9H2"/>',
        'menu' => '<path d="M3 6h18M3 12h18M3 18h18"/>',
        'eye' => '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>',
        'edit' => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/>',
    ];
    return "<svg width='$s' height='$s' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='1.8' stroke-linecap='round' stroke-linejoin='round'>{$p[$n]}</svg>";
}
const PALET = ['#2563eb', '#10b981', '#f2b01e', '#f43f5e', '#8b5cf6', '#06b6d4'];
// Diagram donat (SVG murni, tanpa library)
function donut(array $rows)
{
    $total = array_sum(array_column($rows, 'jml'));
    $off = 25;
    $s = '';
    $leg = '';
    foreach ($rows as $i => $r) {
        $c = PALET[$i % 6];
        $pct = $total ? $r['jml'] / $total * 100 : 0;
        if ($pct > 0) $s .= "<circle r='15.9155' cx='21' cy='21' fill='none' stroke='$c' stroke-width='5' stroke-dasharray='" . round($pct, 2) . ' ' . round(100 - $pct, 2) . "' stroke-dashoffset='" . round($off, 2) . "'/>";
        $off -= $pct;
        $leg .= "<li><i style='background:$c'></i>" . e($r['nama']) . "<b>{$r['jml']}</b></li>";
    }
    return "<div class='donut'><svg viewBox='0 0 42 42'><circle r='15.9155' cx='21' cy='21' fill='none' stroke='#eef2f9' stroke-width='5'/>$s</svg><div class='dn'><b>$total</b>dosen</div></div><ul class='legend'>$leg</ul>";
}
// Diagram batang horizontal
function bars(array $rows)
{
    $max = max(array_merge([1], array_column($rows, 'jml')));
    $h = '';
    foreach ($rows as $i => $r)
        $h .= "<div class='bar'><span>" . e($r['nama']) . "</span><div class='trk'><div class='fill' style='--w:" . round($r['jml'] / $max * 100) . '%;background:' . PALET[$i % 6] . "'></div></div><b>{$r['jml']}</b></div>";
    return $h;
}
function inisial($nama)
{
    $nama = preg_replace('/,.*$/', '', $nama);
    $k = preg_split('/\s+/', trim($nama));
    return strtoupper(mb_substr($k[0], 0, 1) . (isset($k[1]) ? mb_substr($k[1], 0, 1) : ''));
}
function logo($s = 38)
{
    return "<img src='" . BASE_URL . "/assets/img/logo umb.png' width='$s' height='$s' alt='Logo UM Bengkulu' style='object-fit:contain;flex:none'>";
}

// Diagram kolom (batang vertikal)
function columns(array $rows)
{
    $max = max(array_merge([1], array_column($rows, 'jml')));
    $h = '';
    foreach ($rows as $i => $r)
        $h .= "<div class='col' title='" . e($r['nama']) . ": {$r['jml']}'><b>{$r['jml']}</b><div class='cb' style='--h:" . round($r['jml'] / $max, 3) . ';background:' . PALET[$i % 6] . "'></div><span>" . e($r['nama']) . '</span></div>';
    return "<div class='cols'>$h</div>";
}
// Panel grafik dengan tombol ganti jenis diagram (donat / batang / kolom)
function chartpanel($judul, array $rows, $def = 'donut')
{
    $v = ['donut' => donut($rows), 'bars' => bars($rows), 'cols' => columns($rows)];
    $b = '';
    $s = '';
    foreach (['donut' => 'Donat', 'bars' => 'Batang', 'cols' => 'Kolom'] as $k => $l) {
        $b .= "<button type='button' data-v='$k' class='" . ($k == $def ? 'on' : '') . "'>$l</button>";
        $s .= "<div class='v v-$k'" . ($k == $def ? '' : ' hidden') . ">{$v[$k]}</div>";
    }
    return "<div class='panel cp'><div class='cph'><h3>" . e($judul) . "</h3><div class='seg'>$b</div></div>$s</div>";
}
