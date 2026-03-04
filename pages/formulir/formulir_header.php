
<?php
/**
 * Formulir Header Component
 * Displays mother and baby data header for assessment forms
 * 
 * Usage: Include this file with $registrasi variable set
 */

if (!isset($registrasi)) {
    return;
}
?>

<div class="header-formulir">
    <div class="header-section">
        <h3>Data Ibu</h3>
        <table class="header-table">
            <tbody>
                <tr>
                    <td width="150">No. Registrasi</td>
                    <td width="10">:</td>
                    <td><?php echo htmlspecialchars($no_registrasi ?? '-'); ?></td>
                </tr>
                <tr>
                    <td>Nama Ibu</td>
                    <td>:</td>
                    <td><?php echo htmlspecialchars($registrasi['nama_ibu'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <td>Tanggal Lahir</td>
                    <td>:</td>
                    <td><?php echo formatDateIndonesian($registrasi['tanggal_lahir_ibu'] ?? ''); ?></td>
                </tr>
                <tr>
                    <td>Usia</td>
                    <td>:</td>
                    <td><?php echo htmlspecialchars($registrasi['usia_ibu'] ?? '-'); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="header-section">
        <h3>Data Bayi</h3>
        <table class="header-table">
            <tbody>
                <tr>
                    <td width="150">Nama Bayi</td>
                    <td width="10">:</td>
                    <td><?php echo htmlspecialchars($registrasi['nama_bayi'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <td>Tanggal Lahir</td>
                    <td>:</td>
                    <td><?php echo formatDateIndonesian($registrasi['tanggal_lahir_bayi'] ?? ''); ?></td>
                </tr>
                <tr>
                    <td>Usia</td>
                    <td>:</td>
                    <td><?php echo htmlspecialchars($registrasi['usia_bayi'] ?? '-'); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


