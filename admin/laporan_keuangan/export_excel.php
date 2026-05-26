<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';

if ($_SESSION['role'] != 'admin') { header("Location: ../../auth/login.php"); exit; }

$filter_tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');
$filter_tipe  = isset($_GET['tipe']) && $_GET['tipe'] === 'tahunan' ? 'tahunan' : 'bulanan';

$nama_bulan = ['','Januari','Februari','Maret','April','Mei','Juni',
               'Juli','Agustus','September','Oktober','November','Desember'];

// ── Ambil data ────────────────────────────────────────────────────────────────
if ($filter_tipe === 'bulanan') {
    $q = mysqli_query($koneksi, "
        SELECT bulan_tagihan, tahun_tagihan,
               COUNT(*) AS total_transaksi,
               SUM(jumlah_bayar) AS total_tagihan,
               SUM(CASE WHEN status_pembayaran='lunas' THEN jumlah_bayar ELSE 0 END) AS pendapatan,
               SUM(CASE WHEN status_pembayaran='belum_bayar' THEN jumlah_bayar ELSE 0 END) AS belum_bayar,
               COUNT(CASE WHEN status_pembayaran='lunas' THEN 1 END) AS jml_lunas,
               COUNT(CASE WHEN status_pembayaran='belum_bayar' THEN 1 END) AS jml_belum,
               COUNT(CASE WHEN status_pembayaran='menunggu' THEN 1 END) AS jml_menunggu
        FROM tb_transaksi
        WHERE tahun_tagihan = $filter_tahun
        GROUP BY tahun_tagihan, bulan_tagihan
        ORDER BY bulan_tagihan ASC
    ");
    $rows = [];
    while ($r = mysqli_fetch_assoc($q)) $rows[] = $r;
    $filename = "Laporan_Keuangan_Bulanan_{$filter_tahun}";
} else {
    $q = mysqli_query($koneksi, "
        SELECT tahun_tagihan,
               COUNT(*) AS total_transaksi,
               SUM(jumlah_bayar) AS total_tagihan,
               SUM(CASE WHEN status_pembayaran='lunas' THEN jumlah_bayar ELSE 0 END) AS pendapatan,
               SUM(CASE WHEN status_pembayaran='belum_bayar' THEN jumlah_bayar ELSE 0 END) AS belum_bayar,
               COUNT(CASE WHEN status_pembayaran='lunas' THEN 1 END) AS jml_lunas,
               COUNT(CASE WHEN status_pembayaran='belum_bayar' THEN 1 END) AS jml_belum
        FROM tb_transaksi
        GROUP BY tahun_tagihan
        ORDER BY tahun_tagihan DESC
    ");
    $rows = [];
    while ($r = mysqli_fetch_assoc($q)) $rows[] = $r;
    $filename = "Laporan_Keuangan_Tahunan_" . date('Y');
}

// ── Generate XLSX menggunakan ZipArchive (built-in PHP) ───────────────────────
// Format: SpreadsheetML (xlsx = zip berisi XML)

function xlsxEscape($v) {
    return htmlspecialchars((string)$v, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

function xlsxNum($v) { return (float)$v; }

// Shared strings
$sharedStrings = [];
$sharedIdx     = 0;

function addStr($s) {
    global $sharedStrings, $sharedIdx;
    $s = (string)$s;
    if (!isset($sharedStrings[$s])) {
        $sharedStrings[$s] = $sharedIdx++;
    }
    return $sharedStrings[$s];
}

// Build rows data
$headerRow     = [];
$dataRows      = [];
$totalRow      = [];

if ($filter_tipe === 'bulanan') {
    $headerRow = ['No','Bulan','Tahun','Total Transaksi','Lunas','Belum Bayar',
                  'Menunggu','Pendapatan Masuk (Rp)','Tunggakan (Rp)','Total Tagihan (Rp)','Realisasi (%)'];

    $tot = array_fill_keys(['total_transaksi','jml_lunas','jml_belum','jml_menunggu','pendapatan','belum_bayar','total_tagihan'], 0);

    $no = 1;
    foreach ($rows as $r) {
        $pct = $r['total_tagihan'] > 0 ? round($r['pendapatan'] / $r['total_tagihan'] * 100, 1) : 0;
        $dataRows[] = [
            $no++,
            $nama_bulan[(int)$r['bulan_tagihan']],
            (int)$r['tahun_tagihan'],
            (int)$r['total_transaksi'],
            (int)$r['jml_lunas'],
            (int)$r['jml_belum'],
            (int)$r['jml_menunggu'],
            (float)$r['pendapatan'],
            (float)$r['belum_bayar'],
            (float)$r['total_tagihan'],
            $pct . '%',
        ];
        foreach ($tot as $k => $_) $tot[$k] += $r[$k];
    }

    $totalRow = [
        '','TOTAL','',
        $tot['total_transaksi'], $tot['jml_lunas'], $tot['jml_belum'], $tot['jml_menunggu'],
        (float)$tot['pendapatan'], (float)$tot['belum_bayar'], (float)$tot['total_tagihan'], '',
    ];
} else {
    $headerRow = ['No','Tahun','Total Transaksi','Lunas','Belum Bayar',
                  'Pendapatan Masuk (Rp)','Tunggakan (Rp)','Total Tagihan (Rp)','Realisasi (%)'];

    $tot = array_fill_keys(['total_transaksi','jml_lunas','jml_belum','pendapatan','belum_bayar','total_tagihan'], 0);

    $no = 1;
    foreach ($rows as $r) {
        $pct = $r['total_tagihan'] > 0 ? round($r['pendapatan'] / $r['total_tagihan'] * 100, 1) : 0;
        $dataRows[] = [
            $no++,
            (int)$r['tahun_tagihan'],
            (int)$r['total_transaksi'],
            (int)$r['jml_lunas'],
            (int)$r['jml_belum'],
            (float)$r['pendapatan'],
            (float)$r['belum_bayar'],
            (float)$r['total_tagihan'],
            $pct . '%',
        ];
        foreach ($tot as $k => $_) $tot[$k] += $r[$k];
    }

    $totalRow = [
        '','TOTAL',
        $tot['total_transaksi'], $tot['jml_lunas'], $tot['jml_belum'],
        (float)$tot['pendapatan'], (float)$tot['belum_bayar'], (float)$tot['total_tagihan'], '',
    ];
}

// ── Build XML parts ───────────────────────────────────────────────────────────

// Pre-index all strings
addStr('Anuwani Network');
addStr('Laporan Keuangan');
foreach ($headerRow as $h) addStr($h);
foreach ($dataRows as $dr) {
    foreach ($dr as $cell) {
        if (!is_float($cell) && !is_int($cell)) addStr($cell);
    }
}
foreach ($totalRow as $cell) {
    if (!is_float($cell) && !is_int($cell)) addStr($cell);
}

// Convert letter column
function colLetter($n) {
    $r = '';
    while ($n > 0) {
        $n--;
        $r = chr(65 + ($n % 26)) . $r;
        $n = intdiv($n, 26);
    }
    return $r;
}

// Build sheet XML
function buildCell($row, $col, $val, $style = 0) {
    $ref = colLetter($col) . $row;
    if (is_int($val) || is_float($val)) {
        return "<c r=\"$ref\" s=\"$style\"><v>" . xlsxNum($val) . "</v></c>";
    } else {
        $si = addStr((string)$val);
        return "<c r=\"$ref\" t=\"s\" s=\"$style\"><v>$si</v></c>";
    }
}

$rowNum = 1;
$sheetRows = '';

// Title rows
$titleIdx = addStr('Anuwani Network — Laporan Keuangan ' . ($filter_tipe === 'bulanan' ? "Bulanan Tahun $filter_tahun" : 'Tahunan'));
$dateIdx  = addStr('Dicetak: ' . date('d F Y, H:i') . ' WIB');
$sheetRows .= "<row r=\"$rowNum\"><c r=\"A$rowNum\" t=\"s\" s=\"2\"><v>$titleIdx</v></c></row>";
$rowNum++;
$sheetRows .= "<row r=\"$rowNum\"><c r=\"A$rowNum\" t=\"s\"><v>$dateIdx</v></c></row>";
$rowNum++;
$rowNum++; // blank

// Header row
$sheetRows .= "<row r=\"$rowNum\">";
$col = 1;
foreach ($headerRow as $h) {
    $si = addStr($h);
    $sheetRows .= "<c r=\"" . colLetter($col) . "$rowNum\" t=\"s\" s=\"1\"><v>$si</v></c>";
    $col++;
}
$sheetRows .= "</row>";
$rowNum++;

// Data rows
foreach ($dataRows as $dr) {
    $sheetRows .= "<row r=\"$rowNum\">";
    $col = 1;
    foreach ($dr as $cell) {
        if (is_float($cell)) {
            $sheetRows .= "<c r=\"" . colLetter($col) . "$rowNum\" s=\"3\"><v>" . xlsxNum($cell) . "</v></c>";
        } elseif (is_int($cell)) {
            $sheetRows .= "<c r=\"" . colLetter($col) . "$rowNum\"><v>$cell</v></c>";
        } else {
            $si = addStr($cell);
            $sheetRows .= "<c r=\"" . colLetter($col) . "$rowNum\" t=\"s\"><v>$si</v></c>";
        }
        $col++;
    }
    $sheetRows .= "</row>";
    $rowNum++;
}

// Total row
$sheetRows .= "<row r=\"$rowNum\">";
$col = 1;
foreach ($totalRow as $cell) {
    if (is_float($cell)) {
        $sheetRows .= "<c r=\"" . colLetter($col) . "$rowNum\" s=\"4\"><v>" . xlsxNum($cell) . "</v></c>";
    } elseif (is_int($cell)) {
        $sheetRows .= "<c r=\"" . colLetter($col) . "$rowNum\" s=\"1\"><v>$cell</v></c>";
    } else {
        $si = addStr($cell);
        $sheetRows .= "<c r=\"" . colLetter($col) . "$rowNum\" t=\"s\" s=\"1\"><v>$si</v></c>";
    }
    $col++;
}
$sheetRows .= "</row>";

// Shared strings XML
$ssXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count($sharedStrings) . '" uniqueCount="' . count($sharedStrings) . '">';
$sorted = array_flip($sharedStrings); ksort($sorted);
foreach ($sorted as $idx => $str) {
    $ssXml .= '<si><t xml:space="preserve">' . xlsxEscape($str) . '</t></si>';
}
$ssXml .= '</sst>';

// Styles XML
$stylesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <fonts count="3">
    <font><sz val="11"/><name val="Arial"/></font>
    <font><b/><sz val="11"/><name val="Arial"/></font>
    <font><b/><sz val="13"/><color rgb="FFf4600c"/><name val="Arial"/></font>
  </fonts>
  <fills count="4">
    <fill><patternFill patternType="none"/></fill>
    <fill><patternFill patternType="gray125"/></fill>
    <fill><patternFill patternType="solid"><fgColor rgb="FFf4600c"/></patternFill></fill>
    <fill><patternFill patternType="solid"><fgColor rgb="FFfff4ee"/></patternFill></fill>
  </fills>
  <borders count="2">
    <border><left/><right/><top/><bottom/><diagonal/></border>
    <border>
      <left style="thin"><color rgb="FFe4e4e7"/></left>
      <right style="thin"><color rgb="FFe4e4e7"/></right>
      <top style="thin"><color rgb="FFe4e4e7"/></top>
      <bottom style="thin"><color rgb="FFe4e4e7"/></bottom>
    </border>
  </borders>
  <cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>
  <cellXfs count="5">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0"><alignment wrapText="1"/></xf>
    <xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0"><alignment horizontal="center"/></xf>
    <xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0"/>
    <xf numFmtId="#,##0" fontId="0" fillId="0" borderId="1" xfId="0"/>
    <xf numFmtId="#,##0" fontId="1" fillId="3" borderId="1" xfId="0"/>
  </cellXfs>
</styleSheet>';

// Sheet XML
$colCount = count($headerRow);
$lastCol  = colLetter($colCount);
$sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <sheetViews><sheetView workbookViewId="0" showGridLines="1"/></sheetViews>
  <sheetFormatPr defaultRowHeight="16"/>
  <sheetData>' . $sheetRows . '</sheetData>
  <autoFilter ref="A4:' . $lastCol . '4"/>
</worksheet>';

// Workbook XML
$workbookXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"
          xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Laporan Keuangan" sheetId="1" r:id="rId1"/>
  </sheets>
</workbook>';

$workbookRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>';

$contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml"  ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml"            ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml"   ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/sharedStrings.xml"       ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>
  <Override PartName="/xl/styles.xml"              ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
</Types>';

$rootRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>';

// ── Build ZIP / XLSX ──────────────────────────────────────────────────────────
$tmpFile = tempnam(sys_get_temp_dir(), 'xlsx_');
$zip = new ZipArchive();
$zip->open($tmpFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

$zip->addFromString('[Content_Types].xml',              $contentTypes);
$zip->addFromString('_rels/.rels',                      $rootRels);
$zip->addFromString('xl/workbook.xml',                  $workbookXml);
$zip->addFromString('xl/_rels/workbook.xml.rels',       $workbookRels);
$zip->addFromString('xl/worksheets/sheet1.xml',         $sheetXml);
$zip->addFromString('xl/sharedStrings.xml',             $ssXml);
$zip->addFromString('xl/styles.xml',                    $stylesXml);
$zip->close();

// ── Output ────────────────────────────────────────────────────────────────────
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '_' . date('Ymd') . '.xlsx"');
header('Content-Length: ' . filesize($tmpFile));
header('Cache-Control: no-cache, must-revalidate');
readfile($tmpFile);
unlink($tmpFile);
exit;
