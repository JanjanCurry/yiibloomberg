<?php
$this->tableData = [
    'cols' => $cols,
    'rows' => $rows,
];
?>

<div class="table-responsive table-responsive-freeze">
    <div class="table-responsive-inner">
        <table class="table table-striped table-hover table-macro">
            <thead>
            <tr>
                <?php
                foreach ($cols as $i => $cell) {
                    echo $cell;
                }
                ?>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($rows as $row) {
                echo '<tr>';
                foreach ($row as $cell) {
                    echo $cell;
                }
                echo '</tr>';
            }
            ?>
            </tbody>
        </table>
    </div>
</div>