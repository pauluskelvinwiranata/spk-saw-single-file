<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Metode SAW - PHP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container py-4">

    <h2 class="mb-4">Perhitungan SAW (Simple Additive Weighting)</h2>

    <?php
   // ============================
   // Data Kriteria
   // ============================
    $kriteria = [
        ['kode' => 'C1', 'nama' => 'Penghasilan Orang Tua', 'atribut' => 'cost', 'bobot' => 25],
        ['kode' => 'C2', 'nama' => 'Semester',              'atribut' => 'benefit', 'bobot' => 20],
        ['kode' => 'C3', 'nama' => 'Tanggungan Orang Tua',  'atribut' => 'benefit', 'bobot' => 15],
        ['kode' => 'C4', 'nama' => 'Saudara Kandung',       'atribut' => 'benefit', 'bobot' => 10],
        ['kode' => 'C5', 'nama' => 'Nilai',                 'atribut' => 'benefit', 'bobot' => 30],
    ];

    // ============================
    // Data alternatif (nilai)
    // ============================
    $alternatif = [
        'A1' => [80, 20, 20, 20, 20],
        'A2' => [40, 40, 40, 40, 40],
        'A3' => [60, 60, 60, 60, 100],
        'A4' => [80, 80, 80, 80, 80],
        'A5' => [40, 20, 40, 60, 100],
    ];
    ?>

    <!-- Tabel Kriteria -->
    <h4>Data Kriteria</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Kriteria</th>
                <th>Atribut</th>
                <th>Bobot (%)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($kriteria as $k): ?>
                <tr>
                    <td><?php echo $k['kode'] ?></td>
                    <td><?php echo $k['nama'] ?></td>
                    <td><?php echo $k['atribut'] ?></td>
                    <td><?php echo $k['bobot'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Tabel Alternatif -->
    <h4>Data Alternatif</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Alternatif</th>
                <?php foreach ($kriteria as $k): ?>
                    <th><?php echo $k['kode'] ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($alternatif as $a => $nilai): ?>
                <tr>
                    <td><?php echo $a ?></td>
                    <?php foreach ($nilai as $v): ?>
                        <td><?php echo $v ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php
    // ============================
    // Normalisasi
    // ============================
    $normalisasi = [];
    foreach ($kriteria as $i => $k) {
        $kolom = array_column($alternatif, $i);
        $max = max($kolom);
        $min = min($kolom);
        foreach ($alternatif as $a => $nilai) {
            if (!isset($normalisasi[$a])) $normalisasi[$a] = [];
            if ($k['atribut'] === 'benefit') {
                $normalisasi[$a][$i] = $nilai[$i] / $max;
            } else {
                $normalisasi[$a][$i] = $min / $nilai[$i];
            }
        }
    }
    ?>

    <!-- Tabel Normalisasi -->
    <h4>Hasil Normalisasi</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Alternatif</th>
                <?php foreach ($kriteria as $k): ?>
                    <th><?php echo $k['kode'] ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($normalisasi as $a => $nilai): ?>
                <tr>
                    <td><?php echo $a ?></td>
                    <?php foreach ($nilai as $v): ?>
                        <td><?php echo round($v, 4) ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php
    // ============================
    // Hitung skor akhir
    // ============================
    $bobot = array_column($kriteria, 'bobot');
    $total_bobot = array_sum($bobot);
    $bobot_normal = array_map(fn($b) => $b / $total_bobot, $bobot);
    $hasil = [];
    foreach ($normalisasi as $a => $nilai) {
        $hasil[$a] = array_sum(array_map(fn($v, $b) => $v * $b, $nilai, $bobot_normal));
    }
    arsort($hasil);
    ?>

    <!-- Tabel Hasil Akhir -->
    <h4>Hasil Perangkingan</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Alternatif</th>
                <th>Skor SAW</th>
                <th>Peringkat</th>
            </tr>
        </thead>
        <tbody>
            <?php $rank = 1; ?>
            <?php foreach ($hasil as $a => $skor): ?>
                <tr>
                    <td><?php echo $a ?></td>
                    <td><?php echo round($skor, 4) ?></td>
                    <td><?php echo $rank++ ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>

</html>