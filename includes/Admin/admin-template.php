<?php
$depth_one_parents = [];
$depth_two_parents = [];
?>
<div class="wrap">
    <div class="body-dark">
        <?php require_once __DIR__ . '/template/header.php'; ?>
        <main>
            <div class="easydocs-sidebar-menu">
                <div class="tab-container">
                    <?php require_once __DIR__ . '/template/parent-docs.php'; ?>
                    <div class="easydocs-tab-content">
						<?php require_once __DIR__ . '/template/child-docs.php'; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>