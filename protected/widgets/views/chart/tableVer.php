<?php
$this->tableData = [
    'vertical' => $vertical,
];
?>

<div class="table-responsive">
    <table class="table table-striped table-hover table-macro">
        <tbody>
        <?php
        foreach ($vertical as $row) {
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