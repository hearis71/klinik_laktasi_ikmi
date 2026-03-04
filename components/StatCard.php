<?php
/**
 * StatCard Component
 * Klinik Laktasi - Statistics Card
 */

if (!defined('KLINIK_LAKTASI')) {
    die('Direct access not allowed');
}

$title = $title ?? 'Title';
$value = $value ?? '0';
$color = $color ?? 'blue';
$icon = $icon ?? null;

$colorClasses = [
    'blue' => 'blue',
    'teal' => 'teal',
    'purple' => 'purple',
    'indigo' => 'indigo',
];

$colorClass = $colorClasses[$color] ?? 'blue';
?>

<div class="stat-card <?php echo $colorClass; ?>">
    <div class="stat-content">
        <h3 class="stat-title"><?php echo htmlspecialchars($title); ?></h3>
        <p class="stat-value"><?php echo htmlspecialchars($value); ?></p>
    </div>
    <?php if ($icon): ?>
        <span class="stat-icon"><?php echo $icon; ?></span>
    <?php endif; ?>
</div>
