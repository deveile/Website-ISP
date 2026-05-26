<?php
require_once __DIR__ . '/../../auth/cek_login.php';
require_once __DIR__ . '/../../koneksi.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

$filter_tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');
$filter_tipe  = isset($_GET['tipe']) && $_GET['tipe'] === 'tahunan' ? 'tahunan' : 'bulanan';

$nama_bulan = [
    '', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

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
    $filename = "Laporan_Keuangan_Bulanan_{$filter_tahun}";
    $judul    = "Laporan Keuangan Bulanan - Tahun $filter_tahun";
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
    $filename = "Laporan_Keuangan_Tahunan_" . date('Y');
    $judul    = "Laporan Keuangan Tahunan - Semua Periode";
}

$rows = [];
$tot  = [];
while ($r = mysqli_fetch_assoc($q)) $rows[] = $r;

function xe($v) {
    return htmlspecialchars((string)$v, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

function cellStr($v, $bold = false, $bg = '', $color = '') {
    $style = 'font-family:Arial;font-size:11pt;';
    if ($bold)   $style .= 'font-weight:bold;';
    if ($bg)     $style .= "background:$bg;";
    if ($color)  $style .= "color:$color;";
    return '<Cell><Data ss:Type="String" ss:styleID=""><![CDATA[' . $v . ']]></Data></Cell>';
}

function cellNum($v) {
    return '<Cell><Data ss:Type="Number">' . xe((float)$v) . '</Data></Cell>';
}


$xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$xml .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
$xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
$xml .= '  xmlns:o="urn:schemas-microsoft-com:office:office"' . "\n";
$xml .= '  xmlns:x="urn:schemas-microsoft-com:office:excel"' . "\n";
$xml .= '  xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
$xml .= '  xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";

$xml .= '<Styles>

  <Style ss:ID="Default">
    <Font ss:FontName="Arial" ss:Size="11"/>
  </Style>

  <Style ss:ID="sTitle">
    <Font ss:FontName="Arial" ss:Size="14" ss:Bold="1" ss:Color="#f4600c"/>
  </Style>

  <Style ss:ID="sSub">
    <Font ss:FontName="Arial" ss:Size="10" ss:Color="#71717a"/>
  </Style>

  <Style ss:ID="sHeader">
    <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
    <Font ss:FontName="Arial" ss:Size="11" ss:Bold="1" ss:Color="#FFFFFF"/>
    <Interior ss:Color="#f4600c" ss:Pattern="Solid"/>
    <Borders>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e4e4e7"/>
      <Border ss:Position="Right"  ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e4e4e7"/>
    </Borders>
  </Style>

  <Style ss:ID="sData">
    <Alignment ss:Vertical="Center"/>
    <Font ss:FontName="Arial" ss:Size="11"/>
    <Borders>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#f0f0f0"/>
      <Border ss:Position="Right"  ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#f0f0f0"/>
    </Borders>
  </Style>

  <Style ss:ID="sDataEven">
    <Alignment ss:Vertical="Center"/>
    <Font ss:FontName="Arial" ss:Size="11"/>
    <Interior ss:Color="#fafafa" ss:Pattern="Solid"/>
    <Borders>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#f0f0f0"/>
      <Border ss:Position="Right"  ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#f0f0f0"/>
    </Borders>
  </Style>

  <Style ss:ID="sNum">
    <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
    <Font ss:FontName="Arial" ss:Size="11"/>
    <NumberFormat ss:Format="#,##0"/>
    <Borders>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#f0f0f0"/>
      <Border ss:Position="Right"  ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#f0f0f0"/>
    </Borders>
  </Style>

  <Style ss:ID="sNumEven">
    <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
    <Font ss:FontName="Arial" ss:Size="11"/>
    <NumberFormat ss:Format="#,##0"/>
    <Interior ss:Color="#fafafa" ss:Pattern="Solid"/>
    <Borders>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#f0f0f0"/>
      <Border ss:Position="Right"  ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#f0f0f0"/>
    </Borders>
  </Style>

  <Style ss:ID="sGreen">
    <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
    <Font ss:FontName="Arial" ss:Size="11" ss:Bold="1" ss:Color="#16a34a"/>
    <NumberFormat ss:Format="#,##0"/>
    <Borders>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#f0f0f0"/>
    </Borders>
  </Style>

  <Style ss:ID="sRed">
    <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
    <Font ss:FontName="Arial" ss:Size="11" ss:Color="#dc2626"/>
    <NumberFormat ss:Format="#,##0"/>
    <Borders>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#f0f0f0"/>
    </Borders>
  </Style>

  <Style ss:ID="sTotal">
    <Alignment ss:Vertical="Center"/>
    <Font ss:FontName="Arial" ss:Size="11" ss:Bold="1"/>
    <Interior ss:Color="#fff4ee" ss:Pattern="Solid"/>
    <Borders>
      <Border ss:Position="Top"    ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#f4600c"/>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#f4600c"/>
    </Borders>
  </Style>

  <Style ss:ID="sTotalNum">
    <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
    <Font ss:FontName="Arial" ss:Size="11" ss:Bold="1"/>
    <NumberFormat ss:Format="#,##0"/>
    <Interior ss:Color="#fff4ee" ss:Pattern="Solid"/>
    <Borders>
      <Border ss:Position="Top"    ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#f4600c"/>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#f4600c"/>
    </Borders>
  </Style>

  <Style ss:ID="sTotalGreen">
    <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
    <Font ss:FontName="Arial" ss:Size="11" ss:Bold="1" ss:Color="#16a34a"/>
    <NumberFormat ss:Format="#,##0"/>
    <Interior ss:Color="#fff4ee" ss:Pattern="Solid"/>
    <Borders>
      <Border ss:Position="Top"    ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#f4600c"/>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#f4600c"/>
    </Borders>
  </Style>

  <Style ss:ID="sTotalRed">
    <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
    <Font ss:FontName="Arial" ss:Size="11" ss:Bold="1" ss:Color="#dc2626"/>
    <NumberFormat ss:Format="#,##0"/>
    <Interior ss:Color="#fff4ee" ss:Pattern="Solid"/>
    <Borders>
      <Border ss:Position="Top"    ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#f4600c"/>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#f4600c"/>
    </Borders>
  </Style>

</Styles>' . "\n";

$sheetName = xe($filter_tipe === 'bulanan' ? "Bulanan $filter_tahun" : "Tahunan");
$xml .= "<Worksheet ss:Name=\"$sheetName\">\n<Table>\n";

if ($filter_tipe === 'bulanan') {
    $colWidths = [40, 110, 60, 90, 70, 80, 80, 140, 130, 130, 90];
} else {
    $colWidths = [40, 70, 100, 70, 80, 140, 130, 130, 90];
}
foreach ($colWidths as $w) {
    $xml .= "<Column ss:Width=\"$w\"/>\n";
}

$xml .= '<Row ss:Height="28">
  <Cell ss:StyleID="sTitle"><Data ss:Type="String">' . xe($judul) . '</Data></Cell>
</Row>' . "\n";

$xml .= '<Row ss:Height="18">
  <Cell ss:StyleID="sSub"><Data ss:Type="String">Anuwani Network · Dicetak: ' . xe(date('d F Y, H:i')) . ' WIB · Oleh: ' . xe($_SESSION['nama'] ?? 'Administrator') . '</Data></Cell>
</Row>' . "\n";

$xml .= '<Row ss:Height="10"/>' . "\n";

$tot_transaksi = array_sum(array_column($rows, 'total_transaksi'));
$tot_tagihan   = array_sum(array_column($rows, 'total_tagihan'));
$tot_masuk     = array_sum(array_column($rows, 'pendapatan'));
$tot_belum     = array_sum(array_column($rows, 'belum_bayar'));
$tot_lunas     = array_sum(array_column($rows, 'jml_lunas'));
$tot_blm       = array_sum(array_column($rows, 'jml_belum'));

$xml .= '<Row ss:Height="18">
  <Cell ss:StyleID="sSub"><Data ss:Type="String">RINGKASAN:</Data></Cell>
  <Cell ss:StyleID="sSub"><Data ss:Type="String">Total Transaksi: ' . xe($tot_transaksi) . ' | Pendapatan Masuk: Rp ' . xe(number_format($tot_masuk,0,',','.')) . ' | Tunggakan: Rp ' . xe(number_format($tot_belum,0,',','.')) . '</Data></Cell>
</Row>' . "\n";

$xml .= '<Row ss:Height="10"/>' . "\n";

$xml .= '<Row ss:Height="32">' . "\n";
if ($filter_tipe === 'bulanan') {
    $headers = ['No','Bulan','Tahun','Total Transaksi','Lunas','Belum Bayar',
                'Menunggu','Pendapatan Masuk','Tunggakan','Total Tagihan','Realisasi (%)'];
} else {
    $headers = ['No','Tahun','Total Transaksi','Lunas','Belum Bayar',
                'Pendapatan Masuk','Tunggakan','Total Tagihan','Realisasi (%)'];
}
foreach ($headers as $h) {
    $xml .= '  <Cell ss:StyleID="sHeader"><Data ss:Type="String">' . xe($h) . '</Data></Cell>' . "\n";
}
$xml .= '</Row>' . "\n";

$no = 1;
foreach ($rows as $idx => $r) {
    $even  = ($idx % 2 === 1);
    $sD    = $even ? 'sDataEven' : 'sData';
    $sN    = $even ? 'sNumEven'  : 'sNum';
    $pct   = $r['total_tagihan'] > 0
             ? round($r['pendapatan'] / $r['total_tagihan'] * 100, 1) . '%'
             : '0%';

    $xml .= '<Row ss:Height="20">' . "\n";
    $xml .= "  <Cell ss:StyleID=\"$sD\"><Data ss:Type=\"Number\">$no</Data></Cell>\n";

    if ($filter_tipe === 'bulanan') {
        $bln = xe($nama_bulan[(int)$r['bulan_tagihan']]);
        $xml .= "  <Cell ss:StyleID=\"$sD\"><Data ss:Type=\"String\">$bln</Data></Cell>\n";
        $xml .= "  <Cell ss:StyleID=\"$sN\"><Data ss:Type=\"Number\">{$r['tahun_tagihan']}</Data></Cell>\n";
    } else {
        $xml .= "  <Cell ss:StyleID=\"$sN\"><Data ss:Type=\"Number\">{$r['tahun_tagihan']}</Data></Cell>\n";
    }

    $xml .= "  <Cell ss:StyleID=\"$sN\"><Data ss:Type=\"Number\">{$r['total_transaksi']}</Data></Cell>\n";
    $xml .= "  <Cell ss:StyleID=\"$sN\"><Data ss:Type=\"Number\">{$r['jml_lunas']}</Data></Cell>\n";
    $xml .= "  <Cell ss:StyleID=\"$sN\"><Data ss:Type=\"Number\">{$r['jml_belum']}</Data></Cell>\n";

    if ($filter_tipe === 'bulanan') {
        $xml .= "  <Cell ss:StyleID=\"$sN\"><Data ss:Type=\"Number\">{$r['jml_menunggu']}</Data></Cell>\n";
    }

    $xml .= "  <Cell ss:StyleID=\"sGreen\"><Data ss:Type=\"Number\">{$r['pendapatan']}</Data></Cell>\n";
    $xml .= "  <Cell ss:StyleID=\"sRed\"><Data ss:Type=\"Number\">{$r['belum_bayar']}</Data></Cell>\n";
    $xml .= "  <Cell ss:StyleID=\"$sN\"><Data ss:Type=\"Number\">{$r['total_tagihan']}</Data></Cell>\n";
    $xml .= "  <Cell ss:StyleID=\"$sD\"><Data ss:Type=\"String\">$pct</Data></Cell>\n";
    $xml .= '</Row>' . "\n";
    $no++;
}

$xml .= '<Row ss:Height="22">' . "\n";
$xml .= '  <Cell ss:StyleID="sTotal"><Data ss:Type="String"></Data></Cell>' . "\n";
$xml .= '  <Cell ss:StyleID="sTotal"><Data ss:Type="String">TOTAL</Data></Cell>' . "\n";

if ($filter_tipe === 'bulanan') {
    $xml .= '  <Cell ss:StyleID="sTotal"><Data ss:Type="String"></Data></Cell>' . "\n"; 
}

$xml .= '  <Cell ss:StyleID="sTotalNum"><Data ss:Type="Number">' . xe($tot_transaksi) . '</Data></Cell>' . "\n";
$xml .= '  <Cell ss:StyleID="sTotalNum"><Data ss:Type="Number">' . xe($tot_lunas) . '</Data></Cell>' . "\n";
$xml .= '  <Cell ss:StyleID="sTotalNum"><Data ss:Type="Number">' . xe($tot_blm) . '</Data></Cell>' . "\n";

if ($filter_tipe === 'bulanan') {
    $tot_menunggu = array_sum(array_column($rows, 'jml_menunggu'));
    $xml .= '  <Cell ss:StyleID="sTotalNum"><Data ss:Type="Number">' . xe($tot_menunggu) . '</Data></Cell>' . "\n";
}

$xml .= '  <Cell ss:StyleID="sTotalGreen"><Data ss:Type="Number">' . xe($tot_masuk) . '</Data></Cell>' . "\n";
$xml .= '  <Cell ss:StyleID="sTotalRed"><Data ss:Type="Number">'   . xe($tot_belum) . '</Data></Cell>' . "\n";
$xml .= '  <Cell ss:StyleID="sTotalNum"><Data ss:Type="Number">'   . xe($tot_tagihan) . '</Data></Cell>' . "\n";
$xml .= '  <Cell ss:StyleID="sTotal"><Data ss:Type="String">—</Data></Cell>' . "\n";
$xml .= '</Row>' . "\n";

$xml .= "</Table>\n</Worksheet>\n</Workbook>";

header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '_' . date('Ymd') . '.xls"');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
echo "\xEF\xBB\xBF"; 
echo $xml;
exit;