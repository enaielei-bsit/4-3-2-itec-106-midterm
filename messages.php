<?php
    require_once(__DIR__ . "/utils.php");

    $messages ??= [];
?>

<?php
    foreach($messages as $message) {
        $type = $message["type"] ?? "positive";
        $title = $message["title"] ?? "";
        $subtitle = $message["subtitle"] ?? "";
        $subtitles = $message["subtitles"] ?? [];
        $subtitles = !is_array($subtitles) ? [$subtitles] : $subtitles;

        if(empty($title) && empty($subtitles)) continue;
?>
    <div class="<?= stitch('message', $type) ?>">
        <?php if(!empty($title)) { ?>
            <strong><?= $title ?></strong>
        <?php } ?>
        <?php if(!empty($subtitle)) { ?>
            <span><?= $subtitle ?></span>
        <?php } ?>
        <?php if(!empty($subtitles)) { ?>
            <ul>
                <?php foreach($subtitles as $subtitle) { ?>
                    <li><?= $subtitle ?></li>
                <?php } ?>
            </ul>
        <?php } ?>
    </div>
<?php } ?>